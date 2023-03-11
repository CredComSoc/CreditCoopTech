sudo cp vue-app.conf /etc/apache2/sites-available/
ln -s -f /etc/apache2/sites-available/vue-app.conf /etc/apache2/sites-enabled/vue-app.conf
apache2ctl graceful