sudo cp backend-le-ssl.conf /etc/apache2/sites-available/
ln -s -f /etc/apache2/sites-available/backend-le-ssl.conf /etc/apache2/sites-enabled/backend-le-ssl.conf
sudo cp vue-app-le-ssl.conf /etc/apache2/sites-available/
ln -s -f /etc/apache2/sites-available/vue-app-le-ssl.conf /etc/apache2/sites-enabled/vue-app-le-ssl.conf
# sudo cp 000-default.conf /etc/apache2/sites-available/
# ln -s -f /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf
apache2ctl graceful