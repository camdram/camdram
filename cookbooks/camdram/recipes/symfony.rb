node['composer']['install_globally'] = false
include_recipe "composer"
execute "install PHP composer for Symfony dependency management" do
  command "php composer.phar install"
  action :run
  cwd node['camdram']['docroot']
end
