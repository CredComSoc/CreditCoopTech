systemctl stop apache2.service
systemctl stop mysql.service
systemctl stop backend.service
git pull
npm run build 

rm -rf /var/www/dist;
cp -rf dist /var/www;
chmod -R 755 /var/www/dist/;
chown -R www-data:www-data /var/www/dist;

systemctl start backend.service
systemctl start apache2.service
systemctl start mysql.service