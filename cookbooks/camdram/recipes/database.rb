pass = node['camdram']['db_password']

#Install MySQL
node['mysql']['server_root_password'] = pass
node['mysql']['server_debian_password'] = pass
node['mysql']['server_repl_password'] = pass
include_recipe "mysql::server"
include_recipe "mysql::client"

# The chef 'database' cookbook used below requires the ruby mysql library, 
# so we have to jump through a few hoops to install it...
node.set['build_essential']['compiletime'] = true
include_recipe "build-essential"
node['mysql']['client']['packages'].each do |mysql_pack|
  resources("package[#{mysql_pack}]").run_action(:install)
end
chef_gem "mysql"

db_info = {:host => 'localhost', :username => 'root', :password => pass}

mysql_database 'camdram' do
  connection db_info
  action :create
end
mysql_database 'camdram_test' do
  connection db_info
  action :create
end

mysql_database_user 'camdram' do
  connection db_info
  provider Chef::Provider::Database::MysqlUser
  password pass
  database_name 'camdram'
  action :create
end
mysql_database_user 'camdram' do
  database_name 'camdram'
  action :grant
end
mysql_database_user 'camdram_test' do
  connection db_info
  provider Chef::Provider::Database::MysqlUser
  password pass
  database_name 'camdram_test'
  action :create
end
mysql_database_user 'camdram_test' do
  database_name 'camdram_test'
  action :grant
end

#Install phpmyadmin
package 'phpmyadmin'
