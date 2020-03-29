# create databases
CREATE DATABASE IF NOT EXISTS `hubble`;

# create root user and grant rights
GRANT ALL ON *.* TO 'root'@'%';
