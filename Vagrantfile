# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

MEMORY = 2048
CPU_COUNT = 2

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "chef/debian-7.4"
  
  config.vm.synced_folder  ".", "/vagrant", disabled: true
  config.ssh.insert_key = true

  config.vm.network :private_network, ip: "192.168.33.10"
  config.hostsupdater.aliases = ["videos.c3sl.localhost"]

  config.vm.provider :virtualbox do |vb|
    vb.customize ["modifyvm", :id, "--memory", MEMORY.to_s]
    vb.customize ["modifyvm", :id, "--cpus", CPU_COUNT.to_s]

    # Allow DNS to work for Ubuntu 12.10 host
    # http://askubuntu.com/questions/238040/how-do-i-fix-name-service-for-vagrant-client
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
  end  
end
