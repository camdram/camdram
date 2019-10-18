# Camdram

[![Build Status](https://travis-ci.org/camdram/camdram.svg?branch=master)](https://travis-ci.org/camdram/camdram)
[![Deployment Pipeline](https://app.buddy.works/pahoyes/camdram/pipelines/pipeline/144463/badge.svg?token=a793d019566e652178a2b8fb6b590b025de793a9a858bfcc4bb487c57101f994)](https://app.buddy.works/pahoyes/camdram/pipelines/pipeline/144463)
[![Code Coverage](https://codecov.io/gh/camdram/camdram/branch/master/graphs/badge.svg?branch=master)](http://codecov.io/github/camdram/camdram?branch=master)
[![Join the chat at https://gitter.im/camdram/development](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/camdram/development)

Camdram is an [open source](http://opensource.org/docs/osd) project developed by a team of volunteers for the benefit of the Cambridge student theatre community. We use this repository to host the code that runs Camdram and to co-ordinate our work, however we welcome anyone to contribute bugs and suggest new features.

The steps required to set up a development checkout of Camdram are detailed below. For the sake of brevity, these instructions assume that the reader is familiar with a number of technologies, such as developing on a Linux based platform, using Git and GitHub.

New releases are made on the `master` branch every so often using the GitHub interface. This automatically generates a changelog and pushes the new release into production at https://www.camdram.net/. The latest changes can be seen at https://development.camdram.net/ which always reflects the `HEAD` state of this repository.

If you encounter any problems with the instructions below, please [create a GitHub issue]( https://github.com/camdram/camdram/issues/new) or send an e-mail to support@camdram.net. We also have a [live chat hosted on Gitter](https://gitter.im/camdram/development) which you can use to quickly and informally get in touch with the development team.

## 1) Install programs

You will need to install the necessary package dependencies required to run Camdram. **PHP version 7.3 or greater is required**.

### Debian/Ubuntu and Derivatives

The command below can be run on recent Debian-based distros (including Ubuntu and the [Windows Subsystem for Linux](https://docs.microsoft.com/en-us/windows/wsl/install-win10)).

```
sudo apt-get install git-core php php-cli composer php-curl php-intl php-sqlite3 php-gd php-json php-mbstring php-xml php-zip
```

### macOS

You will need to have [Homebrew](https://brew.sh/) installed. The following command _appears_ (from minimal testing) to install everything required on recent versions of macOS.

```
brew install git php composer
```

Installation methods on other distros and operating systems will vary.

## 2) Create a local version of Camdram

The steps below assume that the Composer PHP package manager is installed globally on your system. If it is not available in your distro's repositories, alternate installation methods can be found at https://getcomposer.org/download/.

**Note**: some of the SQL database operations performed by the `composer` commands below are memory-intensive. Default installations of PHP tend to set the memory limit of a PHP script to a value which will undoubtedly cause fatal errors. To prevent this, find your `php.ini` file (location varies by system) and change the `memory_limit` setting, to something like `memory_limit = 1G` (a gigabyte is definitely sufficient - you may be able to get away with a lot less if you prefer). Alternatively you can call the PHP interpreter like so:

```
php -d memory_limit=1G $(which composer) etc.......
```

The command below will download and set up a Camdram checkout in a new folder called `camdram`:

```
composer create-project camdram/camdram camdram dev-master --no-interaction --keep-vcs
```

After obtaining a copy of the code, change into the newly created directory and start a local web server:

```
cd camdram
php app/console server:run
```

You should then be able to visit [http://127.0.0.1:8000/](http://127.0.0.1:8000/) in your web browser to see your personal version of Camdram's homepage.

## 3) Run test suite

Camdram has a limited but growing [automated test suite](https://github.com/camdram/camdram/wiki/Running-and-creating-tests), which can be used to ensure your checkout is working and check for certain regressions after making changes. It can be executed by running:

```
./runtests
```

## 4) Create a fork

Camdram's development model follows the standard idioms used by FOSS projects hosted on GitHub. If you are just interested in experimenting with the codebase, no further steps are necessary, but if you'd like to contribute then you will need to [create a fork](https://help.github.com/articles/fork-a-repo).

After creating a personal fork, you can repoint your checkout using the commands below.

```
git remote rename origin upstream
git remote add origin git@github.com:your-github-username/camdram.git
```

We run Camdram as a meritocracy: anyone who has a reasonable number of pull requests accepted will be given access rights to commit straight to the Camdram repository

## 5) Write some code

It is a good idea to create a "feature branch" before starting development, so that the pull request will be named appropriately:

```
git checkout -b my-cool-feature
```

Some useful tips:
 * The site uses the Symfony PHP framework - [read the documentation](http://symfony.com/doc/3.4/index.html).
 * Use the GitHub issue tracker to discover and discuss issues to work on. If you think you know how to do something, write the code, commit it, and
   submit a pull request.
 * If you want to discuss how to implement a new feature or how to fix a bug, get in touch with one of the developers. It would probably be wise to get in
   touch before starting on any significant projects to avoid wasted effort!
 * Visit http://try.github.io/ if you're not familiar with Git.
 * Code should ideally conform to the style guide here: http://www.php-fig.org/psr/psr-2/. If this is far too daunting, a poorly styled but functional improvement is better than no improvement. You can use http://cs.sensiolabs.org/ to (mostly) clean your code up after writing it.

Depending on the type of change, ensure it works as a logged-in and/or non-logged in visitor. You can grant administration rights to your user account (which allows you to create and modify any Camdram content) by running
```
php app/console camdram:admins --grant=youremail@domain.com
```

## 6) Submit your changes

 * Run `git add file1.php file2.php` for each file you wish to include in the commit
 * Run `git commit` and enter a message describing the changes you have made
 * Run `git push` to send your changes to GitHub

It is good practice to include the relevant issue number (prefixed with a hash #) at the end of the commit message - this will cause your commit to be linked to the issue page on GitHub.

Once your changes are pushed to your Camdram fork on GitHub, you can [submit a pull request](https://help.github.com/articles/creating-a-pull-request/) to have it included in Camdram.

**Please note:** we automatically display the names of some contributors on our [development page](https://www.camdram.net/development/) as a way of saying thanks! By submitting your changes you are acknowledging that you are happy for this to happen.

## 7) Pull in other people's changes

At a later date, once your local repository has become out of sync with Github (because other people have make changes), you can run the following commands to pull in other people's changes and update your checkout:

```
git fetch upstream
git merge upstream/master
```

The following commands may need to be run after the above, if the dependencies, database schema or JS/CSS assets have changed.

```
composer install
php app/console camdram:database:refresh
php app/console camdram:assets:download
```

## 8) Read the Wiki

[The Wiki](http://github.com/camdram/camdram/wiki) has various pieces of information about both the current and in-development versions of Camdram. Reading through those pages can give insight into the more esoteric parts of the system. You can suggest ideas for new articles using the contact details above.

The following wiki pages detail how to create a server set-up that's more similar to the version of Camdram at https://www.camdram.net/:

 * [Setting up for JS/CSS development](https://github.com/camdram/camdram/wiki/Webpack-setup-guide)
 * [Setting up a MySQL database](https://github.com/camdram/camdram/wiki/Setting-up-a-MySQL-database)
 * [Elasticsearch setup guide](https://github.com/camdram/camdram/wiki/Elasticsearch-guide)
 * [External API registration](https://github.com/camdram/camdram/wiki/API-registration)
 * [Setting up an Apache virtual host](https://github.com/camdram/camdram/wiki/Setting-up-an-Apache-virtual-host)

---

### Copyright

Camdram is released under version two of the [GNU General Public License](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html#SEC1).

Copyright (C) 2014–2019 *The Association of Cambridge Theatre Societies* and contributing groups.
