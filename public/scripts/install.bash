#!/usr/bin/env bash
set -euo pipefail

### CONFIG DEFAULTS ###
DEFAULT_INSTALL_DIR="/var/www/stockbase"
DEFAULT_DB="stockbase"
DEFAULT_SYSTEM="StockBase"
DEFAULT_URL="stockbase.local"
PHP_VERSION="8.4"
REPO_URL="https://your-repo-url.git" # change if needed

PHP_MODULES=("curl" "fpm" "gd" "igbinary" "imagick" "imap" "intl" "ldap" "mbstring" "mysql" "readline" "redis" "soap" "xml" "xsl" "zip")

### Ensure running as root ###
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root (sudo)." 
   exit 1
fi

### Prompt the Ts and Cs
echo ""
echo "StockBase Copyright (C) 2025 Andrew Richardson"
echo "This program comes with ABSOLUTELY NO WARRANTY; for details type 'show w'."
echo "This is free software, and you are welcome to redistribute it"
echo "under certain conditions; type 'show c' for details."
echo ""
echo "To continue, type 'Y'"
echo ""
while true; do
    # Ask for folder input and allow creating the folder if needed
    read -p "Input: " tandc

    case $tandc in
        "show w")
            echo "15. Disclaimer of Warranty."
            echo "THERE IS NO WARRANTY FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. "
            echo "EXCEPT WHEN OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM “AS IS” "
            echo "WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, "
            echo "THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. "
            echo "THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU. "
            echo "SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING, REPAIR OR CORRECTION."
            echo ""
            ;;
        "show c")
            echo "16. Limitation of Liability."
            echo "IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY COPYRIGHT HOLDER, "
            echo "OR ANY OTHER PARTY WHO MODIFIES AND/OR CONVEYS THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, "
            echo "INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO "
            echo "USE THE PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED "
            echo "BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR "
            echo "OTHER PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES."
            echo ""
            ;;
        "Y" | "y")
            break
            ;;
        *)
            echo "Unknown input."
            ;;
    esac
done


### Update system ###
echo "Updating System..."
echo ""
apt-get update -y
echo ""
echo "Compelte!"

sleep 1

### Step 1. Web server ###
if ! command -v apache2 >/dev/null && ! command -v nginx >/dev/null; then
    read -p "No web server detected. Install Apache (a) or Nginx (n)? [a]: " choice
    choice=${choice:-a}
    if [[ "$choice" == "n" ]]; then
        apt-get install -y nginx
        SERVER="nginx"
    else
        apt-get install -y apache2
        SERVER="apache2"
    fi
else
    if command -v apache2 >/dev/null; then SERVER="apache2"; else SERVER="nginx"; fi
fi

### Step 2. PHP 8.4 + modules ###
if ! php -v 2>/dev/null | grep -q "$PHP_VERSION"; then
    echo "Installing PHP $PHP_VERSION..."
    add-apt-repository -y ppa:ondrej/php
    apt-get update -y
    apt-get install -y php$PHP_VERSION php$PHP_VERSION-cli php$PHP_VERSION-common
fi

for module in "${PHP_MODULES[@]}"; do
    apt-get install -y "php$PHP_VERSION-$module" || true
done

### Step 3. Composer ###
if ! command -v composer >/dev/null; then
    echo "Installing Composer..."
    EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_SIGNATURE="$(php -r "echo hash_file('SHA384', 'composer-setup.php');")"
    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then
        >&2 echo 'ERROR: Invalid installer signature'
        rm composer-setup.php
        exit 1
    fi
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    rm composer-setup.php
fi

apt install npm

### Step 4. Database server ###
if ! command -v mysql >/dev/null; then
    echo "Installing MySQL..."
    DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server
    systemctl enable mysql --now
fi

echo "Enter MySQL root password (or press enter to create new): "
read -s DB_ROOT_PASS
if [[ -z "$DB_ROOT_PASS" ]]; then
    DB_ROOT_PASS=$(openssl rand -base64 12)
    echo "Generated MySQL root password: $DB_ROOT_PASS"
    mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_ROOT_PASS'; FLUSH PRIVILEGES;"
fi

### Step 5. Project directory ###
read -p "Install directory [$DEFAULT_INSTALL_DIR]: " INSTALL_DIR
INSTALL_DIR=${INSTALL_DIR:-$DEFAULT_INSTALL_DIR}

mkdir -p "$INSTALL_DIR"

# Copy repo contents instead of moving
echo "Copying project files to $INSTALL_DIR..."
rsync -a ./ "$INSTALL_DIR/" --exclude "vendor" --exclude ".git"

cd "$INSTALL_DIR"

### Step 6. Laravel setup ###
cp -n .env.example .env
composer install --no-interaction --prefer-dist --optimize-autoloader
php artisan key:generate

### Step 7. Database setup ###
read -p "Database name [$DEFAULT_DB]: " DB_NAME
DB_NAME=${DB_NAME:-$DEFAULT_DB}

read -p "Database user: " DB_USER
read -s -p "Database password: " DB_PASS
echo ""

mysql -uroot -p"$DB_ROOT_PASS" -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
mysql -uroot -p"$DB_ROOT_PASS" -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -uroot -p"$DB_ROOT_PASS" -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost'; FLUSH PRIVILEGES;"

sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

php artisan migrate --force
php artisan db:seed --force || true

### Step 8. App config ###
read -p "System name [$DEFAULT_SYSTEM]: " SYSNAME
SYSNAME=${SYSNAME:-$DEFAULT_SYSTEM}

read -p "Default URL (FQDN) [$DEFAULT_URL]: " FQDN
FQDN=${FQDN:-$DEFAULT_URL}

read -p "Enable SSL? (y/n): " SSL_CHOICE

if [[ "$SSL_CHOICE" == "y" ]]; then
    read -p "SSL cert file: " SSL_CERT
    read -p "SSL key file: " SSL_KEY
    sed -i "s|APP_URL=.*|APP_URL=https://$FQDN|" .env
else
    sed -i "s|APP_URL=.*|APP_URL=http://$FQDN|" .env
fi

### Step 9. Web server config ###
if [[ "$SERVER" == "apache2" ]]; then
    CONF="/etc/apache2/sites-available/stockbase.conf"
    cat > $CONF <<EOF
<VirtualHost *:80>
    ServerName $FQDN
    DocumentRoot $INSTALL_DIR/public
    <Directory $INSTALL_DIR/public>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/stockbase-error.log
    CustomLog \${APACHE_LOG_DIR}/stockbase-access.log combined
</VirtualHost>
EOF

    if [[ "$SSL_CHOICE" == "y" ]]; then
        cat >> $CONF <<EOF

<VirtualHost *:443>
    ServerName $FQDN
    DocumentRoot $INSTALL_DIR/public
    <Directory $INSTALL_DIR/public>
        AllowOverride All
        Require all granted
    </Directory>
    SSLEngine on
    SSLCertificateFile $SSL_CERT
    SSLCertificateKeyFile $SSL_KEY
    ErrorLog \${APACHE_LOG_DIR}/stockbase-ssl-error.log
    CustomLog \${APACHE_LOG_DIR}/stockbase-ssl-access.log combined
</VirtualHost>
EOF
        a2enmod ssl
    fi

    a2ensite stockbase.conf
    a2dissite 000-default.conf || true
    systemctl reload apache2

else
    CONF="/etc/nginx/sites-available/stockbase.conf"
    cat > $CONF <<EOF
server {
    listen 80;
    server_name $FQDN;
    root $INSTALL_DIR/public;
    index index.php index.html;
    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

    if [[ "$SSL_CHOICE" == "y" ]]; then
        cat >> $CONF <<EOF

server {
    listen 443 ssl;
    server_name $FQDN;
    root $INSTALL_DIR/public;
    index index.php index.html;
    ssl_certificate $SSL_CERT;
    ssl_certificate_key $SSL_KEY;
    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php$PHP_VERSION-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF
    fi

    ln -sf "$CONF" /etc/nginx/sites-enabled/stockbase.conf
    nginx -t && systemctl reload nginx
fi

### Step 10. Permissions ###
chown -R www-data:www-data "$INSTALL_DIR"

### Step 11. Output ###
ROOT_PASS=$(openssl rand -base64 12)
HASHED=$(php -r "echo password_hash('$ROOT_PASS', PASSWORD_BCRYPT);")
mysql -u$DB_USER -p$DB_PASS $DB_NAME -e "UPDATE users SET password='$HASHED', password_expired=1 WHERE id=1;"


# Clean up original repo (optional)
echo ""
echo "Setup complete!"
read -p "Delete original repository files? (y/n): " CLEANUP
if [[ "$CLEANUP" == "y" ]]; then
    echo "Cleaning up source files..."
    rm -rf "$(dirname "$0")/../.."/*
fi


echo ""
echo "✅ Installation complete!"
echo "System: $SYSNAME"
echo "URL: ${SSL_CHOICE:+https://}$FQDN"
echo "Root user password (save this!): $ROOT_PASS"
