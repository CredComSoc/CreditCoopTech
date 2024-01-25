# create databases
CREATE DATABASE IF NOT EXISTS `cc-server`;

# create root user and grant rights
# CREATE USER 'root'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%';
