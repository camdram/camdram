#node['sphinx']['use_package'] = false
#node['sphinx']['version'] = node['camdram']['sphinx_version']
#include_recipe "sphinx"

package "sphinxsearch"

symfony2_console "Generate Sphinx configuration" do
  action :cmd
  command "acts:sphinx:config"
  path "/var/www/camdram"
end

template "/etc/default/sphinxsearch" do
  source "sphinxsearch.erb"
end

execute "create symlink to Sphinx configuration" do
  command "ln -sf /var/www/camdram/app/config/sphinx.default.cfg /etc/sphinxsearch/sphinx.conf"
  action :run
end

directory "/var/log/searchd/" do
  owner 'sphinxsearch'
  group 'root'
  action :create
end

execute "stop Sphinx" do
  command "sudo service sphinxsearch stop"
  action :run
end

directory "/var/www/camdram/app/data/sphinx" do
  action :delete
  recursive true
end
directory "/var/www/camdram/app/data/sphinx" do
  action :create
end

execute "start Sphinx" do
  command "sudo service sphinxsearch start"
  action :run
end
