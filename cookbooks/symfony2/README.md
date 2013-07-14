Description
===========

This cookbook runs the Symfony2 console command.
Adapted from Digital Pioneers N.V.


Requirements
============

Platform
--------

* Debian, Ubuntu
* CentOS, Red Hat, Fedora


Attributes
==========

## User and group to run the command as
    default[:symfony2][:user]  = node[:apache][:user]
    default[:symfony2][:group] = node[:apache][:group]

Usage
=====

## Run ./console doctrine:database:create

    symfony2_console "Create database" do
      action :cmd
    
      command "doctrine:database:create"
    
      path node[:zym_app][:dir]
    end

## Run ./console doctrine:schema:create

    symfony2_console "Create schema" do
      action :cmd
    
      command "doctrine:schema:create"
    
      path node[:zym_app][:dir]
    end

## Run ./console doctrine:fixtures:load

    symfony2_console "Load fixtures" do
      action :cmd
    
      command "doctrine:fixtures:load"
    
      path node[:zym_app][:dir]
    end

License and Author
==================

Author:: Geoffrey Tran (<geoffrey.tran@gmail.com>)
Author:: Florian Holzhauer (<f.holzhauer@digitalpioneers.de>)
Author:: Ole Michaelis (<o.michaelis@digitalpioneers.de>)
Author:: Michael Kamphausen (<m.kamphausen@digitalpioneers.de>)

Copyright:: 2012, Digital Pioneers N.V.
Copyright:: 2012, Geoffrey Tran <http://geoffreytran.com>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.


