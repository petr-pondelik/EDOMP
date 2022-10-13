# Electronical Database Of Mathematical Problems

## Requirements

* **PHP 7.2**
  * extensions:
    * php7.2-zip
    * php7.2-xml
    * php7.2-mbstring
* **Composer 1.x**
  * You can use Composer's binary from IDE or install Composer  
  * For installing Composer, follow the instructions on [Introduction - Composer](https://getcomposer.org/doc/00-intro.md)
* **Node 12.22.12**
* **NPM**
* **MySQL >=5.6** or **MariaDB >=10.1**

## Installation

Make sure you have directories **log/, temp/, data/, www/public_data/ and www/public_data/logos created and writable**.

    For production (not for local setup), uncomment in Bootstrap/Bootstrap.php (line:95):    

    $configurator->addConfig(__DIR__ . '/../Config/config.' . $env . '.neon');

### 1. Vendors installation

In application **root directory** run:

    composer install
    npm install

Switch between PHP versions:

    sudo update-alternatives --config php

Use NVM to switch between Node.js versions:

    # Install NVM on Ubuntu
    curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.0/install.sh | bash
    export NVM_DIR="$HOME/.nvm"
    [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
    [ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"
    nvm --version

    # List available Node versions
    nvm ls-remote

    # Install Node version
    nvm install <version>

    # Choose specific Node version
    nvm use <version>

### 2. Resources compilation

After that, **compile the app resources**:  

For local development with webpack dev-server (dev-server is watching changes in resources files):

    npm run start:dev

For local development without webpack dev-server:

    npm run dev

For production (minified, optimized assets output):

    npm run prod

### 3. Database installation

**Create database scheme** from Doctrine entities.  
Run from **app root directory**:

    php ./www/index.php orm:schema-tool:create

EITHER  
Fill database with init data.   
Run from **app root directory**:

    sudo php ./www/index.php migrations:execute --up InitData

OR

Fill database with testing data.    
Run from **app root directory**:

    sudo php ./www/index.php migrations:execute --up TestingDataV1

### 4. Newton server installation

Get Newton API from [GitHub](https://github.com/aunyks/newton-api).

Download ZIP or clone the repository via SSH in case you have GitLab account with SSH key:

    git clone git@github.com:aunyks/newton-api.git

Run Newton server:

    node <newton_dir>/app.js

It should display message like "We're up at 3000!".  
Here, 3000 is the port the Newton server is listening on.   
So the API here is listening on localhost:3000 or 127.0.0.1:3000.

## Run PHPUnit tests

To run **all** the PHPUnit tests, run:

    sudo ./vendor/bin/phpunit --bootstrap vendor/autoload.php Tests

To run **specific** PHPUnit tests, run:

    sudo ./vendor/bin/phpunit --bootstrap vendor/autoload.php Tests/<path_to_subdir_or_file>

If errors or failures occur, comment these lines in **Tests/EDOMPTestCase.php**:    
(this setting enables using Nette sessions during tests, but disables most of the PHPUnit messages)

    protected $preserveGlobalState = false;
    protected $runTestInSeparateProcess = true;

## Localhost restrictions and tips

Action for **tests compilation** will not work on localhost. Overleaf API requires **publicly visible archive** to create project on portal.

For catching e-mails sent from application locally, it's handy to use **Mailhog**.

## More information

For more information, try to read **dev_notes.md**.
