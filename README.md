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

## Notes

\(x^2 + y^2 + 30 + <par type="integer" min="10" max="50"/> = 2z^2\)

## Database

### problem_final_test_rel

CREATE TRIGGER problem_final_test_rel_update_check BEFORE INSERT ON problem_final_test_rel
  FOR EACH ROW
  BEGIN
    DECLARE cnt INT;
    SET cnt = ( SELECT COUNT(*) FROM problem WHERE problem_id = NEW.prototype_id AND is_prototype = TRUE );
    IF cnt < 1 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Problem prototype id does not match any existing prototype.';
    END IF;
END;

CREATE TRIGGER problem_final_test_rel_update_check BEFORE UPDATE ON problem_final_test_rel
  FOR EACH ROW
  BEGIN
    DECLARE cnt INT;
    SET cnt = ( SELECT COUNT(*) FROM problem WHERE problem_id = NEW.prototype_id AND is_prototype = TRUE );
    IF cnt < 1 THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Problem prototype id does not match any existing prototype.';
    END IF;
END;

### problem_prototype_json_data

ALTER TABLE prototype_json_data ADD UNIQUE condition_problem_unique(condition_type_id, problem_id);

ALTER TABLE prototype_json_data DROP INDEX condition_problem_unique;

### Konzultace 7.3. 2019

-   Struktura hlavičky
    -   Logo,
        -   Import, select již použitých hlaviček
    -   Ročník
    -   Obor
    -   Označení testu

-   Generování určitého počtu různých variant testů

-   (Bonus: Náhodný výběr úlohy z filtrovaného selectu)

### Konzulatace 21.3. 2019

-   Vygenerování PDF z výstupního LaTeX dokumentu pomocí webové služby: https://github.com/aslushnikov/latex-online#api

-   Šablona pro výstup do LaTeX?

-   V případě pokusu o odstranění příkladu použitého v některém z vygenerovaných testů vznikne chyba v důsledku referenční integrity tabulky relace Problém - Test
    -   Jak se k tomu postavit?
        -   Nepovolit takovou úlohu odstranit? (před tím by musel být ostraněn test)
        -   Odstranit úlohu i z testů, ve kterých byla použita (při vygenerování podobného testu by test obsahoval o úlohu méně)
        -   


### DOCTRINE CMDLINE
-   Vytvoření DB schématu:

        sudo php ./www/index.php orm:schema-tool:create

-   Drop schématu:

        sudo php ./www/index.php orm:schema-tool:drop --force

-   Update schématu:

        sudo php ./www/index.php orm:schema-tool:update --force

### PHPUNit Tests

./vendor/bin/phpunit --bootstrap App/bootstrap.php App/AppTests/Model/Entity

### Known problems - to mention in thesis

-   Timeout - parameters complexity threshhold
-   Database longtext max length - threshhold

### Newton API restrictions

-   Variable can't be "e" --> it's parsed as exp() function !!!