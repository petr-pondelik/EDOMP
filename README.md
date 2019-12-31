# Electronical Database Of Mathematical Problems

## Installation

    composer install

Make sure you have created writable directory www/wwwtmp.

## Development

### XAMPP Server

#### Installation

-   Download XAMPP Server for Linux from: [XAMPP Website](https://www.apachefriends.org/index.html)
-   Move to the Downloads folder
-   The installation package you downloaded needs to be made executable before it can be used further. Run the following command for this purpose:
        $ chmod 755 [package name]
-   Now the install package is in an executable form.
-   As a privileged root user, run the following command in order to launch the graphical setup wizard.
        $ sudo ./[package name]
-   Work through the graphical setup wizard
-   Launch XAMPP through the Terminal
        $ sudo /opt/lampp/lampp start
-   If you get error output, it means that Net Tools are not installed on your system
-   In order to install Net Tools, run the following command as root:
        $ sudo apt install net-tools
-   Verify Installation. In browser:
        http://localhost/dashboard
        http://localhost/phpmyadmin

#### Basic commands

Start XAMPP:

    sudo /opt/lampp/lampp start

Stop XAMPP:

    sudo /opt/lampp/lampp stop

Restart XAMPP:

    sudo /opt/lampp/lampp restart

Start only specific service (mysql in example):

    sudo /opt/lampp/lampp startmysql


#### Problems solving

You may be running your own mysql or apache. It that case you have to stop these services.

    sudo service mysql stop
    sudo service apache2 stop

#### Virtual Hosts

-   Allow the usage of custom virtual hosts. Uncomment:

        Include etc/extra/httpd-vhosts.conf

-   Create your virtual host (add to extra/httpd-vhosts.conf):

        <VirtualHost \*:80>
            ServerName myfirstapp.localhost
            DocumentRoot "/opt/lampp/htdocs/my-first-project"
            DirectoryIndex index.php
            <Directory "/opt/lampp/htdocs/my-first-project">
                Options All
                AllowOverride All
                Require all granted
            </Directory>
        </VirtualHost>

-   XAMPP Restart is needed after httpd-vhosts.conf editation.