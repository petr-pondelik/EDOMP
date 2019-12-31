# DEVELOPMENT NOTES

## Command Line

### Doctrine

Vytvoření DB schématu:

    sudo php ./www/index.php orm:schema-tool:create

Drop schématu:

    sudo php ./www/index.php orm:schema-tool:drop --force

Update schématu:

    sudo php ./www/index.php orm:schema-tool:update --force

Nasazení migrace:

    sudo php ./www/index.php migrations:execute --up <migration_name>
        
Stažení migrace:

    sudo php ./www/index.php migrations:execute --down <migration_name>

### Resources compilation

For local development with webpack dev-server (dev-server is watching changes in resources files):

    npm run start:dev

For local development without webpack dev-server:

    npm run dev
    
For production (minified, optimized assets output):

    npm run prod

### PHPUnit

Spuštění sady testů:

    sudo ./vendor/bin/phpunit --bootstrap vendor/autoload.php Tests/<path_to_tests_set>

If errors or failures occur, comment these lines in **Tests/EDOMPTestCase.php**:    
(this setting enables using Nette sessions during tests, but disables most of the PHPUnit messages)

    protected $preserveGlobalState = false;
    protected $runTestInSeparateProcess = true;

## Known problems

-   Timeout - parameters complexity threshhold
-   Database longtext max length - threshhold

For production (not for local setup), uncomment in Bootstrap/Bootstrap.php (line:95):    
    
    $configurator->addConfig(__DIR__ . '/../Config/config.' . $env . '.neon');

## Newton API

-   Variable can't be "e" --> it's parsed as exp() function !!!: OK
-   Variable for equations can be only x, y or z (Newton API then formats output into equation standard form)
    -   Solve by select box with variables x, y and z for equations, for others templates types, leave text input: OK
    
-   equations in fractions format can't be handled by Newton API --> in needs to be manually multiplied by variable dividers!!!   
    -   NEEDS TO BE SOLVED!!!
    -   PROPOSAL
        -   Simplify expression (in classic way)
        -   Search for fractions with variable in divider
        -   If those were found, get all the dividers containing variable
        -   Find all the fractions dividers: \/\s*(\([x\-\+\s\(\)\d]*\))
        -   Find all the fractions (with grouped counters and dividers): ([x\d\sp]*)\/\s*(\([\-\+\s\(\)\dx]*\))
        -   Multiply all the expression members with collected dividers (make fraction divider 1, multiply counter by remaining dividers)
            -   Torn found variable fractions from expression, process it, then multiply rest of the expression with all the dividers and merge both parts
        
        
        Detect zero multiplied bracket:    
        0\s?(\(([\sx\+\d\(]+\)+))+

## Database

### problem_prototype_json_data

    ALTER TABLE prototype_json_data ADD UNIQUE condition_problem_unique(condition_type_id, problem_id);
    
    ALTER TABLE prototype_json_data DROP INDEX condition_problem_unique;


## Regulární výrazy

### Postprocess ProblemFinal Body

#### FIRST: RE to remove expressions in brackets multiplied by zero

    (note: actually only for \big( ... \big) expressions - needs to be extended)

    [\+\-]?\s*0\s*\\big\([\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\\big\)

#### SECOND: RE to remove fractions multiplied by zero

    [\+\-]?\s*0\s*\\frac\{[\d\\a-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}\s*\{[\da-zA-Z\"\s\<\>\/\=\+\-\*\(\)\^\{\}]*\}

#### THIRD: RE to remove values multiplied by zero

    [\+\-]\s*0(\s+|\s*\*\s*)\d

#### FINALLY: RE to remove zero values

    [\+\-]\s*0\s*
    
## TESTING TEMPLATES

### Testing Linear Equations templates

    $$ x + <par min="-5" max="15"/> = 4 $$
    
    $$ <par min="1" max="3"/> x = <par min="1" max="2"/> $$
    
    $$ <par min="-3" max="3"/> x = <par min="-2" max="2"/> - <par min="-3" max="3"/>x $$
    
    $$ <par min="-5" max="5"/> x - <par min="-2" max="2"/> = <par min="-5" max="5"/> - <par min="-4" max="4"/>x $$
    
    $$ \frac{<par min="-3" max="3"/>x}{<par min="-3" max="3"/>} + 5 = \frac{<par min="-3" max="3"/>}{2} - <par min="-5" max="5"/> $$
    
    $$ \frac{<par min="-2" max="2"/>x}{<par min="-3" max="3"/>} + \frac{<par min="-3" max="3"/>x}{<par min="-3" max="3"/>} = <par min="-3" max="3"/> $$
    
    $$ x + <par min="0" max="0"/> \big( 5 x + 4 \big) + <par min="0" max="0"/> = - <par min="0" max="0"/> $$
    
    $$ 5 x = 15 + <par min="-5" max="5"/> + 2 $$
    
    $$ <par min="-5" max="6"/> x + <par min="-5" max="5"/> = <par min="-5" max="5"/> $$
    
    $$ \frac{<par min="-5" max="6"/> x + <par min="-5" max="5"/>}{5} = <par min="-5" max="5"/> $$

### Testing Quadratic Equations templates with variable fractions

    $$ <par min="-5" max="5"/> x^2 + x + 5 = 4 $$
    
    $$ <par min="-5" max="5"/> x^2 + <par min="-5" max="5"/>x + <par min="-5" max="5"/> = 0 $$
    
    $$ \frac{x}{x - <par min="-5" max="5"/>} + \frac{x - <par min="-5" max="5"/>}{6} = \frac{4}{2} $$
    
    $$ \frac{x - <par min="-5" max="5"/>}{x} + \frac{x}{x - 1} + \frac{<par min="-10" max="10"/>}{x - x^2} = 0 $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{x^2 + x} + \frac{<par min="-5" max="5"/>}{x} $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{2 x^2 + x} + \frac{<par min="-5" max="5"/>}{x} $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{3 x^2 + 3 x} + \frac{<par min="-5" max="5"/>}{x} $$
    
    $$ 1 = \frac{x - <par min="-5" max="8"/> + 4}{3 x + 3 x} + \frac{<par min="-5" max="5"/>}{x + <par min="-5" max="5"/>} $$
    
    <!-- + 4 will shorten with 4 <par ... -->
    $$ 1 = \frac{x - <par min="-5" max="5"/> + 4}{ 4 <par min="-3" max="3"/> \big( 3 x + 3 \big) } + \frac{<par min="-4" max="4"/>}{x + <par min="-4" max="4"/>} $$
    
    $$ \frac{<par min="-3" max="3"/>}{x - <par min="-3" max="3"/>} - \frac{1}{x + 2} + \frac{8}{x - 4} = 0 $$
    
    $$ \frac{<par min="-3" max="3"/> + 5}{ <par min="-5" max="5"/> \big( x - 2 \big) } - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$
    
    $$ \frac{<par min="-2" max="2"/> + 5}{ \big( x + 2 \big) \big( x - <par min="-2" max="2"/> \big) } - \frac{1}{x + 2} + \frac{2}{4} = 0 $$

### Testing Quadratic Equations in multiply form

    $$ <par min="-2" max="2"/> \big( 4x - <par min="-3" max="3"/> \big) \big( x + 1 \big) = \big( <par min="-4" max="4"/> + 1 \big) \big( x - 1 \big) - 7$$

### Testing Arithmetic Sequences

    $$ a_n = <par min="-5" max="5"/> $$
    
    $$ a_n = <par min="-5" max="5"/> n $$
    
    $$ a_n = <par min="-5" max="5"/> - <par min="-5" max="5"/> n $$
    
    $$ a_n = \frac{<par min="-5" max="5"/> n - <par min="-5" max="5"/>}{<par min="-5" max="5"/>} $$
    
    $$ a_n = \frac{<par min="-5" max="5"/> - <par min="-5" max="5"/>}{3} $$

### Testing Geometric Sequences

    $$ q_n = <par min="-5" max="5"/> $$
    
    $$ q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{n-1} $$
    
    $$ q_n = \big( \frac{<par min="-5" max="5"/>}{<par min="-5" max="5"/>} \big)^{1-n} $$
    
    $$ q_n = <par min="-5" max="5"/> * 3^{1-n} $$
    
    $$ q_n = \big( - <par min="-5" max="5"/> \big)^{n} $$

### Testing postprocessProblemFinalExpression

    $$ \frac{ -2 }{x - 2} - \frac{1}{x + 2} + \frac{x^2 - 8}{x^2 - 4} = 0 $$
    
## Pomůcky

### Regex pro nahrazení objektového přístupu za array přístup

        ->(\w+)\)
        ['$1'])
        
## Development Environment

Application was developed on **Ubuntu 18.04 LTS**.

The **XAMPP for Linux 64bit 7.3.1-0** was used for development.

This version of XAMPP contains the following software releases:
   - **PHP 7.3.0**
   - **MariaDB 10.1.37**
   - **OpenSSL 1.0.2q**
   - **phpMyAdmin 4.8.4**

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

Allow the usage of custom virtual hosts. In order to achieve that, in **<lampp_dir>/etc/httpd.conf**, uncomment:

        Include etc/extra/httpd-vhosts.conf

Create your virtual host (add to **extra/httpd-vhosts.conf**):

        <VirtualHost *:80>
            ServerName appName.localhost
            DocumentRoot "path_to_your_www"
            DirectoryIndex index.php
            <Directory "path_to_your_www">
                Options All
                AllowOverride All
                Require all granted
            </Directory>
        </VirtualHost>

XAMPP **restart is needed** after httpd-vhosts.conf editation.