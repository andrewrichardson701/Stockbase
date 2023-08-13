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

-- Set the users and users_roles auto_increment to 0 - this is to allow the root user and root role to be ID=0
ALTER TABLE users AUTO_INCREMENT = 0;
ALTER TABLE users_roles AUTO_INCREMENT = 0;

-- Set all other tables to be increment 1
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



-- Add config_default to the table 
INSERT INTO config_default 
(banner_color, logo_image, favicon_image, ldap_enabled, ldap_username, ldap_password, 
ldap_domain, ldap_host, ldap_port, ldap_basedn, ldap_usergroup, ldap_userfilter, currency, 
sku_prefix, smtp_host, smtp_port, smtp_encryption, smtp_password, smtp_from_email, 
smtp_from_name, smtp_to_email, smtp_username, system_name, ldap_host_secondary)
VALUES ('#E1B12C', 'default/default-logo.png', 'default/default-favicon.png', 1, 'ldapauth', 
'RHJvcHNCdWlsZHNTa2lsbDEyISE=', 'ajrich.co.uk', '10.0.2.2', 389, 'DC=ajrich,DC=co,DC=uk', 
'cn=Users', '(objectClass=User)', '£', 'ITEM-', 'mail.ajrich.co.uk', 587, 'starttls', 'RGVtb1Bhc3MxIQ==',
'inventory@ajrich.co.uk', 'Inventory System', 'inventory@ajrich.co.uk', 'inventory@ajrich.co.uk', 'Inventory System', '10.0.2.2');

-- Add user roles to the user roles table
INSERT INTO users_roles (id, name, description, is_admin, is_root) 
VALUES (0, 'Root', 'Root role for the default Root user ONLY.', 1, 1);
VALUES (1, 'User', 'Default group for normal Users.', 0, 0);
VALUES (2, 'Admin', 'Administrator role for any Administrator users.', 1, 0);

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