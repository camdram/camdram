Camdram
========================

The portal website for student theatre in Cambridge

[![Build Status](https://travis-ci.org/camdram/camdram.png?branch=master)](https://travis-ci.org/camdram/camdram)

Camdram is an open source project developed for the benefit of the Cambridge student theatre community. Anyone can contribute to the bugs and new features. Below are the steps required to set up a development checkout of Camdram. If you encounter any problems with the below, please create a Github issue or send an e-mail to websupport@camdram.net

1) Install programs
--------------------

Use one of the following commands to install the necessary packages required to run Camdram

###Debian-based distributions, e.g. Ubuntu

    $ sudo apt-get install git-core php5 php5-cli curl php5-curl php5-intl php5-sqlite php-apc php5-gd

###RPM-based distributions, e.g. Fedora

    $ sudo yum install git php php-cli curl php-intl php-pdo php-gd

1) Create a checkout of the Camdram repository
----------------------------------------------

Create an account on Github, then 'fork' this repository using the link above.

Run `git clone https://github.com/YOUR-GITHUB-USERNAME/camdram.git`, which will pull a copy of
the code into a new folder called 'camdram'

Change into the newly created 'camdram' directory before proceeding:

    cd camdram

3) Install PHP dependencies
-------------------------------

Symfony (and therefore Camdram) uses Composer to download the PHP libraries it uses. First, install composer locally inside the Camdram source code folder:

    curl -sS https://getcomposer.org/installer | php

This downloads a file named 'composer.phar'. Run this to download all the PHP libaries:

    php composer.phar install -n

4) Create a database
---------------------------

Run the command below to generate a SQLite datastore which contains randomly-generated sample data

    php app/console camdram:database:update

Note: if you get errors at this step - or in the next step, you get a blank page from the webserver,
you may need to set 

  date.timezone = Europe/London

in the php.ini file for the PHP Command Line. In Mint this is at /etc/php5/cli/php.ini

5) Run the web server
---------------------------

Run `php app/console server:run` to start a web server. You should then be able to visit [http://localhost:8000/app_dev.php](http://localhost:8000/app_dev.php) in your web browser

The 'app_dev.php' in the URL launches Camdram in the 'development' environment, which is optimized for the frequent code changes that occur when doing development work. It also contains a useful toolbar at the foot of the page which contains, amongst other information, useful information about load times, memory usage and the number of SQL queries run.

6) Read the Wiki
----------------

[The Wiki][1] has various pieces of information about both the current and in-development 
versions of Camdram. Reading through those pages can give insight into the more esoteric
parts of the system.

7) Pull in other people's changes
-------------------------------------

At a later date, once your local repository has become out of sync with Gituhb (because other people have committed code), you can run the following commands to pull in other people's changes and update your checkout:

    git pull
    php composer.phar install
    php app/console camdram:database:update

This will pull in the latest code, update any changes to the dependencies and update the database. The second and third command may not be necessary if no one has recently changed the dependencies or database schema, but there's never any harm in running them (apart from database camdram:database:update with a SQLite, which completely drops and recreates the whole database).


8) Write some code
--------------------

 * The site uses the Symfony PHP framework - read the documentation at 
   http://symfony.com/doc/2.3/index.html
 * Use the Github issue tracker to get an idea what we're currently working on.
   If you think you know how to do something, write the code, commit it, and 
   submit a pull request.
 * If you want to discuss how to implement a new feature or how to fix a bug, 
   get in touch with one of the developers. It would probably be wise to get in
   touch before starting on any significant projects to avoid wasted effort!
 * Visit http://try.github.io/ if you're not familiar with Git.

9) Commit some code
----------------------

 * Run `git add file1.php file2.php` for each file you wish to include in the commit
 * Run `git commit` and enter a message describing the changes you have made
 * Run `git push` to send your changes to Github

It is good practice to include the relevant issue number (prefixed with a hash #) at the end of the commit message - this will cause your commit message to appear on the issue page on Github.

[1]: http://github.com/camdram/camdram/wiki
