# Camdram

[![Build Status](https://travis-ci.org/camdram/camdram.png?branch=master)](https://travis-ci.org/camdram/camdram)
[![Join the chat at https://gitter.im/camdram/development](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/camdram/development)

Camdram is an open source project developed for the benefit of the Cambridge student theatre community. Anyone can contribute bugs and new features. The steps required to set up a development checkout of Camdram are detailed below. For the sake of brevity, these instructions assume that the reader is familiar with a number of technologies, such as developing on a Linux based platform, using Git and GitHub.

New releases are tagged on the `master` branch every so often and pushed to https://www.camdram.net/. https://development.camdram.net/ always reflects the latest changes.

If you encounter any problems with the instructions below, please [create a GitHub issue]( https://github.com/camdram/camdram/issues/new) or send an e-mail to websupport@camdram.net. We also have a [live chat hosted on Gitter](https://gitter.im/camdram/development) which you can use to quickly and informally get in touch with the development team.

## 1) Install programs

Install the necessary packages required to run Camdram. Camdram currently requires PHP version 5.6 or greater.

The command below can be run on recent Debian-based distros (including Ubuntu) - installation methods on other distros and operating systems will vary.

    $ sudo apt-get install git-core php php-cli composer php-curl php-intl php-sqlite3 php-gd php-json

The steps below assume that the Composer package manager is installed globally on your system. If it is not available in your distro's repositories, alternate installation methods can be found at https://getcomposer.org/download/

## 2) Create a local version of Camdram

The command below will download and set up a Camdram checkout in a new folder called `camdram`:

    composer create-project camdram/camdram camdram dev-master --no-interaction

After obtaining a copy of the code, change into the newly created 'camdram' directory before proceeding:

    cd camdram

## 3) Run the web server

Run `php app/console server:run` to start a web server. You should then be able to visit [http://localhost:8000/app_dev.php](http://localhost:8000/app_dev.php) in your web browser to see your personal version of Camdram's homepage.

The 'app_dev.php' in the URL launches Camdram in the 'development' environment, which is optimized for the frequent code changes that occur when doing development work. It also contains a useful toolbar at the foot of the page which contains, amongst other information, useful information about load times, memory usage and the number of SQL queries run. [Read more about Symfony's environments here](https://github.com/camdram/camdram/wiki/The-Symfony-environments).

## 4) Create a fork

Camdram's development model follows the standard idioms used by FOSS projects hosted on GitHub. If you are just interested in experimenting with the codebase, no further steps are necessary, but if you'd like to contribute then you will need to [create a fork](https://help.github.com/articles/fork-a-repo).

After creating a personal fork, you can repoint your checkout using the commands below.

    $ git remote rename origin upstream
    $ git remote add origin git@github.com:username/camdram.git

## 5) Write some code

It is a good idea to create a "feature branch" before starting development, so that the pull request will be named appropriately:

    $ git checkout -b my-cool-feature

Some useful tips:
 * The site uses the Symfony PHP framework - [read the documentation](http://symfony.com/doc/2.8/index.html).
 * Use the GitHub issue tracker to discover and discuss issues to work on. If you think you know how to do something, write the code, commit it, and
   submit a pull request.
 * If you want to discuss how to implement a new feature or how to fix a bug, get in touch with one of the developers. It would probably be wise to get in
   touch before starting on any significant projects to avoid wasted effort!
 * Visit http://try.github.io/ if you're not familiar with Git.
 * Code should ideally conform to the style guide here: http://www.php-fig.org/psr/psr-2/. If this is far too daunting, a poorly styled but functional improvement is better than no improvement. You can use http://cs.sensiolabs.org/ to (mostly) clean your code up after writing it.

Depending on the type of change, make sure to check it works as a logged-in and/or non-logged in visitor. You can log in to your local instance of Camdram with one of three default accounts (the password for each is just 'password'):
 * user1@camdram.net
 * user2@camdram.net
 * admin@camdram.net (this is an admin user) 

## 6) Submit your changes

 * Run `git add file1.php file2.php` for each file you wish to include in the commit
 * Run `git commit` and enter a message describing the changes you have made
 * Run `git push` to send your changes to GitHub

It is good practice to include the relevant issue number (prefixed with a hash #) at the end of the commit message - this will cause your commit to be linked to the issue page on GitHub.

Once your changes are pushed to your Camdram fork on GitHub, you can [submit a pull request](https://help.github.com/articles/creating-a-pull-request/) to have it included in Camdram.

## 7) Pull in other people's changes

At a later date, once your local repository has become out of sync with Github (because other people have make changes), you can run the following commands to pull in other people's changes and update your checkout:

    $ git fetch upstream
    $ git merge upstream/master

The following commands may need to be run after the above, if the dependencies or database schema have changed.

    $ composer install
    $ php app/console camdram:database:refresh

## 8) Read the Wiki

[The Wiki](http://github.com/camdram/camdram/wiki) has various pieces of information about both the current and in-development
versions of Camdram. Reading through those pages can give insight into the more esoteric
parts of the system.

The following wiki pages detail how to create a server set-up that's more similar to the live version of Camdram:

 * [Setting up an Apache virtual host](https://github.com/camdram/camdram/wiki/Setting-up-an-Apache-virtual-host)
 * [Setting up a MySQL database](https://github.com/camdram/camdram/wiki/Setting-up-a-MySQL-database)
 * [External API registration](https://github.com/camdram/camdram/wiki/API-registration)
 * [Elasticsearch setup guide](https://github.com/camdram/camdram/wiki/Elasticsearch%20setup%20guide)

You can also [read information about Camdram's suite of tests](https://github.com/camdram/camdram/wiki/Running-and-creating-tests)
