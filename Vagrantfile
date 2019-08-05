# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

  config.vm.box = "ubuntu/bionic64"
  config.vm.hostname = "printerwatchdog"
  config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"
  config.vm.network "forwarded_port", guest: 8000, host: 8081, host_ip: "127.0.0.1"
  config.vm.network "forwarded_port", guest: 3306, host: 3307, host_ip: "127.0.0.1"
  # config.vm.network "public_network"

  config.vm.provider "virtualbox" do |vb|
    vb.gui = false
    vb.memory = "2048"
    vb.cpus = 2
    vb.name = "printerwatchdog"
  end

  config.vm.provision "shell", inline: <<-SHELL
     apt-get update
     #apt-get upgrade -y

     mkdir /tmp/apc
     mount -t ramfs ramfs /tmp/apc
     echo "ramfs   /tmp/apc     ramfs   defaults        0       0" >> /etc/fstab


     apt-get install -y php-cli php-snmp php-xml php-curl php-json php-mysql php-zip php-apcu php-xdebug php-fpm mysql-server mysql-client unzip nginx

     sudo mysql -u root -e "CREATE DATABASE IF NOT EXISTS PrinterWatchdog;"
     sudo mysql -u root -e "CREATE USER 'pwdog'@'localhost' IDENTIFIED BY 'pwdog';"
     sudo mysql -u root -e "GRANT ALL PRIVILEGES ON PrinterWatchdog.* TO 'pwdog'@'localhost';"

     mkdir /vagrant/.debug
     mkdir /vagrant/.debug/log
     mkdir /vagrant/xdebug-profiles
     mkdir /vagrant/xdebug-traces

     echo 'server {
               gzip on;
               access_log /vagrant/.debug/log/access.log combined;
               error_log /vagrant/.debug/log/error.log warn;

               listen         80 default_server;
               listen         [::]:80 default_server;
               server_name    printerwatchdog printerwatchdog.local;
               root           /vagrant/public;
               index          index.php;

           location / {
                    try_files $uri $uri/ /index.php$is_args$args;
               }

           location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.2-fpm.sock;
                include         fastcgi_params;
                fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
                fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
           }

           location ~ /\.ht {
                deny all;
            }
           }' > /etc/nginx/sites-available/printerwatchdog
     ln -s /etc/nginx/sites-available/printerwatchdog /etc/nginx/sites-enabled/printerwatchdog
     rm /etc/nginx/sites-enabled/default
     service nginx restart


     echo 'zend_extension=xdebug.so
           xdebug.default_enable = 1
           xdebug.idekey = "PHPSTORM"
           xdebug.remote_enable = 1
           xdebug.remote_autostart = 0
           xdebug.remote_connect_back = 1
           xdebug.remote_port = 9000
           xdebug.remote_host = "localhost"
           xdebug.remote_handler=dbgp
           xdebug.remote_log="/vagrant/.debug/log/xdebug/xdebug.log"
           xdebug.profiler_output_dir = "/vagrant/.debug/xdebug-profiles/"
           xdebug.profiler_enable = 0
           xdebug.profiler_enable_trigger = 1
           xdebug.profiler_enable_trigger_value = "PROFILE_PHPSTORM"
           xdebug.trace_enable_trigger = 1
           xdebug.trace_enable_trigger_value = "TRACE_PHPSTORM"
           xdebug.trace_output_dir = "/vagrant/.debug/xdebug-traces/"
           xdebug.collect_params = 4
           xdebug.collect_return = 1
           xdebug.auto_trace = 0' > /etc/php/7.2/mods-available/xdebug.ini
     service php7.2-fpm restart

     wget https://get.symfony.com/cli/installer -O - | bash
     mv /root/.symfony/bin/symfony /usr/local/bin/symfony

     php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
     php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
     php composer-setup.php
     php -r "unlink('composer-setup.php');"
     mv /home/vagrant/composer.phar /usr/local/bin/composer

     cd /vagrant/public
     wget https://raw.githubusercontent.com/krakjoe/apcu/master/apc.php

     cd /vagrant
     composer install

     php bin/console doctrine:schema:create -n --env=dev
     php bin/console doctrine:fixtures:load -n

     ########################################################
     ## You also can use the local Symfony Server by
     ## running the followed commands in /vagrant folder
     ##
     # symfony server:ca:install
     # symfony server:start -d

  SHELL
end
