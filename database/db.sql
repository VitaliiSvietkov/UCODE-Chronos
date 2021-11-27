CREATE DATABASE IF NOT EXISTS chronos;

CREATE USER IF NOT EXISTS 'vsvietkov'@'localhost' IDENTIFIED BY 'Securepass1!';

GRANT All Privileges ON chronos.* TO 'vsvietkov'@'localhost';

USE chronos;
