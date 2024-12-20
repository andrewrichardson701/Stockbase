-- MySQL Extras
--
-- Host: localhost    Database: stockbase
-- ------------------------------------------------------
-- Server version	8.0.33-0ubuntu0.20.04.2


--
-- Current Database: `stockbase`
--
USE stockbase;

-- Set Auto_increment for all tables
-- Set all tables to be increment 1

ALTER TABLE area AUTO_INCREMENT = 1;
ALTER TABLE cable_item AUTO_INCREMENT = 1;
ALTER TABLE cable_transaction AUTO_INCREMENT = 1;
ALTER TABLE cable_types AUTO_INCREMENT = 1;
ALTER TABLE changelog AUTO_INCREMENT = 1;
ALTER TABLE config AUTO_INCREMENT = 1;
ALTER TABLE config_default AUTO_INCREMENT = 1;
ALTER TABLE item AUTO_INCREMENT = 1;
ALTER TABLE manufacturer AUTO_INCREMENT = 1;
ALTER TABLE notifications AUTO_INCREMENT = 1;
ALTER TABLE optic_comment AUTO_INCREMENT = 1;
ALTER TABLE optic_connector AUTO_INCREMENT = 1;
ALTER TABLE optic_distance AUTO_INCREMENT = 1;
ALTER TABLE optic_item AUTO_INCREMENT = 1;
ALTER TABLE optic_speed AUTO_INCREMENT = 1;
ALTER TABLE optic_transaction AUTO_INCREMENT = 1;
ALTER TABLE optic_type AUTO_INCREMENT = 1;
ALTER TABLE optic_vendor AUTO_INCREMENT = 1;
ALTER TABLE password_reset AUTO_INCREMENT = 1;
ALTER TABLE session_log AUTO_INCREMENT = 1;
ALTER TABLE shelf AUTO_INCREMENT = 1;
ALTER TABLE site AUTO_INCREMENT = 1;
ALTER TABLE stock AUTO_INCREMENT = 1;
ALTER TABLE stock_img AUTO_INCREMENT = 1;
ALTER TABLE stock_tag AUTO_INCREMENT = 1;
ALTER TABLE tag AUTO_INCREMENT = 1;
ALTER TABLE theme AUTO_INCREMENT = 1;
ALTER TABLE transaction AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE users_roles AUTO_INCREMENT = 1;


-- Add config_default to the table 
INSERT INTO config_default 
(banner_color, logo_image, favicon_image, ldap_enabled, ldap_username, ldap_password, 
ldap_domain, ldap_host, ldap_port, ldap_basedn, ldap_usergroup, ldap_userfilter, currency, 
sku_prefix, smtp_host, smtp_port, smtp_encryption, smtp_password, smtp_from_email, 
smtp_from_name, smtp_to_email, smtp_username, system_name, ldap_host_secondary, base_url, smtp_enabled, default_theme_id,
cost_enable_normal, cost_enable_cable, footer_enable, footer_left_enable, footer_right_enable, 2fa_enabled, 2fa_enforced, signup_allowed)
VALUES ('#E1B12C', 'default/default-logo.png', 'default/default-favicon.png', 0, 'ldapusername', 
'SUPERSECRETPASSWORD', 'domain.com', '127.0.0.1', 389, 'DC=domain,DC=com', 
'cn=Users', '(objectClass=User)', '£', 'ITEM-', 'mail.domain.com', 587, 'starttls', 'SUPERSECRETPASSWORD',
'stockbase@domain.com', 'StockBase', 'stockbase@domain.com', 'stockbase@domain.com', 'StockBase', '127.0.0.1', 
'stockbase.domain.com', 0, 1, 1, 1, 1, 1, 1, 0, 0, 0);

-- Duplicaye the config_default table to config table
INSERT INTO config SELECT * FROM config_default;

-- Add user roles to the user roles table
INSERT INTO users_roles (id, name, description, is_optic, is_admin, is_root) 
VALUES  
    (1, 'User', 'Default group for normal Users.', 0, 0, 0),    
    (2, 'Admin', 'Administrator role for any Administrator users.', 1, 1, 0),
    (3, 'Optics User', 'Users with access to Optics stock.', 1, 0, 0),
    (4, 'Root', 'Root role for the default Root user ONLY.', 1, 1, 1);
UPDATE users_roles SET id=0 where id=4;
ALTER TABLE users_roles AUTO_INCREMENT = 4;

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
 
INSERT INTO notifications (id, name, title, description, enabled) 
VALUES 
    (1, 'stock-added', 'Stock Added', 'Adding stock to the system.', 1),
    (2, 'stock-removed', 'Stock Removed', 'Removing stock from the system.', 1),
    (3, 'stock-deleted', 'Stock Deleted', 'Deleting stock from the system.', 1),
    (4, 'stock-deleted-restore', 'Stock Restored', 'Restoring stock from deletion within the system.', 1),
    (5, 'stock-moved', 'Stock Moved', 'Moving stock within the system.', 1),
    (6, 'stock-edited', 'Stock Edited', 'Editing of stock details within the system.', 1),
    (7, 'stock-images', 'Stock Image Linking', 'Modification of stock image linking within the system.', 1),
    (8, 'cablestock-added', 'Cable Stock Added', 'Adding cable stock to the system.', 1),
    (9, 'cablestock-removed', 'Cable Stock Removed', 'Removing cable stock from the system.', 1),
    (10, 'cablestock-moved', 'Cable Stock Moved', 'Moving cable stock from the system.', 1),
    (11, 'optic-added', 'Optic Stock Added', 'Adding an optic to the system.', 1),
    (12, 'optic-removed', 'Optic Stock Removed', 'Removing an optic from the system.', 1),
    (13, 'optic-moved', 'Optic Stock Moved', 'Moving an optic from the system.', 1),
    (14, 'minstock-warning', 'Minimum Stock Warnings', 'Warning for stock being below the minimum stock count.', 1),
    (15, 'important', 'Important', 'Important notifications. These need to be enabled', 1);

UPDATE notifications SET id=0 WHERE id=15;
ALTER TABLE notifications AUTO_INCREMENT = 15;

INSERT INTO theme (id, name, file_name)
VALUES
    (1, 'Dark', 'theme-dark.css'),
    (2, 'Light', 'theme-light.css'),
    (3, 'Light Blue', 'theme-light-blue.css'),
    (4, 'Dark Red', 'theme-dark-red.css');

INSERT INTO optic_type (name)
VALUES
    ('SFP'),
    ('SFP+');
    
INSERT INTO optic_connector (name)
VALUES 
    ('LC'),
    ('SC'),
    ('FC'),
    ('ST'),
    ('RJ45');

INSERT INTO optic_speed (name)
VALUES 
    ('100M'),
    ('1G'),
    ('4G'),
    ('8G'),
    ('10G'),
    ('25G'),
    ('40G'),
    ('50G'),
    ('100G'),
    ('200G'),
    ('400G'),
    ('800G');