Camdram.net
========================

The easiest way to get started with Camdram development is to use Vagrant 
(http://www.vagrantup.com/), which automatically set up a correctly-configured
virtual machine.

1) Install programs
--------------------

In addition to Vagrant, camdram uses NFS to set up a shared folder between the 
two machines, and Git to mange to the source code.
* [VirtualBox][1] - virtualizaion
* Vagrant - for configuring development environments
* Git - source code management
* NFs 

###Debian-based distributions, e.g. Ubuntu

    $ sudo apt-get install vagrant nfs-kernel-server git-core

###RPM-based distributions, e.g. Fedora

    $ sudo yum install VirtualBox nfs-utils git-core

The current version of Fedora (19) doesn't include vagrant in its package repository.
Download the latest version of Vagrant from the [downloads][2] page and install, e.g.

    $ wget http://files.vagrantup.com/packages/a40522f5fabccb9ddabad03d836e120ff5d14093/vagrant_1.3.5_x86_64.rpm
    $ sudo yum install vagrant_1.3.5_x86_64.rpm`

1) Create a checkout of the Camdram repository
----------------------------------------------

Create an account on Github, then 'fork' this repository using the link above.

Run `git clone https://github.com/YOUR-GITHUB-USERNAME/camdram.git`, which will pull a copy of
the code into a new folder called 'camdram'

3) Start up the virtual machine
-------------------------------

Navigate to the folder with the source code and start up the virtual machine

    cd camdram
    vagrant up
    
The initial set-up will take a little while (it has to download a ~300 Mb virtual
machine image and install the necessary programs). When you've finished work for
the time being, run `vagrant suspend` to suspend the virtual machine. `vagrant up`
will start up the machine again.

4) Add a line to /etc/hosts
---------------------------

The virtual machine contains a virtual host which expects the hostname 
`YOUR-USERNAME.camdram.net`. Add a line similar to the following to /etc/hosts to
make this hostname point to the virtual machine.

    `192.168.150.10         joe.camdram.net`


3) Write some code
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

[1]: http://www.virtualbox.org/
[2]: http://downloads.vagrantup.com/
