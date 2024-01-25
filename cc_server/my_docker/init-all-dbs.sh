#!/usr/bin/bash

echo $CC_DATABASES

# create databases
for DB in $CC_DATABASES; do
  mariadb -u root --password=$MARIADB_ROOT_PASSWORD -e "CREATE DATABASE \`$DB\`;"
  mariadb -u root --password=$MARIADB_ROOT_PASSWORD -D "$DB" < /custom-init-scripts/install.sql
done 

# grant root user privileges
mariadb -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';" --password=$MARIADB_ROOT_PASSWORD
