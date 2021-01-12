Vagrant.require_version ">= 2.1.4"

Vagrant.configure("2") do |config|
  config.vagrant.plugins = ["vagrant-hostmanager"]

  config.env.enable
  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ip_resolver = proc do |vm, resolving_vm|
    if vm.id
      `VBoxManage guestproperty get #{vm.id} "/VirtualBox/GuestInfo/Net/1/V4/IP"`.split()[1]
    end
  end

  config.vm.box = "camurphy/cappuccino"
  config.vm.network "private_network", type: "dhcp"
  config.vm.hostname = "bootstrap-menu-bundle.wip"
  config.vm.synced_folder ".", "/var/www", type: "nfs"
end
