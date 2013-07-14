# The version of PHP that comes with Debian is too old, so we add
# the 'dotdeb' thirdparty repo
#apt_repository "dotdeb" do
#    uri node['dotdeb']['uri']
#    distribution node['dotdeb']['distribution']
#    components ['all']
#    key "http://www.dotdeb.org/dotdeb.gpg"
#    action :add
#end

#Do an update as we've just added a new software source
#execute "update apt sources" do
#    command "apt-get update"
#    action :run
#end

#Set up additional php configuration options
#node['php']['directives'] = {'date.timezone' => 'Europe/London'}

include_recipe "php"

#Install the required PHP extensions
%w{php5-curl php5-intl php5-mysql php-apc php5-gd php5-imagick}.each do |pkg|
  package pkg
end

#Install Apache and PHP
node['apache']['user'] = 'vagrant'
node['apache']['group'] = 'vagrant'
include_recipe "apache2"
include_recipe "apache2::mod_php5"

#Set up the virtual host
web_app 'camdram' do 
  server_name node['camdram']['servername']
  docroot node['camdram']['docroot']
end

template "/etc/php5/cli/php.ini" do
  source "php-cli.ini.erb"
end

template "/etc/php5/apache2/php.ini" do
  source "php-apache2.ini.erb"
end

execute "restart Apache" do
  command "sudo service apache2 restart"
  action :run
end
