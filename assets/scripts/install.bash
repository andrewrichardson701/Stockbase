#!/bin/bash

script_dir="$( cd "$( dirname "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"
assets_dir="$( cd "$( dirname "$script_dir" )" &> /dev/null && pwd )"
root_path="$( cd "$( dirname "$assets_dir" )" &> /dev/null && pwd )"

# Function to check and install necessary packages
check_install_package() {
    local package_name="$1"
    if ! dpkg -l | grep -q "$package_name"; then
        echo "Installing $package_name..."
        if [ "$package_name" = "php8.1" ]; then
            sudo apt update
            sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y
            sudo add-apt-repository ppa:ondrej/php
            sudo apt update
            sudo apt install -y "$package_name" php8.1-calendar php8.1-common php8.1-ctype php8.1-ldap php8.1-mysqli php8.1-curl php8.1-dom php8.1-exif php8.1-ffi php8.1-fileinfo php8.1-filter php8.1-ftp php8.1-gd php8.1-gettext php8.1-hash php8.1-iconv php8.1-igbinary php8.1-imagick php8.1-imap php8.1-intl php8.1-json php8.1-ldap php8.1-libxml php8.1-mbstring php8.1-mysqli php8.1-mysqlnd php8.1-openssl php8.1-pcntl php8.1-pcre php8.1-pdo php8.1-pdo_mysql php8.1-phar php8.1-posix php8.1-readline php8.1-redis php8.1-reflection php8.1-session php8.1-shmop php8.1-simplexml php8.1-soap php8.1-sockets php8.1-sodium php8.1-spl php8.1-sysvmsg php8.1-sysvsem php8.1-sysvshm php8.1-tokenizer php8.1-xml php8.1-xmlreader php8.1-xmlrpc php8.1-xmlwriter php8.1-xsl php8.1-zip php8.1-zlib >/dev/null 2>&1 &
        else 
            sudo apt-get update
            sudo apt-get install -y "$package_name" >/dev/null 2>&1 &
        fi
        echo "$package_name installed!"
    elif [ "$package_name" = "php8.1" ]; then
        sudo apt update
        sudo apt install -y "$package_name" php8.1-calendar php8.1-common php8.1-ctype php8.1-ldap php8.1-mysqli php8.1-curl php8.1-dom php8.1-exif php8.1-ffi php8.1-fileinfo php8.1-filter php8.1-ftp php8.1-gd php8.1-gettext php8.1-hash php8.1-iconv php8.1-igbinary php8.1-imagick php8.1-imap php8.1-intl php8.1-json php8.1-ldap php8.1-libxml php8.1-mbstring php8.1-mysqli php8.1-mysqlnd php8.1-openssl php8.1-pcntl php8.1-pcre php8.1-pdo php8.1-pdo_mysql php8.1-phar php8.1-posix php8.1-readline php8.1-redis php8.1-reflection php8.1-session php8.1-shmop php8.1-simplexml php8.1-soap php8.1-sockets php8.1-sodium php8.1-spl php8.1-sysvmsg php8.1-sysvsem php8.1-sysvshm php8.1-tokenizer php8.1-xml php8.1-xmlreader php8.1-xmlrpc php8.1-xmlwriter php8.1-xsl php8.1-zip php8.1-zlib >/dev/null 2>&1 &
    else
        echo "$package_name is already installed."
    fi
}

# Function to enable SSL and configure redirection for Apache
enable_ssl_apache() {
    sudo a2enmod ssl
    sudo a2enmod rewrite
    sudo systemctl restart apache2

    cat > /etc/apache2/sites-available/$web_domain.conf <<EOL
<VirtualHost *:80>
    ServerName $web_domain
    DocumentRoot $folder_name

    <Directory $folder_name>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    Redirect permanent / https://$web_domain/
</VirtualHost>

<VirtualHost *:443>
    ServerName $web_domain
    DocumentRoot $folder_name

    <Directory $folder_name>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile $ssl_certificate
    SSLCertificateKeyFile $ssl_key

    Redirect permanent / https://$web_domain/
</VirtualHost>
EOL

    sudo a2ensite $web_domain.conf
    sudo systemctl reload apache2
}

# Function to enable SSL and configure redirection for Nginx
enable_ssl_nginx() {
    sudo ln -s /etc/nginx/sites-available/$web_domain /etc/nginx/sites-enabled/
    sudo systemctl reload nginx

    cat > /etc/nginx/sites-available/$web_domain <<EOL
server {
    listen 80;
    server_name $web_domain;

    root $folder_name;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    return 301 https://$web_domain$request_uri;
}

server {
    listen 443 ssl;
    server_name $web_domain;

    root $folder_name;
    index index.php index.html;

    ssl_certificate $ssl_certificate;
    ssl_certificate_key $ssl_key;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
EOL

    sudo systemctl reload nginx
}

# Function to generate a random password
generate_password() {
    gen_password=$(openssl rand -base64 12)
}

# Hash password using PASSWORD_DEFAULT
hash_password() {
    hashed_password=$(php -r "echo password_hash('$gen_password', PASSWORD_DEFAULT);")
}

echo ""
echo "StockBase Copyright (C) 2023 Andrew Richardson"
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

# Check and install necessary packages
echo ""
echo "Checking installed packages..."
echo ""
echo "Checking php8.1..."
check_install_package php8.1
sleep 1
echo ""
echo "Checking mysql-server..."
check_install_package mysql-server
echo ""
echo "Done!"
echo ""
sleep 1

# Check if MySQL is installed
if ! dpkg -l | grep -q "mysql-server"; then
    echo "MySQL is not installed. Installing now..."
    sudo apt-get update
    sudo apt-get install -y mysql-server >/dev/null 2>&1 &
    echo "MySQL installed!"
    sleep 1
    sudo mysql_secure_installation
fi
slep 1
echo ""
# Ask for folder input and check if it exists
while true; do
    # Ask for folder input and allow creating the folder if needed
    read -p "Enter the folder name to install to (e.g. /var/www/html/inventory): " folder_name

    # Check if the folder exists
    if [ -d "$folder_name" ]; then
        echo "Moving files from $root_path to $folder_name..."
        #cp "$0" "$folder_name"  # Copy the script itself
        mv "$root_path"/* "$folder_name"/   # Move all files except the script
        sleep 1
        echo "Files moved successfully to $folder_name."
        break
    else
        # Get the parent directory
        parent_dir="$(dirname "$folder_name")"
        # Check if the parent directory exists
        if [ -d "$parent_dir" ]; then
            read -p "The folder doesn't exist. Do you want to create it? (yes/no): " create_folder
            if [ "$create_folder" = "yes" ]; then
                mkdir -p "$folder_name"
                echo "Folder created."
                sleep 1
                echo "Moving files to $folder_name..."
                #cp "$0" "$folder_name"  # Copy the script itself
                mv "$root_path"/* "$folder_name"/   # Move all files except the script
                sleep 1
                echo "Files moved successfully to $folder_name."
                break
            else
                echo "Folder not created. Exiting."
                exit 1
            fi
        else
            echo "The path leading up to the folder does not exist."
        fi
    fi
done
echo ""

# Ask for FQDN
read -p "Enter the Fully Qualified Domain Name (FQDN) to access the site: " web_domain

echo ""
echo "Web Domain: $web_domain"
sleep 1
echo ""

# Ask if SSL should be used
while true; do
    read -p "Do you want to use SSL for secure connections? (Y/N): " use_ssl
    case "$use_ssl" in
        [Yy]* ) use_ssl=true;
            read -p "Enter the path to the SSL certificate file: " ssl_cert_file;
            read -p "Enter the path to the SSL key file: " ssl_key_file;
            break;;
        [Nn]* ) use_ssl=false; break;;
        * ) echo "Please answer Y or N.";;
    esac
done

protocol="http"
if [ "$use_ssl" = true ]; then
    protocol="https"
    echo "SSL is enabled."
    echo "SSL Certificate File: $ssl_certificate"
    echo "SSL Key File: $ssl_key"
else
    echo "SSL is not enabled."
fi
sleep 1
echo ""

# Check if Apache2 or Nginx is already installed
if dpkg -l | grep -q "apache2"; then
    web_server="apache2"
elif dpkg -l | grep -q "nginx"; then
    web_server="nginx"
else
    options=("apache2" "nginx")
    PS3="Select the web server to use: "
    select web_server in "${options[@]}"; do
        if [ -n "$web_server" ]; then
            break
        else
            echo "Invalid choice. Please select a valid option."
        fi
    done
fi

echo "Using $web_server as the web server."
sleep 1
echo ""
echo "Checking for $web_server package..."

# Check and install necessary packages
check_install_package "$web_server"

echo "Done!"
sleep 1
echo ""

# Create a web server configuration file with or without SSL
if [ "$web_server" = "apache2" ]; then
    if [ "$use_ssl" = true ]; then
        enable_ssl_apache
    else
        # Create Apache Virtual Host configuration without SSL
        echo "Creating Apache Virtual Host configuration..."
        cat > /etc/apache2/sites-available/$web_domain.conf <<EOL
<VirtualHost *:80>
    ServerName $web_domain
    DocumentRoot $folder_name

    <Directory $folder_name>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOL

        sudo a2ensite $web_domain.conf
        sudo systemctl reload apache2

        echo "Apache Virtual Host configuration created and enabled."
    fi
elif [ "$web_server" = "nginx" ]; then
    if [ "$use_ssl" = true ]; then
        enable_ssl_nginx
    else
        # Create Nginx server block configuration without SSL
        echo "Creating Nginx server block configuration..."
        cat > /etc/nginx/sites-available/$web_domain <<EOL
server {
    listen 80;
    server_name $web_domain;

    root $folder_name;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
EOL

        sudo ln -s /etc/nginx/sites-available/$web_domain /etc/nginx/sites-enabled/
        sudo systemctl reload nginx

        echo "Nginx server block configuration created and enabled."
    fi
fi

# Check if MySQL setup has been completed before
if ! mysql -u root -e ";" 2>/dev/null; then
    echo "Running MySQL secure installation..."
    sleep 1
    sudo mysql_secure_installation
fi

sleep 1
echo ""
# Verify MySQL root password
while true; do
    read -s -p "Enter the MySQL root password: " mysql_root_password
    MYSQL_PWD=mysql_root_password
    echo
    if mysql -u root -e ";" 2>/dev/null; then
        echo ""
        break
    else
        echo "Incorrect password. Please try again."
    fi
done

echo "Checking for 'inventory' database..."
echo ""
# Check if "inventory" database exists
if mysql -u root -e "USE inventory;" 2>/dev/null; then
    read -p "The 'inventory' database already exists. Do you want to remove it and install the new one? (yes/no): " remove_database
    if [ "$remove_database" = "yes" ]; then
        echo "Removing existing 'inventory' database..."
        mysql -u root -e "DROP DATABASE inventory;"
        sleep 1
        echo "Database removed."
    else
        echo "Database needs to be created to continue."
        echo "Please remove the database named 'inventory' to continue..."
        read -n 1 -s -r -p "Press any key to exit..."
        exit 1
    fi
else
    echo "The 'inventory' database does not exist."
    echo "Database will be created."
fi

sleep 1
echo ""
# Run the mysql_setup.sql script
sql_setup_script="$folder_name/assets/sql/db_setup.sql"
sql_extras_script="$folder_name/assets/sql/db_extras.sql"
if [ -f "$sql_setup_script" ]; then
    echo "Running MySQL setup script from assets..."
    mysql -u root < "$sql_setup_script"
    sleep 5
    echo "MySQL setup script executed."
    if [ -f "$sql_extras_script" ]; then
        echo "Running MySQL setup extras script from assets..."
        mysql -u root < "$sql_extras_script"
        sleep 5
        echo "MySQL setup extras script executed."
    else
        echo "MySQL setup extras script not found at $sql_extras_script."
    fi
    echo ""
    echo "Updating base_url with the selected web url..."
    mysql -u root -e "UPDATE inventory.config SET base_url='$web_domain' WHERE id=1;";
    sleep 1
    echo "Done!"
    echo ""

    while true; do    
        echo "Checking base_url is set..."
        # Query to get the base_url value from the config table
        config_base_url=$(mysql -u root -e "SELECT base_url FROM inventory.config WHERE id=1;")
        sleep 1

        # Check if base_url is equal to the desired web_domain
        if [ "$config_base_url" = "$web_domain" ]; then
            echo "base_url is set correctly: $config_base_url."
            break  # Exit the loop if the condition is true
        else
            echo "base_url is not set correctly: $config_base_url."
            echo "Retrying..."
            mysql -u root -p -e "UPDATE inventory.config SET base_url='$web_domain' WHERE id=1;"
            echo "Done!"
        fi

        # Add a delay before retrying (to avoid rapid and unnecessary retries)
        sleep 5
    done
else
    echo "MySQL setup script not found at $sql_setup_script."
fi
sleep 1
echo ""

# Create "inventory" user and set password
echo "User needed to access the database."

# Check if 'inventory' user exists
echo "Checking if inventory user exists..."
echo ""
user_exists=$(mysql -u root -e "SELECT User FROM mysql.user WHERE User='inventory' AND Host='localhost';" --skip-column-names)
sleep 1
if [ -n "$user_exists" ]; then
    # The 'inventory' user exists, prompt the user for action
    echo "Inventory user already exists. Do you want to drop the user and re-create it?"
    echo "Selecting 'N' will prompt for the password."
    while true; do
        read -p "Do you want to drop the user? (Y/N): " drop_user
        case "$drop_user" in
            [Yy]* ) 
                    mysql -u root -e "DROP USER 'inventory'@'localhost';"
                    mysql -u root -e "flush privileges;"
                    echo "User 'inventory' dropped."
                    echo ""
                    while true; do
                        read -s -p "Enter a password for the 'inventory' user: " inventory_user_password
                        echo
                        read -s -p "Confirm the password for the 'inventory' user: " inventory_user_password_confirm
                        echo
                        if [ "$inventory_user_password" = "$inventory_user_password_confirm" ]; then
                                echo "Creating 'inventory' user..."
                                mysql -u root -e "CREATE USER 'inventory'@'localhost' IDENTIFIED BY '$inventory_user_password';"
                                mysql -u root -e "GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';"
                                mysql -u root -e "FLUSH PRIVILEGES;"
                                echo "User 'inventory' created."
                                correct_password="Y"
                            break
                        else
                            echo "Passwords do not match. Please try again."
                        fi
                    done
                    break;;
            [Nn]* ) 
                    echo ""
                    while true; do
                        read -s -p "Enter the password for 'inventory' user: " inventory_user_password;
                        if mysql -u inventory -p"$inventory_user_password" -e ";" 2>/dev/null; then
                            echo "Password matches."
                            correct_password="Y"
                            break
                        else
                            echo "Incorrect password. Please try again."
                        fi
                    done
                    break;;
            * ) echo "Please answer Y or N.";;
        esac
    done
else 
    echo ""
    while true; do
        read -s -p "Enter a password for the 'inventory' user: " inventory_user_password
        echo
        read -s -p "Confirm the password for the 'inventory' user: " inventory_user_password_confirm
        echo
        if [ "$inventory_user_password" = "$inventory_user_password_confirm" ]; then
                echo "Creating 'inventory' user..."
                mysql -u root -e "CREATE USER 'inventory'@'localhost' IDENTIFIED BY '$inventory_user_password';"
                mysql -u root -e "GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';"
                mysql -u root -e "FLUSH PRIVILEGES;"
                echo "User 'inventory' created."
                correct_password="Y"
            break
        else
            echo "Passwords do not match. Please try again."
        fi
    done
fi

if [ "$correct_password" = "Y" ]; then
    dbh="$folder_name/includes/dbh.inc.php"
    echo "Updating $dbh with new password..."

    # Search and replace the string in the file
    sed -i "s/\$dBUsername = 'admin';/\$dBUsername = 'inventory';/" "$dbh"
    sed -i "s/\$dBPassword = 'admin';/\$dBPassword = '$inventory_user_password';/" "$dbh"
    echo "done!"
else
    echo "Password issue for inventory user..."
fi
sleep 1
echo ""

# Generate and hash password
echo "Generating new root user password..."
generate_password
sleep 0.5
echo "Hashing new root user password..."
hash_password
sleep 0.5

# get system hostname
hostname=$(hostname --fqdn)

echo "Creating root user for site login..."
# Insert new user to table
mysql -u root -e "INSERT INTO inventory.users (id, username, first_name, last_name, email, auth, role_id, enabled, password_expired, password) VALUES (1, 'root', 'root', 'root', 'root@$hostname', 'local', 0, 1, 1, '$hashed_password');"
mysql -u root -e "UPDATE inventory.users SET id=0 where id=1;"
mysql -u root -e "ALTER TABLE inventory.users AUTO_INCREMENT = 1;"   
sleep 1
echo "Done!"
echo ""

while true; do
    echo "Do you want to enable LDAP windows authentication?"
    echo "You will be requried to enter all of the details here, however you will still be able to login as root"
    echo "You can test the LDAP config on the admin settings page at $protocol://$web_domain/admin.php#ldap-settings"
    read -p "Enable LDAP? (Y/N): " ldap_enabled
    case "$ldap_enabled" in
        [Yy]* ) 
            ldap_enabled=1
            while true; do
                echo
                read -p "LDAP authentication username: " ldap_username
                echo
                read -s -p "LDAP authentication password: " ldap_password
                echo
                read -p "LDAP domain: " ldap_domain
                echo
                read -p "LDAP host: " ldap_host
                echo
                read -p "Secondary LDAP host (this can be blank): " ldap_host_secondary
                echo
                read -p "LDAP port: " ldap_port
                echo
                read -p "LDAP Base DN: " ldap_basedn
                echo
                read -p "LDAP User Group: " ldap_usergroup
                echo
                read -p "LDAP User Filter: " ldap_userfilter
                echo    
                    ldap_password_hash=$(echo -n "$ldap_password" | base64)
                    echo "Pushing settings to config..."
                    mysql -u root -e "UPDATE inventory.config SET ldap_enabled=$ldap_enabled, ldap_username='$ldap_username', ldap_password='$ldap_password_hash', ldap_domain='$ldap_domain', ldap_host='$ldap_host', ldap_host_secondary='$ldap_host_secondary', ldap_port=$ldap_port, ldap_basedn='$ldap_basedn', ldap_usergroup='$ldap_usergroup', ldap_userfilter='$ldap_userfilter' WHERE id=1;"
                    echo "Config Saved."
                    correct_password="Y"
                break
            done
            break;;
        [Nn]* ) 
            ldap_enabled=0
            mysql -u root -e "UPDATE inventory.config SET ldap_enabled=$ldap_enabled WHERE id=1;"
            echo "LDAP disabled."
            break;;
        * ) echo "Please answer Y or N.";;
    esac
done
sleep 0.5
echo ""

while true; do
    echo "Input SMTP email details now?"
    echo "You will be requried to enter all of the details here."
    echo "You can test/change the SMTP config on the admin settings page at $protocol://$web_domain/admin.php#smtp-settings"
    read -p "Configure SMTP now? (Y/N): " smtp_config
    case "$smtp_config" in
        [Yy]* ) 
            while true; do
                echo
                read -p "SMTP Host (e.g. mail.domain.com): " smtp_host
                echo
                read -p "SMTP Port: " smtp_port
                echo
                echo "Select SMTP encryption."
                echo " 1. STARTTLS"
                echo " 2. TLS"
                echo " 3. SSL"
                while true; do
                    
                    read -p "SMTP Encryption: " smtp_encryption_value
                    case "$smtp_encryption_value" in
                        [1]* ) 
                            smtp_encryption="starttls"
                            break;;
                        [2]* ) 
                            smtp_encryption="tls"
                            break;;
                        [3]* ) 
                            smtp_encryption="ssl"
                            break;;
                        * ) echo "Please select from either 1, 2 or 3.";;
                    esac
                done
                read -p "SMTP Username: " smtp_username
                echo
                read -s -p "SMTP Password: " smtp_password
                echo
                read -p "SMTP From Email: " smtp_from_email
                echo
                read -p "SMTP From Name: " smtp_from_name
                echo
                read -p "SMTP To Email (Backup): " smtp_to_email
                echo
                    smtp_password_hash=$(echo -n "$smtp_password" | base64)
                    echo "Pushing settings to config..."
                    mysql -u root -e "UPDATE inventory.config SET smtp_host='$smtp_host', smtp_port=$smtp_port, smtp_encryption='$smtp_encryption', smtp_username='$smtp_username', smtp_password='$smtp_password_hash', smtp_from_email='$smtp_from_email', smtp_from_name='$smtp_from_name', smtp_to_email='$smtp_to_email' WHERE id=1;"
                    echo "Config Saved."
                break
            done
            break;;
        [Nn]* ) 
            echo "SMTP not configured."
            break;;
        * ) echo "Please answer Y or N.";;
    esac
done
echo ""
echo "Setting permissions..."
chmod 777 $folder_name -R
sleep 0.5
echo "Done!"
echo ""

# Display the web URL for accessing the system

echo "============================================================================="
echo ""
echo "=   You can access the system at: $protocol://$web_domain"
echo "=   Login with username: root, password: $gen_password"
echo ""
echo "============================================================================="
echo ""