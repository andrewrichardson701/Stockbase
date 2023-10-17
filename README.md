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
- Bootstrap (v4.5.2) (included in headers)
- Jquery (v3.5.1) (included in headers)
- Font Awesome (v6.4.0) (included in headers)
- Google Font - Poppins (included in headers)

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
    sudo apt install -y php8.1 php8.1-calendar php8.1-common php8.1-ctype php8.1-ldap php8.1-mysqli php8.1-curl php8.1-dom php8.1-exif php8.1-ffi php8.1-fileinfo php8.1-filter php8.1-ftp php8.1-gd php8.1-gettext php8.1-hash php8.1-iconv php8.1-igbinary php8.1-imagick php8.1-imap php8.1-intl php8.1-json php8.1-ldap php8.1-libxml php8.1-mbstring php8.1-mysqli php8.1-mysqlnd php8.1-openssl php8.1-pcntl php8.1-pcre php8.1-pdo php8.1-pdo_mysql php8.1-phar php8.1-posix php8.1-readline php8.1-redis php8.1-reflection php8.1-session php8.1-shmop php8.1-simplexml php8.1-soap php8.1-sockets php8.1-sodium php8.1-spl php8.1-sysvmsg php8.1-sysvsem php8.1-sysvshm php8.1-tokenizer php8.1-xml php8.1-xmlreader php8.1-xmlrpc php8.1-xmlwriter php8.1-xsl php8.1-zip php8.1-zlib php-curl
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

    *Make sure you are already in the downloaded repo folder*

    Replace `new/folder/location/` to the folder you want your server hosted from (e.g. /var/www/html/inventory/) including the trailing /

    ```
    sudo cp -a . /new/folder/location/
    ```

    Set the permissions for your new folder location

    ```
    sudo chown -R www-data:www-data /new/folder/location/
    sudo chmod go-rwx /new/folder/location/
    ```

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

<details>
<summary><h2>Change Log</h2></summary>
<details>
<summary><h3>0.3.1-beta</h3></summary>
<h4>Beta release 0.3.1, Script updates, swipe card login.</h4>

- Transaction include page styling corrected under pagination form
- Swipe card login now working. Testing pending once card reader is obtained.
- Card login page is now complete and working. Test buttons in place for passes until pass reader in place.
- Users with no theme saved can now login. Fixed the SQL query to make a LEFT JOIN for theme.
- DB install extras updated in db_extras.sql.
- Fulldump run and saved.
- Adjustments made to various pages based on installation bash script.
- Edit images button added back in to the stock edit page.
- Login page is now working for the card reader, still needs a full test but now doesnt try to login when pressing any button.
- MySQL scripts updated to add the needed info to the DB.
- Bash script updated with some more prompts and fixed the first prompt with a case instead of else if.
- Admin global settings is now a more cleaned up table.
- Transactions now support cable_transaction table.
- Transaction include page now supports cable_transaction page.
- Updated cable_transaction table to now include the shelf_id. SQL queries updated.
- Added error checking from urls to the pages where they are needed and adjusted the error query strings to be more useful.

</details>
<details>
<summary><h3>0.3.0-beta</h3></summary>
<h4>Beta release 0.3.0, Adjustments for mobile width and card reader tech.</h4>

- Mobile CSS in progress
- Some HTML elements are hidden/shown based on width.
- Admin page is not visible from mobile form factor unless the url is appended.
- New CSS added for mobile form factor.
- Nav now loads properl on mobile.
- Footer now loads differently on mobile.
- Index page now works on mobile. Less columns show to reduce clutter
- Cablestock page now works on mobile.
- Stock (view) page now works on mobile.
- Stock (add) page now works on mobile.
- Stock (remove) page now works on mobile.
- Stock (move) page now works on mobile.
- Stock (edit) page now works on mobile.
- Transactions inc now working on mobile, with page numbers becoming a select field.
- Index page pagination row is now longer being sorted with the rest of the table.
- Swipe card prompt now shows up on mobile form factor.
- Swipe card fields added to users table.
- Swipe cards can now be added on the profile page.
- Swipe cards can be re-assigned on the profile page.
- login-card.inc.php added to handle card logins.
- Swipe card assigning and re-assigning is handled in admin.inc.php.
- Swipe card de-assigning is handled in admin.inc.php.
- Bootstrap 4.5.2 CSS added in assets/css folder for redundancy.
- Email example added to Email Notification Settings section of admin page via AJAX.
- Some modification to the smtp.inc.php email template to allow it to be embedded in php page.

</details>
<details>
<summary><h3>0.2.1-beta</h3></summary>
<h4>Beta release 0.2.1, based on initial feedback.</h4>

- Added more themes. Theme CSS now has more properties which can be adjusted.
- Changelog page has been formatted better and now fills the page.
- Email notifications can now be disabled from the admin page. Each notification type is configurable, excpet them important things, like password resets.
- SMTP send_mail function has been updated to check if the notification type is enabled. All send_mail requests have been updated accordingly.
- Default theme selection now added to Global Settings of admin page
- Themes are now in their own table in the database and theme selection is now auto generated on pages.
- Theme test page added ad theme-test.php. This shows all relevant pages which are affected by the theme. The css can be edited, applied, downloaded and uploaded from here for new themes.
- About page added, accessible from the footer copyright.
- The http-headers.php is now merged into head.php. These were both being called at the same time so seemed pointless being split.
- Name and branding changes to StockBase. This might not be the final name.
- Footer can now be disabled from the $showFoot variable in foot.php - this will likely hidden on final release.
- Emails now have useful content in them. It used to be just numbers but now it gives relevant info.
- Mysql dumps updated.

</details>
<details>
<summary><h3>0.2.0-beta</h3></summary>
<h4>Beta release 0.2.0, based on initial feedback.</h4>

- Corrected the url redirects when a user tries to reach a page without being logged in. Logging in now redirects to the correct page.
- Removed the title and welcome message from the index page and cablestock pages.
- Moved the title into the Nav bar and linked it to the index page.
- Corrected the issue with the offset being negative when no items are found on the index sql query. Negative numbers now default to 0.
- Back button removed from the nav. This was creating loops where you couldnt actually go back.
- Changed the icon in the clear button to be the fa-ban icon and rotated it 90degrees
- Changed the serial number so it can now be copied but this may be going later down the line
- Stock page now allows you to edit individual rows in the item table. This allows the adding of new serial numbers which were missed.
- Images can be permenantly deleted from the admin page
- Cable stock now relates to shelves rather than just sites. This is now added correctly too.
- Stock page now hides irrelevant info for cables.
- Less important info is now under the "more info" section on the stock page.
- The "show 0 stock" button now ONLY shows 0 stock rows, now all rows.
- Corrected the cablestock searching and formatting.
- Dynamic searching is now in and working. Ajax based searching which updates on input.
- Can now search with more criteria on the home page.
- Images are now larger on the home page. This is copied throughout.
- Cablestock page now allows you to go to the stock properies page by clicking the cable name. This is the same as normal stock items, with less important info removed. 
- Can now change the image for cablestock with the above change.
- Label and Manufacturer are now select boxes rather than input because this makes more sense.
- Added deleted field to tables (item, stock, shelf, area etc) so that things can be tracked.
- Deleting stock when the stock count is 0, no longer deletes the row from the database and instead marks deleted as 1.
- Minimum stock count now checks against the site using the shelf of the object that the stock was removed from for the email notifications.
- Added a light theme (for those who no longer want their eyes), which can be enabled under the user profile section.
- Added more themes. Theme CSS now has more properties which can be adjusted.
- Email notification settings section added to admin page. This is a work in progress.
- Changelog section added to admin page. Moved from the hidden link and now shows 10 by default, with a link to the full page

</details>
<details>
<summary><h3>0.1.0-beta</h3></summary>
<h4>First beta test release of the system to be tested for install and running functionality</h4>

- Fully functioning changelog reachable from the secret admin menu - this will have a home eventually.
- Fully functional LDAP login system with failover host integration.
- Fully functional SMTP mail sending. All information is saved and pulled from the DB when requested.
- Fully customisable global settings, including logo, system name and banner colour.
- Email password resetting now possible and working.
- Bash install script implemented and fucntional. May need adapting as changes happen to the system.
- Email template created but could probably use some work.
- Stock editing, adding, removing and moving all functional to limited testing. Further testing needed.

<h5>Issues</h5>

- Some page redirects are not redirecting correctly and will be addressed when found.

</details>
</details>





