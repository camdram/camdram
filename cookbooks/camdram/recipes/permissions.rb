package "acl"

execute "remount disk with acl permissions" do
  command "mount -o remount,acl /var/www/camdram"
  action :run
end

execute "setting permissions of cache and log directories" do
  command "sudo setfacl -R -m u:www-data:rwX -m u:vagrant:rwX app/cache app/logs"
  action :run
  cwd node['camdram']['docroot']
end

execute "setting permissions of cache and log directories" do
  command "sudo setfacl -R -m u:www-data:rwX -m u:vagrant:rwX app/cache app/logs"
  action :run
  cwd node['camdram']['docroot']
end
