#!/bin/sh

# If you would like to do some extra provisioning you may
# add any commands you wish to this file and they will
# be run after the Homestead machine is provisioned.
echo "Running after.sh"
cp /vagrant/phinx.yml.dist /vagrant/phinx.yml
cp /home/vagrant/cfp.dev/config/development.dist.yml /home/vagrant/cfp.dev/config/development.yml
cd /vagrant
vendor/bin/phinx migrate --environment=development
