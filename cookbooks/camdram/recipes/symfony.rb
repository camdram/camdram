node['composer']['install_globally'] = false
node['composer']['install_dir'] = '/var/www/camdram'
include_recipe "composer"
execute "install PHP dependencies" do
  command "php composer.phar install"
  action :run
  cwd '/var/www/camdram'
end
