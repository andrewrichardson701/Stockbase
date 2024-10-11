
<!-- Improved compatibility of back to top link: See: https://github.com/othneildrew/Best-README-Template/pull/73 -->
<a id="readme-top"></a>
<!--
*** Thanks for checking out the Best-README-Template. If you have a suggestion
*** that would make this better, please fork the repo and create a pull request
*** or simply open an issue with the tag "enhancement".
*** Don't forget to give the project a star!
*** Thanks again! Now go create something AMAZING! :D
-->



<!-- PROJECT SHIELDS -->
<!--
*** I'm using markdown "reference style" links for readability.
*** Reference links are enclosed in brackets [ ] instead of parentheses ( ).
*** See the bottom of this document for the declaration of the reference variables
*** for contributors-url, forks-url, etc. This is an optional, concise syntax you may use.
*** https://www.markdownguide.org/basic-syntax/#reference-style-links
-->
[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![LinkedIn][linkedin-shield]][linkedin-url]



<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/andrewrichardson701/Stockbase">
    <img src="assets/img/config/default/default-logo.png" alt="Logo" width="80" height="80">
  </a>

<h3 align="center">Stockbase</h3>

  <p align="center">
    An inventory management system, tailed to data centres, with less of the bloat.
    <br />
    <a href="https://github.com/andrewrichardson701/Stockbase"><strong>Explore the docs »</strong></a>
    <br />
    <br />
    <a href="https://stockbase-demo.ajrich.co.uk">View Demo</a>
    ·
    <a href="https://github.com/andrewrichardson701/Stockbase/issues/new?labels=bug&template=bug-report---.md">Report Bug</a>
    ·
    <a href="https://github.com/andrewrichardson701/Stockbase/issues/new?labels=enhancement&template=feature-request---.md">Request Feature</a>
  </p>
</div>



<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table of Contents</summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
      <ul>
        <li><a href="#built-with">Built With</a></li>
      </ul>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#roadmap">Roadmap</a></li>
    <li><a href="#contributing">Contributing</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
## About The Project

[![Product Name Screen Shot][product-screenshot]](https://stockbase-demo.ajrich.co.uk)

Stockbase is an inventory management system, tailored to data centres, designed to have less of the bloat that commercial and other open source systems have.

The design aims to make managing and tracking devices, parts, cables, fibre optics and anything else of use in an easy to manage platform.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Built With
* [![PHP][PHP.net]][PHP-url]
* [![Bootstrap][Bootstrap.com]][Bootstrap-url]
 * [![JavaScript][JavaScript.com]][JavaScript-url]
* [![JQuery][JQuery.com]][JQuery-url]

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- GETTING STARTED -->
## Getting Started

The installation steps are important to be followed in order.

If this is not a fresh install (e.g. a new VM or you have no existing database or web server) feel free to use the Automated Deployment script.


### Installation


<details>

<summary><h3>Automated Deployment (Not recommended unless on a fresh install)</h3></summary>

  

For automated deployment, run the below command to clone the repository and run the install script:

  

`git clone https://github.com/andrewrichardson701/Stockbase.git && /bin/bash stockbase/assets/scripts/install.bash`

  

This will run the setup for the system and provide a username and password to login with.

  

Login to your site to continue with any further setup

  

</details>

<details>

<summary><h3>Manual Deployment</h3></summary>

For manual deployment, it requires all packages to be installed manually and the database to be configured and setup correctly.

  

Clone the repo first, and the follow the below steps.

`git clone https://github.com/andrewrichardson701/Stockbase.git`

  

1. Update your packages and install them if you are confident they are okay to be updated

  

`sudo apt update`

  

`sudo apt upgrade`

  

2. Install PHP 8.3 and all dependencies required

  

a. Install the PHP repository

  

```

sudo apt install lsb-release ca-certificates apt-transport-https software-properties-common -y

sudo add-apt-repository ppa:ondrej/php

sudo add-apt-repository ppa:ondrej/nginx-mainline

sudo add-apt-repository ppa:ondrej/apache2

sudo apt update

```

  

b. Install the package and dependencies

```

sudo apt install -y php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-fpm php8.3-gd php8.3-igbinary php8.3-imagick php8.3-imap php8.3-intl php8.3-ldap php8.3-mbstring php8.3-mysql php8.3-readline php8.3-redis php8.3-soap php8.3-xml php8.3-xsl php8.3-zip

```

  

3. Install MySQL Server and run first setup

  

*Confirm whether or not a MySQL database is installed first, for example MariaDB. If MariaDB is installed, it WILL stop the MariaDB service to break.*

*Run: 'mysql -u root -p' to confirm if there are any mysql databases installed. If it lets you login with a password, there is one already. Skip this step if it exists.*

  

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

  

- Confirm there is no database named 'stockbase'

  

```

mysql -u root -p

  

USE stockbase;

quit;

```

  

If mysql throws an error, the database doesn’t exist. This is what we want.

If it does exist, it will be overwritten.

- Run the MySQL DB setup

*we will navigate to the downloaded git repo*

  

```

cd stockbase

  

mysql -u root -p < assets/sql/db_setup.sql

```

  

- Run the extras script to fill in the required tables with the information they need.

  

*This script creates the required fields for the config and config_default tables, also setting the auto-increment values*

  

```

mysql -u root -p < assets/sql/db_extras.sql

```

- Create a user for the database to verify against

  

We will first check if a user exists under the name 'stockbaseuser'.

  

```

mysql -u root -p

SELECT User, Host FROM mysql.user WHERE User='stockbaseuser' AND Host='localhost';

```

  

If no rows are returned, we will add a new user.

If there are rows, we will either need to know the current password, or drop the user.

  

Select the relevant option:

  

<details>

<summary><h5>No user found, create new</h5></summary>

  

- Create the new user, replacing `[SECRET PASSWORD]` with your password

  

```

CREATE USER 'stockbaseuser'@'localhost' IDENTIFIED BY '[SECRET PASSWORD]';

GRANT ALL PRIVILEGES ON stockbase.* TO 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

</details>

  

<details>

<summary><h5>User exists and password known</h5></summary>

  

- Grant the user permissions.

  

```

GRANT ALL PRIVILEGES ON stockbase.* TO 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

</details>

  

<details>

<summary><h5>User exists and needs to be dropped</h5></summary>

  

- Drop the user

  

```

DROP USER 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

- Create the new user, replacing `[SECRET PASSWORD]` with your password

  

```

CREATE USER 'stockbaseuser'@'localhost' IDENTIFIED BY '[SECRET PASSWORD]';

GRANT ALL PRIVILEGES ON stockbase.* TO 'stockbaseuser'@'localhost';

FLUSH PRIVILEGES;

quit;

```

  

</details>

  

- Confirm you can login and access the database

  

```

mysql -u stockbaseuser -p

USE stockbase;

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

$dBUsername = 'stockbaseuser';

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

mysql -u stockbaseuser -p

  

INSERT INTO stockbase.users (id, username, first_name, last_name, email, auth, role_id, enabled, password_expired, password)

VALUES (1, 'root', 'root', 'root', 'root@$hostname', 'local', 0, 1, 1, '[PASSWORD HASH]]');

UPDATE stockbase.users SET id=0 where id=1;

ALTER TABLE stockbase.users AUTO_INCREMENT = 1;

```

  

6. Decide on your web URL

  

We need a base URL for the site to be located at (e.g. stockbase.domain.com)

  

Update the config with this url, replacing `[WEB DOMAIN]` with your domain name/url:

  

```

mysql -u stockbaseuser -p

  

UPDATE config SET base_url='[WEB DOMAIN]' WHERE id=1;

quit;

```

  

7. Move your files to your web server/desired location

  

*Make sure you are already in the downloaded repo folder*

  

Replace `new/folder/location/` to the folder you want your server hosted from (e.g. /var/www/html/stockbase/) including the trailing /

  

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

- Run the below to create the config, replacing `[DOMAIN NAME]` and `[LOCATION]` with your domain name and folder location

  

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

- Run the below to create the config, replacing `[DOMAIN NAME]` and `[LOCATION]` with your domain name and folder location

  

```

cat > /etc/nginx/sites-available/$web_domain <<EOL

server {

listen 80;

server_name $web_domain;

  

root $folder_name;

index index.php index.html;

  

location / {

try_files \$uri \$uri/ /index.php?\$query_string;

}

  

location ~ \.php$ {

include fastcgi_params;

fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

fastcgi_index index.php;

fastcgi_param SCRIPT_FILENAME $document_root\$fastcgi_script_name;

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

<em>This assumes you have an SSL certificate and will not cover Lets Encrypt but it can be used for your cert if needed.</em>

  

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

try_files \$uri \$uri/ /index.php?\$query_string;

}

  

location ~ \.php$ {

include fastcgi_params;

fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

fastcgi_index index.php;

fastcgi_param SCRIPT_FILENAME $document_root\$fastcgi_script_name;

}

  

return 301 https://$web_domain\$request_uri;

}

  

server {

listen 443 ssl;

server_name $web_domain;

  

root $folder_name;

index index.php index.html;

  

ssl_certificate $ssl_certificate;

ssl_certificate_key $ssl_key;

  

add_header X-Frame-Options "SAMEORIGIN";

add_header X-Content-Type-Options "nosniff";

  

charset utf-8;

  

location / {

try_files \$uri \$uri/ /index.php?\$query_string;

}

  

location = /favicon.ico { access_log off; log_not_found off; }

location = /robots.txt { access_log off; log_not_found off; }

  

error_page 404 /index.php;

  

location ~ \.php$ {

fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;

fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;

include fastcgi_params;

fastcgi_read_timeout 300s;

proxy_read_timeout 600s;

fastcgi_buffers 16 16k;

fastcgi_buffer_size 32k;

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

  

e.g. https://stockbase.domain.local/

  

You will need to select "local" as your login type if the local toggle is shown on the login page

LDAP will be enabled by default with a config in place, which will not work on your system.

  

Login with the username 'root' and password created in step 5 (NOT the hashed password).

  

You will be prompted to make your first Site / Area / Shelf for the system, so please add one. (these can be changed later)

  

Head to the 'Admin' page from the navigation bar and configure your setup.

  

</details>

<!-- ROADMAP -->
## Roadmap

[Restyaboard Roadmap](todo.ajrich.co.uk)

- [x] Favourites list
- [ ] Cable stock auditing

See the [open issues](https://github.com/andrewrichardson701/Stockbase/issues) for a full list of proposed features (and known issues).

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTRIBUTING -->
## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### Top contributors:

<a href="https://github.com/andrewrichardson701/Stockbase/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=andrewrichardson701/Stockbase" alt="contrib.rocks image" />
</a>



<!-- LICENSE -->
## License

Distributed under the GNU General Public License v3.0. See `COPYING.txt` for more information.

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- CONTACT -->
## Contact

Project Link: [https://github.com/andrewrichardson701/Stockbase](https://github.com/andrewrichardson701/Stockbase)

<p align="right">(<a href="#readme-top">back to top</a>)</p>



<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/andrewrichardson701/Stockbase.svg?style=for-the-badge
[contributors-url]: https://github.com/andrewrichardson701/Stockbase/graphs/contributors
[forks-shield]: https://img.shields.io/github/forks/andrewrichardson701/Stockbase.svg?style=for-the-badge
[forks-url]: https://github.com/andrewrichardson701/Stockbase/network/members
[stars-shield]: https://img.shields.io/github/stars/andrewrichardson701/Stockbase.svg?style=for-the-badge
[stars-url]: https://github.com/andrewrichardson701/Stockbase/stargazers
[issues-shield]: https://img.shields.io/github/issues/andrewrichardson701/Stockbase.svg?style=for-the-badge
[issues-url]: https://github.com/andrewrichardson701/Stockbase/issues
[license-shield]: https://img.shields.io/github/license/andrewrichardson701/Stockbase.svg?style=for-the-badge
[license-url]: https://github.com/andrewrichardson701/Stockbase/blob/master/COPYING.txt
[linkedin-shield]: https://img.shields.io/badge/-LinkedIn-black.svg?style=for-the-badge&logo=linkedin&colorB=555
[linkedin-url]: https://linkedin.com/in/andrewrichardson701
[product-screenshot]: assets/img/index-screenshot.png
[PHP.net]: https://img.shields.io/badge/PHP-4F5B93?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/
[Bootstrap.com]: https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white
[Bootstrap-url]: https://getbootstrap.com
[JQuery.com]: https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white
[JQuery-url]: https://jquery.com 
[JavaScript.com]: https://img.shields.io/badge/JS-70DB4F?style=for-the-badge&logo=javascript&logoColor=white
[JavaScript-url]: https://www.javascript.com/
