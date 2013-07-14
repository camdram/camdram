# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|

  config.vm.box = "precise32"
  config.vm.box_url = "https://dl.dropboxusercontent.com/u/2289657/squeeze32-vanilla.box"

  config.vm.network :hostonly, "192.168.150.10"

  config.vm.share_folder "www", "/var/www/camdram", ".", :nfs => true
  config.vm.customize [
    "setextradata",
    :id,
    "VBoxInternal2/SharedFoldersEnableSymlinksCreate/www",
    "1"
  ]

  config.vm.provision :chef_solo do |chef|
    chef.json = {
      :camdram => { :servername => ENV['USER'] + '.camdram.net' }
    }
    #chef.cookbooks_path = ['.', 'cookbooks', 'chef-cookbooks-master']
    #chef.recipe_url = 'http://peter.camdram.net/cookbooks.tar.gz'
    chef.add_recipe "camdram"
  end

end
