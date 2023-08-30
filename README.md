# Inventory

This project contains the code for the Inventory web package, including the setup scripts and basic database configuration.

The purpose of this project is for stock tracking and locating.

## Dependent Packages
- Linux 
- Apache2 / Nginx (user choice, or whichever is installed)
- PHP 8.1 (v8.1.22)
    - php8.1-calendar, php8.1-common, php8.1-ctype, php8.1-ldap, php8.1-mysqli, php8.1-curl, php8.1-dom, php8.1-exif, php8.1-ffi, php8.1-fileinfo, php8.1-filter, php8.1-ftp, php8.1-gd, php8.1-gettext, php8.1-hash, php8.1-iconv, php8.1-igbinary, php8.1-imagick, php8.1-imap, php8.1-intl, php8.1-json, php8.1-ldap, php8.1-libxml, php8.1-mbstring, php8.1-mysqli, php8.1-mysqlnd, php8.1-openssl, php8.1-pcntl, php8.1-pcre, php8.1-pdo, php8.1-pdo_mysql, php8.1-phar, php8.1-posix, php8.1-readline, php8.1-redis, php8.1-reflection, php8.1-session, php8.1-shmop, php8.1-simplexml, php8.1-soap, php8.1-sockets, php8.1-sodium, php8.1-spl, php8.1-sysvmsg, php8.1-sysvsem, php8.1-sysvshm, php8.1-tokenizer, php8.1-xml, php8.1-xmlreader, php8.1-xmlrpc, php8.1-xmlwriter, php8.1-xsl, php8.1-zip, php8.1-zlib
- MySQL Server (v8.0.34) (or similar DB using mysql syntax)
- PHPMailer (v6.8.0) (Packaged at includes/PHPMailer)

*These packages are all installed as part of the install script at* `assets/scripts/install.bash`*.*

## Installation
### Automated Deployment
For automated deployment, run the below command to clone the repository and run the install script:

`git clone http://git.ajrich.co.uk/web/inventory.git && ./inventory/assets/scripts/install.bash`

This will run the setup for the system and provide a username and password to login with.

Login to your site to continue with any further setup

<details>
<summary><h3>Manual Deployment</h3></summary>
For manual deployment, it requires all packages to be installed manually and the database to be configured and setup correctly.

Clone the repo first, and the follow the below steps.
`git clone http://git.ajrich.co.uk/web/inventory.git`

1. Update your packages and install them if you are confident they are okay to be updated

    `sudo apt update`

    `sudo apt upgrade`

2. Install PHP 8.1 and all dependencies required

    a. Install the PHP repository

    ```
    sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y
    sudo add-apt-repository ppa:ondrej/php
    sudo apt update 
    ```

    b. Install the package and dependencies
    ```
    sudo apt install -y php8.2 php8.1-calendar php8.1-common php8.1-ctype php8.1-ldap php8.1-mysqli php8.1-curl php8.1-dom php8.1-exif php8.1-ffi php8.1-fileinfo php8.1-filter php8.1-ftp php8.1-gd php8.1-gettext php8.1-hash php8.1-iconv php8.1-igbinary php8.1-imagick php8.1-imap php8.1-intl php8.1-json php8.1-ldap php8.1-libxml php8.1-mbstring php8.1-mysqli php8.1-mysqlnd php8.1-openssl php8.1-pcntl php8.1-pcre php8.1-pdo php8.1-pdo_mysql php8.1-phar php8.1-posix php8.1-readline php8.1-redis php8.1-reflection php8.1-session php8.1-shmop php8.1-simplexml php8.1-soap php8.1-sockets php8.1-sodium php8.1-spl php8.1-sysvmsg php8.1-sysvsem php8.1-sysvshm php8.1-tokenizer php8.1-xml php8.1-xmlreader php8.1-xmlrpc php8.1-xmlwriter php8.1-xsl php8.1-zip php8.1-zlib
    ```

3. Install MySQL Server and run first setup

    ``` 
    sudo apt install mysql-server 

    sudo mysql_secure_installation
    ```
    *Make sure to set a root password and not leave it blank.*

4. Install your preferred web server (apache2 and nginx are both supported here, but this can be adapted)

    ```
    sudo apt install apache2
    ```
    or
    ```
    sudo apt install nginx
    ```

5. Setup Database

    - Confirm there is no database named 'inventory'

        ```
        mysql -u root -p

        USE inventory;
        quit;
        ```

        If mysql throws an error, the database doesnt exist. This is what we want. 
        If it does exist, it will be overwritten.
    
    - Run the MySQL DB setup
        *we will navigate to the downloaded git repo*

        ```
        cd inventory

        mysql -u root < assets/sql/db_setup.sql
        ```

    - Run the extras script to fill in the required tables with the information they need.
        *This script creates the required fields for the config and config_default tables, also setting the auto-increment values*

        ```
        mysql -u root < assets/sql/db_extras.sql
        ```
    
    - Create a user for the database to verify against

        We will first check if a user exists under the name 'inventory'.

        ```
        mysql -u root -p
        
        SELECT User, Host FROM mysql.user WHERE User='inventory' AND Host='localhost';
        ```

        If no rows are returned, we will add a new user. 
        If there are rows, we will either need to know the current password, or drop the user.

        Select the relevant option:

        <details>
        <summary><h5>No user found, create new</h5></summary>

        - Create the new user, replacing `[SECRET PASSWORD]` with your password

        ```
        CREATE USER 'inventory'@'localhost' IDENTIFIED BY '[SECRET PASSWORD]';
        GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';
        FLUSH PRIVILEGES;
        quit;
        ```

        </details>

        <details>
        <summary><h5>User exists and password known</h5></summary>

        - Grant the user permissions.

        ```
        GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';
        FLUSH PRIVILEGES;
        quit;
        ```

        </details>

        <details>
        <summary><h5>User exists and needs to be dropped</h5></summary>

        - Drop the user

        ```
        DROP USER 'inventory'@'localhost';
        FLUSH PRIVILEGES;
        quit;
        ```

        - Create the new user, replacing `[SECRET PASSWORD]` with your password

        ```
        CREATE USER 'inventory'@'localhost' IDENTIFIED BY '[SECRET PASSWORD]';
        GRANT ALL PRIVILEGES ON inventory.* TO 'inventory'@'localhost';
        FLUSH PRIVILEGES;
        quit;
        ```

        </details>

    - Confirm you can login and access the database

        ```
        mysql -u inventory -p
        
        USE inventory;
        SELECT * FROM config_default;
        ```

        If you get data returned from this and no errors it all worked as expected.
    
    - Update the database connection php file with your new credentials

        Edit `includes/dbh.inc.php` and change the below to your new info:

        ```
        $dBUsername = 'admin';
        $dBPassword = 'admin';
        ```

        e.g.

        ```
        $dBUsername = 'inventory';
        $dBPassword = 'SecretSpecialPassword';
        ```

    - Create and update the root user password for your initial user
        Select a password for your initial root user.
        This will be prompted to be changed once you first log in.

        Generate your hashed password with the below, replacing `[SECRET PASSWORD]` with your password:

        ```
        php -r "echo password_hash('[SECRET PASSWORD]', PASSWORD_DEFAULT); echo\"\n\";"
        ```

        Run the below to add your first user, replacing `[PASSWORD HASH]` with your hashed password from above:

        ```
        mysql -u inventory -p

        INSERT INTO inventory.users (id, username, first_name, last_name, email, auth, role_id, enabled, password_expired, password) 
            VALUES (1, 'root', 'root', 'root', 'root@$hostname', 'local', 0, 1, 1, '[PASSWORD HASH]]');
        UPDATE inventory.users SET id=0 where id=1;
        ALTER TABLE inventory.users AUTO_INCREMENT = 1;
        ```

6. Decide on your web URL

    We need a base URL for the site to be located at (e.g. inventory.domain.com)

    Update the config with this url, replacing `[WEB DOMAIN]` with your domain name/url:

    ```
    mysql -u inventory -p

    UPDATE config SET base_url='[WEB DOMAIN]' WHERE id=1;
    quit;
    ```

7. Move your files to your web server/desired location


8. Web config setup

    We first need to decide whether we will use SSL for this. 

    <details>
    <summary><h5>No SSL</h5></summary>
    Make a note of your file locations

    <details>
    <summary>Apache</summary>
    - Run the below to create the config, replacing `[DOMAIN NAME]` and `[LOCATION]` with your domain name and fodler location

        ```
        web_domain='[DOMAIN NAME]'
        folder_name='[LOCATION]'

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
        ```
    - Enable the site

        ```
        sudo a2ensite $web_domain.conf
        sudo systemctl reload apache2
        ```

    </details>

    <details>
    <summary>Nginx</summary>
    - Run the below to create the config, replacing `[DOMAIN NAME]` and `[LOCATION]` with your domain name and fodler location

        ```
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
        ```

    - Enable the site

        ```
        sudo ln -s /etc/nginx/sites-available/$web_domain /etc/nginx/sites-enabled/
        sudo systemctl reload nginx
        ```
        
    </details>

    </details>

    <details>
    <summary><h5>Using SSL</h5></summary>
    <em>This assumes you have an SSL certificate and will not cover LetsEncrypt but it can be used for your cert if needed.</em>

    Make a note of your SSL key and certificate file locations

    <details>
    <summary>Apache</summary>
    - Run the below, replacing `[DOMAIN NAME]`, `[LOCATION]`, `[SSL KEY]` and `[SSL CERT]` with your domain name, folder location, ssl key location and ssl cert location.

        ```
        web_domain='[DOMAIN NAME]'
        folder_name='[LOCATION]'
        ssl_certificate='[SSL CERT]'
        ssl_key='[SSL KEY]'

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
        ```

    - Enable to appropriate modules and enable the site

    ```
    sudo enable_ssl_apache
    sudo a2enmod ssl
    sudo a2enmod rewrite
    sudo systemctl restart apache2
    sudo a2ensite $web_domain.conf
    sudo systemctl reload apache2
    ```

    </details>

    <details>
    <summary>Nginx</summary>
    - Run the below, replacing `[DOMAIN NAME]`, `[LOCATION]`, `[SSL KEY]` and `[SSL CERT]` with your domain name, folder location, ssl key location and ssl cert location.

        ```
        web_domain='[DOMAIN NAME]'
        folder_name='[LOCATION]'
        ssl_certificate='[SSL CERT]'
        ssl_key='[SSL KEY]'

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
        ```

    - Add a symlink for this file

    ```
    sudo enable_ssl_nginx
    sudo ln -s /etc/nginx/sites-available/$web_domain /etc/nginx/sites-enabled/
    sudo systemctl reload nginx
    ```

    </details>
    </details>

9. Login to your site to continue with any further setup
    Login to your newly setup site by connecting to the domain name in your browser

    e.g. https://inventory.domain.local/

    You will need to select "local" as your login type if the local toggle is shown on the login page
    LDAP will be enabled by default with a config in place, which will not work on your system.

    Login with the username 'root' and password created in step 5 (NOT the hashed password).

    You will be prompted to make your first Site / Area / Shelf for the system, so please add one. (these can be changed later)

    Head to the 'Admin' page from the navigation bar and configure your setup.

</details>





