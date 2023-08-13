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
            sudo apt install -y "$package_name" php8.1-calendar php8.1-common php8.1-ctype php8.1-mysqli php8.1-curl php8.1-dom php8.1-exif php8.1-ffi php8.1-fileinfo php8.1-filter php8.1-ftp php8.1-gd php8.1-gettext php8.1-hash php8.1-iconv php8.1-igbinary php8.1-imagick php8.1-imap php8.1-intl php8.1-json php8.1-ldap php8.1-libxml php8.1-mbstring php8.1-mysqli php8.1-mysqlnd php8.1-openssl php8.1-pcntl php8.1-pcre php8.1-pdo php8.1-pdo_mysql php8.1-phar php8.1-posix php8.1-readline php8.1-redis php8.1-reflection php8.1-session php8.1-shmop php8.1-simplexml php8.1-soap php8.1-sockets php8.1-sodium php8.1-spl php8.1-sysvmsg php8.1-sysvsem php8.1-sysvshm php8.1-tokenizer php8.1-xml php8.1-xmlreader php8.1-xmlrpc php8.1-xmlwriter php8.1-xsl php8.1-zip php8.1-zlib >/dev/null 2>&1 &
        else 
            sudo apt-get update
            sudo apt-get install -y "$package_name" >/dev/null 2>&1 &
        fi
        echo "$package_name installed!"
    elif [ "$package_name" = "php8.1" ]; then
        sudo apt update
        sudo apt install -y "$package_name" php8.1-calendar php8.1-common php8.1-ctype php8.1-mysqli php8.1-curl php8.1-dom php8.1-exif php8.1-ffi php8.1-fileinfo php8.1-filter php8.1-ftp php8.1-gd php8.1-gettext php8.1-hash php8.1-iconv php8.1-igbinary php8.1-imagick php8.1-imap php8.1-intl php8.1-json php8.1-ldap php8.1-libxml php8.1-mbstring php8.1-mysqli php8.1-mysqlnd php8.1-openssl php8.1-pcntl php8.1-pcre php8.1-pdo php8.1-pdo_mysql php8.1-phar php8.1-posix php8.1-readline php8.1-redis php8.1-reflection php8.1-session php8.1-shmop php8.1-simplexml php8.1-soap php8.1-sockets php8.1-sodium php8.1-spl php8.1-sysvmsg php8.1-sysvsem php8.1-sysvshm php8.1-tokenizer php8.1-xml php8.1-xmlreader php8.1-xmlrpc php8.1-xmlwriter php8.1-xsl php8.1-zip php8.1-zlib >/dev/null 2>&1 &
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

# Check and install necessary packages
check_install_package php8.1
check_install_package mysql-server


# Check if MySQL is installed
if ! dpkg -l | grep -q "mysql-server"; then
    echo "MySQL is not installed. Installing now..."
    sudo apt-get update
    sudo apt-get install -y mysql-server >/dev/null 2>&1 &
    echo "MySQL installed!"
    sudo mysql_secure_installation
fi

# Ask for folder input and check if it exists
while true; do
    # Ask for folder input and allow creating the folder if needed
    read -p "Enter the folder name to install to (e.g. /var/www/html/inventory): " folder_name

    # Check if the folder exists
    if [ -d "$folder_name" ]; then
        echo "Moving files from $root_path to $folder_name..."
        #cp "$0" "$folder_name"  # Copy the script itself
        mv "$root_path"/* "$folder_name"/   # Move all files except the script
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
                echo "Moving files to $folder_name..."
                #cp "$0" "$folder_name"  # Copy the script itself
                mv "$root_path"/* "$folder_name"/   # Move all files except the script
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

if [ "$use_ssl" = true ]; then
    echo "SSL is enabled."
    echo "SSL Certificate File: $ssl_certificate"
    echo "SSL Key File: $ssl_key"
else
    echo "SSL is not enabled."
fi
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

# Check and install necessary packages
check_install_package "$web_server"

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
    sudo mysql_secure_installation
fi

echo ""
# Verify MySQL root password
while true; do
    read -s -p "Enter the MySQL root password: " mysql_root_password
    echo
    if mysql -u root -p"$mysql_root_password" -e ";" 2>/dev/null; then
        echo ""
        break
    else
        echo "Incorrect password. Please try again."
    fi
done

# Check if "inventory" database exists
if mysql -u root -p"$mysql_root_password" -e "USE inventory;" 2>/dev/null; then
    read -p "The 'inventory' database already exists. Do you want to remove it and install the new one? (yes/no): " remove_database
    if [ "$remove_database" = "yes" ]; then
        echo "Removing existing 'inventory' database..."
        mysql -u root -p"$mysql_root_password" -e "DROP DATABASE inventory;"
        echo "Database removed."
    else
        echo "Database needs to be created to continue."
        echo "Please remove the database named 'inventory' to continue..."
        read -n 1 -s -r -p "Press any key to exit..."
        exit 1
    fi
else
    echo "The 'inventory' database does not exist."
fi

echo ""
# Run the mysql_setup.sql script
sql_setup_script="$folder_name/assets/sql/db_setup.sql"
if [ -f "$sql_setup_script" ]; then
    echo "Running MySQL setup script from assets..."
    mysql -u root -p"$mysql_root_password" < "$sql_setup_script"
    echo "MySQL setup script executed."
else
    echo "MySQL setup script not found at $sql_setup_script."
fi
echo ""

# Create "inventory" user and set password
echo "User needed to access the database."

# Check if 'inventory' user exists
echo "Checking if inventory user exists..."
user_exists=$(mysql -u root -p"$mysql_root_password" -e "SELECT User FROM mysql.user WHERE User='inventory' AND Host='localhost';" --skip-column-names)

if [ -n "$user_exists" ]; then
    # The 'inventory' user exists, prompt the user for action
    echo "Inventory user already exists. Do you want to drop the user and re-create it?"
    echo "Selecting 'N' will prompt for the password."
    while true; do
        read -p "Do you want to drop the user? (Y/N): " drop_user
        case "$drop_user" in
            [Yy]* ) mysql -u root -p"$mysql_root_password" -e "DROP USER 'inventory'@'localhost';"
                    mysql -u root -p"$mysql_root_password" -e "flush privileges;"
                    echo "User 'inventory' dropped."
                    break;;
            [Nn]* ) 
                    while true; do
                        read -p "Enter the password for 'inventory' user: " inventory_user_password;
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
    if [ "$drop_user" = [Yy]* ]; then
        while true; do
            read -s -p "Enter a password for the 'inventory' user: " inventory_user_password
            echo
            read -s -p "Confirm the password for the 'inventory' user: " inventory_user_password_confirm
            echo
            if [ "$inventory_user_password" = "$inventory_user_password_confirm" ]; then
                    echo "Creating 'inventory' user..."
                    mysql -u root -p"$mysql_root_password" -e "CREATE USER 'inventory'@'localhost' IDENTIFIED BY '$inventory_user_password';"
                    mysql -u root -p"$mysql_root_password" -e "GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';"
                    mysql -u root -p"$mysql_root_password" -e "FLUSH PRIVILEGES;"
                    echo "User 'inventory' created."
                    correct_password="Y"
                break
            else
                echo "Passwords do not match. Please try again."
            fi
        done
    else
        if mysql -u inventory -p"$inventory_user_password" -e ";" 2>/dev/null; then
            echo "Password matches."
            correct_password="Y"
            break
        else
            echo "Incorrect password."
        fi
    fi
else 
    while true; do
        read -s -p "Enter a password for the 'inventory' user: " inventory_user_password
        echo
        read -s -p "Confirm the password for the 'inventory' user: " inventory_user_password_confirm
        echo
        if [ "$inventory_user_password" = "$inventory_user_password_confirm" ]; then
                echo "Creating 'inventory' user..."
                mysql -u root -p"$mysql_root_password" -e "CREATE USER 'inventory'@'localhost' IDENTIFIED BY '$inventory_user_password';"
                mysql -u root -p"$mysql_root_password" -e "GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';"
                mysql -u root -p"$mysql_root_password" -e "FLUSH PRIVILEGES;"
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
    sed -i "s/\$dBPassword = 'admin';/\$dBUsername = 'inventory';/" "$dbh"
    sed -i "s/\$dBPassword = 'admin';/\$dBPassword = '$inventory_user_password';/" "$dbh"
    echo "done!"
else
    echo "Password issue for inventory user..."
fi
echo ""

# Generate and hash password
echo "Generating new password..."
generate_password
echo "Hashing new password..."
hash_password

# get system hostname
hostname=$(hostname --fqdn)

echo "Creating root user for site login..."
# Insert new user to table
mysql -u root -p"$mysql_root_password" -e \
    "ALTER TABLE inventory.users AUTO_INCREMENT = 0;"
mysql -u root -p"$mysql_root_password" -e \
    "INSERT INTO inventory.users (id, username, first_name, last_name, email, auth, role_id, enabled, password_expired, password) \
    VALUES (0, 'root', 'root', 'root', 'root@$hostname', 'local', 0, 1, 1, '$hashed_password');"

echo "Done!"
echo ""

# Display the web URL for accessing the system
protocol="http"
if [ "$use_ssl" = true ]; then
    protocol="https"
fi
echo "============================================================================="
echo ""
echo "=   You can access the system at: $protocol://$web_domain"
echo "=   Login with username: root, password: $gen_password"
echo ""
echo "============================================================================="
echo ""