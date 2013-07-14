# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|

  config.vm.box = "precise32"
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"

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
    chef.add_recipe "camdram"
  end

end
