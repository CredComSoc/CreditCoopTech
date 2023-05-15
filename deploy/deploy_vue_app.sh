#NB Do this first!
# sudo cp vue-app.conf /etc/apache2/sites-available/
# npm run build

#  deploy dist to relevant dir

rm -rf /var/www/dist;
cp -rf dist /var/www;
chmod -R 755 /var/www/dist/;
chown -R www-data:www-data /var/www/dist;
