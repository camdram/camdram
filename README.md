Camdram.net
========================

The instructions below should fairly accurately describe how to go from creating a
checkout to getting a working site

1) Create a checkout of the camdram repository
----------------------------------------------

Run `git clone https://github.com/camdram/camdram.git`, which will pull a copy of
the code into a new folder called 'camdram'

2) Install dependencies
-----------------------

Camdram is built upon Symfony, which uses [Composer][2] to manage its dependencies
on other software libraries. It is recommened that you install Composer into the
camdram directory:

`cd camdram`

`curl -s https://getcomposer.org/installer | php`

Then run the following command, which will automatically download all camdram's
PHP dependencies

    php composer.phar install

3) Checking your System Configuration
-------------------------------------

Make sure that your local system is properly configured for Symfony by running
the `check.php` script from the command line:

    php app/check.php

Correct any problems it finds...

4) Create a database
--------------------

Using your database administration tool of choice (e.g. phpMyAdmin), create
a database with its own username and password, and start with importing a copy
of the existing camdram database.


5) Configure camdram
--------------------

Camdram expects to find a configuration file is at app/config/parameters.yml.
app/config/parameters.dist.yml contains some default values and can be used as
a staring point.

`cp app/config/parameters.dist.ini app/config/parameters.ini`

Enter the database configuration details chosen above. The API keys are used
by the authentication system and are optional (there's a wiki page about
how to obtain them). The Raven and local (password-based) login systems work
without an API key.

6) Create a virtual host
------------------------
Create an Apache vhost similar to the following:

        <VirtualHost *:80>

                DocumentRoot /var/www/camdram/web
                ServerName local.camdram.net

                <Directory /var/www/camdram>
                        Options Indexes FollowSymLinks MultiViews
                        AllowOverride All
                        Order allow,deny
                        allow from all
                </Directory>

        </VirtualHost>

7) Migrate database schema
--------------------------

Run the following command to make the necessary changes to the database:

`php app/console doctrine:migrations:migrate`

8) (Optional) Run console tools to tidy up database
---------------------------------------------------

It is recommended that you run `php app/console camdram:init`, which performs a range of data migrations
(creating slugs, calculating Cambridge terms/weeks, detecting Raven/Google accounts based on e-mail addresses)

There are a number of other tools which can be run which can be run, which are
detailed on a Wiki page


Introduction to Symfony
-----------------------

The Symfony Standard Edition is configured with the following defaults:

  * Twig is the only configured template engine;

  * Doctrine ORM/DBAL is configured;

  * Swiftmailer is configured;

  * Annotations for everything are enabled.

It comes pre-configured with the following bundles:

  * **FrameworkBundle** - The core Symfony framework bundle

  * [**SensioFrameworkExtraBundle**][6] - Adds several enhancements, including
    template and routing annotation capability

  * [**DoctrineBundle**][7] - Adds support for the Doctrine ORM

  * [**TwigBundle**][8] - Adds support for the Twig templating engine

  * [**SecurityBundle**][9] - Adds security by integrating Symfony's security
    component

  * [**SwiftmailerBundle**][10] - Adds support for Swiftmailer, a library for
    sending emails

  * [**MonologBundle**][11] - Adds support for Monolog, a logging library

  * [**AsseticBundle**][12] - Adds support for Assetic, an asset processing
    library

  * [**JMSSecurityExtraBundle**][13] - Allows security to be added via
    annotations

  * [**JMSDiExtraBundle**][14] - Adds more powerful dependency injection
    features

  * **WebProfilerBundle** (in dev/test env) - Adds profiling functionality and
    the web debug toolbar

  * **SensioDistributionBundle** (in dev/test env) - Adds functionality for
    configuring and working with Symfony distributions

  * [**SensioGeneratorBundle**][15] (in dev/test env) - Adds code generation
    capabilities

  * **AcmeDemoBundle** (in dev/test env) - A demo bundle with some example
    code

Enjoy!

[1]:  http://symfony.com/doc/2.1/book/installation.html
[2]:  http://getcomposer.org/
[3]:  http://symfony.com/download
[4]:  http://symfony.com/doc/2.1/quick_tour/the_big_picture.html
[5]:  http://symfony.com/doc/2.1/index.html
[6]:  http://symfony.com/doc/2.1/bundles/SensioFrameworkExtraBundle/index.html
[7]:  http://symfony.com/doc/2.1/book/doctrine.html
[8]:  http://symfony.com/doc/2.1/book/templating.html
[9]:  http://symfony.com/doc/2.1/book/security.html
[10]: http://symfony.com/doc/2.1/cookbook/email.html
[11]: http://symfony.com/doc/2.1/cookbook/logging/monolog.html
[12]: http://symfony.com/doc/2.1/cookbook/assetic/asset_management.html
[13]: http://jmsyst.com/bundles/JMSSecurityExtraBundle/master
[14]: http://jmsyst.com/bundles/JMSDiExtraBundle/master
[15]: http://symfony.com/doc/2.1/bundles/SensioGeneratorBundle/index.html
