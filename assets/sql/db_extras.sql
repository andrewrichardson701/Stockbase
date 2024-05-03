-- MySQL Extras
--
-- Host: localhost    Database: inventory
-- ------------------------------------------------------
-- Server version	8.0.33-0ubuntu0.20.04.2


--
-- Current Database: `inventory`
--
USE inventory;

-- Set Auto_increment for all tables
-- Set all tables to be increment 1
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE users_roles AUTO_INCREMENT = 1;
ALTER TABLE area AUTO_INCREMENT = 1;
ALTER TABLE cable_item AUTO_INCREMENT = 1;
ALTER TABLE cable_transaction AUTO_INCREMENT = 1;
ALTER TABLE cable_types AUTO_INCREMENT = 1;
ALTER TABLE config AUTO_INCREMENT = 1;
ALTER TABLE config_default AUTO_INCREMENT = 1;
ALTER TABLE item AUTO_INCREMENT = 1;
ALTER TABLE label AUTO_INCREMENT = 1;
ALTER TABLE manufacturer AUTO_INCREMENT = 1;
ALTER TABLE shelf AUTO_INCREMENT = 1;
ALTER TABLE site AUTO_INCREMENT = 1;
ALTER TABLE stock AUTO_INCREMENT = 1;
ALTER TABLE stock_img AUTO_INCREMENT = 1;
ALTER TABLE stock_label AUTO_INCREMENT = 1;
ALTER TABLE transaction AUTO_INCREMENT = 1;


-- Add blank config to config table ready to be edited
INSERT INTO config (id) VALUES (1);

-- Add config_default to the table 
INSERT INTO config_default 
(banner_color, logo_image, favicon_image, ldap_enabled, ldap_username, ldap_password, 
ldap_domain, ldap_host, ldap_port, ldap_basedn, ldap_usergroup, ldap_userfilter, currency, 
sku_prefix, smtp_host, smtp_port, smtp_encryption, smtp_password, smtp_from_email, 
smtp_from_name, smtp_to_email, smtp_username, system_name, ldap_host_secondary, base_url)
VALUES ('#E1B12C', 'default/default-logo.png', 'default/default-favicon.png', 0, 'ldapusername', 
'SUPERSECRETPASSWORD', 'domain.com', '127.0.0.1', 389, 'DC=domain,DC=com', 
'cn=Users', '(objectClass=User)', '£', 'ITEM-', 'mail.domain.com', 587, 'starttls', 'SUPERSECRETPASSWORD',
'stockbase@domain.com', 'StockBase', 'stockbase@domain.com', 'stockbase@domain.com', 'StockBase', '127.0.0.1', 
'stockbase.domain.com');

-- Add user roles to the user roles table
INSERT INTO users_roles (id, name, description, is_admin, is_root) 
VALUES  
    (1, 'User', 'Default group for normal Users.', 0, 0),    
    (2, 'Admin', 'Administrator role for any Administrator users.', 1, 0),
    (3, 'Root', 'Root role for the default Root user ONLY.', 1, 1);
UPDATE users_roles SET id=0 where id=3;
ALTER TABLE users_roles AUTO_INCREMENT = 3;

INSERT INTO cable_types (id, name, description, parent)
VALUES
    (1, 'Copper', 'Generic Copper Cable', 'Copper'),
    (2, 'Cat5e', 'Cat5e Copper Cable', 'Copper'),
    (3, 'Cat6', 'Cat6 Copper Cable', 'Copper'),
    (4, 'Fibre', 'Generic Fibre Cable', 'Fibre'),
    (5, 'SM LC-LC', 'Single Mode LC to LC Fibre Cable', 'Fibre'),
    (6, 'SM SC-SC', 'Single Mode SC to SC Fibre Cable', 'Fibre'),
    (7, 'SM LC-SC', 'Single Mode LC to SC Fibre Cable', 'Fibre'),
    (8, 'MM LC-LC', 'Multi Mode LC to LC Fibre Cable', 'Fibre'),
    (9, 'MM SC-SC', 'Multi Mode SC to SC Fibre Cable', 'Fibre'),
    (10, 'MM LC-SC', 'Multi Mode LC to SC Fibre Cable', 'Fibre'),
    (11, 'Power', 'Generic Power Cable', 'Power'),
    (12, 'Other', 'Other Generic Cable', 'Other');
