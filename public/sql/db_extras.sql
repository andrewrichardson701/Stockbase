-- MySQL Extras
--
-- Host: localhost    Database: stockbase
-- ------------------------------------------------------
-- Server version	8.0.33-0ubuntu0.20.04.2


--
-- Current Database: `stockbase`
--
USE stockbase;

-- Add config_default to the table 
INSERT INTO `config_default` VALUES (1,'#E1B12C','default/default-logo.png','default/default-favicon.png',0,'ldapusername','SUPERSECRETPASSWORD','domain.com','127.0.0.1','127.0.0.1',389,'DC=domain,DC=com','cn=Users','(objectClass=User)','ITEM-','£','mail.domain.com',587,'starttls','stockbase@domain.com','SUPERSECRETPASSWORD','stockbase@domain.com','StockBase','stockbase@domain.com',NULL,NULL,NULL,'basic',NULL,0,'StockBase','stockbase.local',1,0,0,1,1,1,0,0,0,NULL,'2025-08-12 05:35:10');

-- Add config_default to the config table 
INSERT INTO `config` VALUES (1,'#E1B12C','default/default-logo.png','default/default-favicon.png',0,'ldapusername','SUPERSECRETPASSWORD','domain.com','127.0.0.1','127.0.0.1',389,'DC=domain,DC=com','cn=Users','(objectClass=User)','ITEM-','£','mail.domain.com',587,'starttls','stockbase@domain.com','SUPERSECRETPASSWORD','stockbase@domain.com','StockBase','stockbase@domain.com',NULL,NULL,NULL,'basic',NULL,0,'StockBase','stockbase.local',1,0,0,1,1,1,0,0,0,NULL,'2025-08-12 05:35:10');


-- Add default email_templates_default
INSERT INTO `email_templates_default` VALUES 
    (1,'Welcome','welcome_email','Welcome to ##SYSTEM_NAME##','Welcome to <a href=\"##BASE_URL##\">##SYSTEM_NAME##</a>.','Welcome email, sent to a user after their first login/sign up.','[##USER_NAME##, ##BASE_URL##, ##SYSTEM_NAME##]',1,'2025-07-20 18:48:13','2025-08-05 16:47:15'),
    (2,'Stock Added','stock_added','Stock Added - ##STOCK_NAME##.','Stock added, for <strong>##STOCK_URL##</strong> in <strong>##SITE_NAME##</strong>, <strong>##AREA_NAME##</strong>, <strong>##SHELF_NAME##!</strong><br>\nStock count added: <strong>##QUANTITY##</strong><br>\nNew stock count: <strong>##NEW_QUANTITY##</strong>.','Stock added notification to let the user know what they added and how many.','[##STOCK_NAME##, ##STOCK_URL##, ##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##, ##QUANTITY##, ##NEW_QUANTITY##]',1,'2025-08-03 19:45:40','2025-08-03 20:42:59'),
    (3,'New Stock','stock_added_new','New Stock Added - ##STOCK_NAME##','New stock item added: <strong>##STOCK_URL##</strong>.<br><br> Details:<br> <table style=\"margin:auto\"> <thead> <tr><th style=\"text-align:right\">ID:</th><td style=\"text-align:left\">##STOCK_ID##</td></tr> <tr><th style=\"text-align:right\">Name:</th><td style=\"text-align:left\">##STOCK_NAME##</td></tr> <tr><th style=\"text-align:right\">Description:</th><td style=\"text-align:left\">##STOCK_DESCRIPTION##</td></tr> <tr><th style=\"text-align:right\">SKU:</th><td style=\"text-align:left\">##STOCK_SKU##</td></tr> <tr><th style=\"text-align:right\">Min. Stock:</th><td style=\"text-align:left\">##STOCK_MIN_STOCK##</td></tr> </thead> </table>','New stock item added notification to let the user know what they added and how many.','[##STOCK_URL##, ##STOCK_ID##, ##STOCK_NAME##, ##STOCK_DESCRIPTION##, ##STOCK_SKU##, ##STOCK_MIN_STOCK##]',1,'2025-08-04 11:54:01','2025-08-05 16:31:18'),
    (4,'Stock Removed','stock_removed','Stock Removed - ##STOCK_NAME##','Stock removed, from <strong>##STOCK_URL##</strong> in <strong>##SITE_NAME##</strong>, <strong>##AREA_NAME##</strong>, <strong>##SHELF_NAME##!</strong><br> Stock count removed: <strong>##QUANTITY##</strong><br> New stock count: <strong>##NEW_QUANTITY##</strong>.','Stock removed notification to let the user know what they removed and how many.','[##STOCK_NAME##, ##STOCK_URL##, ##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##, ##QUANTITY##, ##NEW_QUANTITY##]',1,'2025-08-04 11:09:15','2025-08-04 17:35:01'),
    (5,'Sock Moved','stock_moved','Stock Moved - ##STOCK_NAME##','Stock moved for <strong>##STOCK_URL##</strong>.<br><br> Quantity Moved: <strong>##QUANTITY##</strong><br> From: <strong>##SITE_NAME_OLD##</strong>, <strong>##AREA_NAME_OLD##</strong>, <strong>##SHELF_NAME_OLD##</strong><br> To: <strong>##SITE_NAME_NEW##</strong>, <strong>##AREA_NAME_NEW##</strong>, <strong>##SHELF_NAME_NEW##</strong><br>','Stock moved notification to let the user know what they moved, where from, to and how many.','[##STOCK_NAME##, ##STOCK_URL##, ##QUANTITY##, ##SITE_NAME_OLD##, ##AREA_NAME_OLD##, ##SHELF_NAME_OLD##, ##SITE_NAME_NEW##, ##AREA_NAME_NEW##, ##SHELF_NAME_NEW##]',1,'2025-08-04 17:35:01','2025-08-04 17:36:24'),
    (6,'Stock Edited','stock_edited','Stock Edited - ##STOCK_NAME##','Stock item edited: <strong>##STOCK_URL##</strong>.<br><br>\nDetails:<br>\n<table style=\"margin:auto\">\n<thead>\n<tr><th style=\"text-align:right\"></th><th style=\"text-align:left\">Old value</th><th style=\"text-align:left\">New value</th></tr>\n<tr><th style=\"text-align:right\">ID:</th><td style=\"text-align:left\">##STOCK_ID##</td><td style=\"text-align:left\">##STOCK_ID##</td></tr>\n<tr><th style=\"text-align:right\">Name:</th><td style=\"text-align:left\">##STOCK_NAME_OLD##</td><td style=\"text-align:left\">##STOCK_NAME_NEW##</td></tr>\n<tr><th style=\"text-align:right\">Description:</th><td style=\"text-align:left\">##STOCK_DESCRIPTION_OLD##</td><td style=\"text-align:left\">##STOCK_DESCRIPTION_NEW##</td></tr>\n<tr><th style=\"text-align:right\">SKU:</th><td style=\"text-align:left\">##STOCK_SKU_OLD##</td><td style=\"text-align:left\">##STOCK_SKU_NEW##</td></tr>\n<tr><th style=\"text-align:right\">Min. Stock:</th><td style=\"text-align:left\">##STOCK_MIN_STOCK_OLD##</td><td style=\"text-align:left\">##STOCK_MIN_STOCK_NEW##</td></tr>\n<tr><th style=\"text-align:right\">Tags:</th><td style=\"text-align:left\">##STOCK_TAGS_OLD##</td><td style=\"text-align:left\">##STOCK_TAGS_NEW##</td></tr>\n</thead>\n</table>','Stock edited notification, showing the before and after information changes.','[##STOCK_URL##, ##STOCK_ID##, ##STOCK_NAME##, ##STOCK_NAME_OLD##, ##STOCK_DESCRIPTION_OLD##, ##STOCK_SK_OLDU##, ##STOCK_TAGS_OLD##, ##STOCK_MIN_STOCK_NEW##, ##STOCK_NAME_NEW##, ##STOCK_DESCRIPTION_NEW##, ##STOCK_SKU_NEW##, ##STOCK_MIN_STOCK_NEW##, ##STOCK_TAGS_NEW##]',1,'2025-08-04 18:02:46','2025-08-04 18:20:52'),
    (7,'Stock Deleted','stock_deleted','Stock Deleted - ##STOCK_NAME##','Stock item deleted: <strong>##STOCK_URL##</strong>.<br><br> Details:<br> <table style=\"margin:auto\"> <thead> <tr><th style=\"text-align:right\">ID:</th><td style=\"text-align:left\">##STOCK_ID##</td></tr> <tr><th style=\"text-align:right\">Name:</th><td style=\"text-align:left\">##STOCK_NAME##</td></tr> <tr><th style=\"text-align:right\">Description:</th><td style=\"text-align:left\">##STOCK_DESCRIPTION##</td></tr> <tr><th style=\"text-align:right\">SKU:</th><td style=\"text-align:left\">##STOCK_SKU##</td></tr> </thead> </table> <br> If this was a mistake, the item can be restored here: ##STOCK_RESTORE_URL##.','Stock deleted notification, showing the information of the item and the option to restore it.','[##STOCK_URL##, ##STOCK_ID##, ##STOCK_NAME##, ##STOCK_DESCRIPTION##, ##STOCK_SKU##, ##STOCK_RESTORE_URL##]',1,'2025-08-05 10:03:37','2025-08-05 10:03:49'),
    (8,'Stock Restored','stock_deleted_restore','Stock Restored - ##STOCK_NAME##','Stock item Restored: <strong>##STOCK_URL##</strong>.<br><br> Details:<br> <table style=\"margin:auto\"> <thead> <tr><th style=\"text-align:right\">ID:</th><td style=\"text-align:left\">##STOCK_ID##</td></tr> <tr><th style=\"text-align:right\">Name:</th><td style=\"text-align:left\">##STOCK_NAME##</td></tr> <tr><th style=\"text-align:right\">Description:</th><td style=\"text-align:left\">##STOCK_DESCRIPTION##</td></tr> <tr><th style=\"text-align:right\">SKU:</th><td style=\"text-align:left\">##STOCK_SKU##</td></tr> </thead> </table>','Stock restoration notification, to show that a stock item is no longer deleted.','[##STOCK_URL##, ##STOCK_ID##, ##STOCK_NAME##, ##STOCK_DESCRIPTION##, ##STOCK_SKU##]',1,'2025-08-05 12:02:46','2025-08-05 12:02:49'),
    (9,'Minimum Stock','min_stock_warning','Stock quantity below minimum level - ##STOCK_NAME##','Stock count is below the minimum level of <strong style=\"color:red\">##STOCK_MIN_STOCK##</strong> with <strong style=\"color:red\">##QUANTITY##</strong>, for stock: <strong>##STOCK_URL##</strong>.<br> Please order more stock to replenish.<br><br> Details:<br> <table style=\"margin:auto\"> <thead> <tr><th style=\"text-align:right\">ID:</th><td style=\"text-align:left\">##STOCK_ID##</td></tr> <tr><th style=\"text-align:right\">Name:</th><td style=\"text-align:left\">##STOCK_NAME##</td></tr> <tr><th style=\"text-align:right\">Description:</th><td style=\"text-align:left\">##STOCK_DESCRIPTION##</td></tr> <tr><th style=\"text-align:right\">SKU:</th><td style=\"text-align:left\">##STOCK_SKU##</td></tr> <tr><th style=\"text-align:right\">Min. stock:</th><td style=\"text-align:left;color:red\">##STOCK_MIN_STOCK##</td></tr> <tr><th style=\"text-align:right\">Quantity:</th><td style=\"text-align:left;color:red\">##QUANTITY##</td></tr> <tr><th style=\"text-align:right\">Location:</th><td style=\"text-align:left;color:red\">##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##</td></tr> </thead> </table>','Minimum stock level prompt when removing stock. To remind you to order more.','[##STOCK_URL##, ##STOCK_ID##, ##STOCK_NAME##, ##STOCK_DESCRIPTION##, ##STOCK_SKU##, ##STOCK_MIN_STOCK##, ##QUANTITY##, ##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##]',1,'2025-08-05 12:18:40','2025-08-05 12:18:44'),
    (10,'Image Linked','image_linked','New image linked to ##STOCK_NAME##','New image to stock item: <strong>##STOCK_URL##</strong>.<br><br> Image, with link id: ##IMAGE_ID## and name: ##IMAGE_NAME##, can be found here: ##IMAGE_URL##','Linking new images to a stock item will notify the user.','[##STOCK_NAME##, ##STOCK_URL##, ##IMAGE_ID##, ##IMAGE_NAME##, ##IMAGE_URL##]',1,'2025-08-05 12:29:47','2025-08-05 12:29:52'),
    (11,'Image Unlinked','image_unlinked','Image unlinked from ##STOCK_NAME##','Image unlinked stock item: <strong>##STOCK_URL##</strong>.<br><br> Image, with link id: ##IMAGE_ID## and name: ##IMAGE_NAME##, can be found here: ##IMAGE_URL##','Unlinking images from the stock item.','[##STOCK_NAME##, ##STOCK_URL##, ##IMAGE_ID##, ##IMAGE_NAME##, ##IMAGE_URL##]',1,'2025-08-05 12:30:59','2025-08-05 12:31:05'),
    (12,'Cable Added','cablestock_added','Cable Stock Added - ##STOCK_NAME##.','Cable stock added, for <strong>##STOCK_URL##</strong> in <strong>##SITE_NAME##</strong>, <strong>##AREA_NAME##</strong>, <strong>##SHELF_NAME##!</strong><br>Stock count added: <strong>##QUANTITY##</strong><br>New stock count: <strong>##NEW_QUANTITY##</strong>.','Cable stock added notification to let the user know what they added and how many.','[##STOCK_NAME##, ##STOCK_URL##, ##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##, ##QUANTITY##, ##NEW_QUANTITY##]',1,'2025-08-05 13:25:17','2025-08-05 16:31:24'),
    (13,'Cable Removed','cablestock_removed','Cable Stock Removed - ##STOCK_NAME##','Cable stock removed, from <strong>##STOCK_URL##</strong> in <strong>##SITE_NAME##</strong>, <strong>##AREA_NAME##</strong>, <strong>##SHELF_NAME##!</strong><br> Stock count removed: <strong>##QUANTITY##</strong><br> New stock count: <strong>##NEW_QUANTITY##</strong>.','Cable stock removed notification to let the user know what they removed and how many.','[##STOCK_NAME##, ##STOCK_URL##, ##SITE_NAME##, ##AREA_NAME##, ##SHELF_NAME##, ##QUANTITY##, ##NEW_QUANTITY##]',1,'2025-08-05 13:25:41','2025-08-05 16:31:27');

-- duplicate the email_templates_default to email_tempaltes 
INSERT INTO email_templates SELECT * FROM email_templates_default;

-- Add Root user
INSERT INTO `users` VALUES 
(1,'Root','root@stockbase.local','root','2025-08-08 08:55:35','$2y$10$wHhK4pR5h6j8uN5FZ0E3eu2k7KQnD7i1JfF9Fzv8uTg3kPZfN7X2e',NULL,NULL,NULL,NULL,'local',1,0,1,'2025-01-08 21:39:57','2025-06-16 14:04:31',0,NULL);

-- Add Root user permissions
INSERT INTO `users_permissions` VALUES 
(1,1,1,1,1,1,1,1,1,1,1,1,1,1,'2025-01-08 21:39:57','2025-01-08 21:39:57');

-- Add user permission roles
INSERT INTO `users_permissions_roles` VALUES 
    (1,'Root',1,1,1,1,1,1,1,1,1,1,1,1,1,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (2,'Admin',0,1,1,1,1,1,1,1,1,1,1,1,1,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (3,'User - All',0,0,1,1,1,1,1,1,1,1,1,1,0,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (4,'User - Stock',0,0,1,1,0,0,0,0,0,0,0,1,0,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (5,'User - Cables',0,0,1,1,1,0,0,0,0,0,0,1,0,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (6,'User - Optics',0,0,1,1,1,1,0,0,0,0,0,1,0,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (7,'User - Assets',0,0,1,1,1,1,1,1,1,1,1,1,0,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (8,'User - Changelog',0,0,1,1,1,1,1,1,1,1,1,1,1,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (9,'No Permissions',0,0,0,0,0,0,0,0,0,0,0,0,0,'2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (10,'Only Changelog',0,0,0,0,0,0,0,0,0,0,0,0,1,'2025-06-16 18:23:30','2025-06-16 18:23:30');

INSERT INTO `cable_types` VALUES
    (1, 'Copper', 'Generic Copper Cable', 'Copper','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (2, 'Cat5e', 'Cat5e Copper Cable', 'Copper','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (3, 'Cat6', 'Cat6 Copper Cable', 'Copper','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (4, 'Fibre', 'Generic Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (5, 'SM LC-LC', 'Single Mode LC to LC Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (6, 'SM SC-SC', 'Single Mode SC to SC Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (7, 'SM LC-SC', 'Single Mode LC to SC Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (8, 'MM LC-LC', 'Multi Mode LC to LC Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (9, 'MM SC-SC', 'Multi Mode SC to SC Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (10, 'MM LC-SC', 'Multi Mode LC to SC Fibre Cable', 'Fibre','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (11, 'Power', 'Generic Power Cable', 'Power','2025-01-08 21:39:57','2025-01-08 21:39:57'),
    (12, 'Other', 'Other Generic Cable', 'Other','2025-01-08 21:39:57','2025-01-08 21:39:57');
 
INSERT INTO `notifications` VALUES 
    (1,'important','Important','Important notifications. These need to be enabled',1,1,NULL,'2025-06-10 16:13:00'),
    (2,'stock-added','Stock Added','Adding stock to the system.',2,1,NULL,NULL),
    (3,'stock-added-new','New Stock Item Added','Adding a new stock item to the system',3,1,NULL,NULL),
    (4,'stock-removed','Stock Removed','Removing stock from the system.',NULL,1,NULL,NULL),
    (5,'stock-moved','Stock Moved','Moving stock within the system.',NULL,1,NULL,NULL),
    (6,'stock-edited','Stock Edited','Editing of stock details within the system.',NULL,1,NULL,NULL),
    (7,'stock-deleted','Stock Deleted','Deleting stock from the system.',NULL,1,NULL,NULL),
    (8,'stock-deleted-restore','Stock Restored','Restoring stock from deletion within the system.',NULL,1,NULL,NULL),
    (9,'minstock-warning','Minimum Stock Warnings','Warning for stock being below the minimum stock count.',NULL,1,NULL,NULL),
    (10,'stock-images','Stock Image Linking','Modification of stock image linking within the system.',NULL,1,NULL,NULL),
    (11,'cablestock-added','Cable Stock Added','Adding cable stock to the system.',NULL,1,NULL,NULL),
    (12,'cablestock-removed','Cable Stock Removed','Removing cable stock from the system.',NULL,1,NULL,NULL);


INSERT INTO `theme` VALUES 
    (1,'Dark Laravel','theme-dark-laravel.css',NULL,NULL),
    (2,'Dark','theme-dark.css',NULL,NULL),
    (3,'Light','theme-light.css',NULL,NULL),
    (4,'Light Blue','theme-light-blue.css',NULL,NULL),
    (5,'Dark Red','theme-dark-red.css',NULL,NULL),
    (6,'Dark Black','theme-dark-black.css',NULL,NULL),
    (7,'Citrus','theme-citrus.css',NULL,NULL);

INSERT INTO `optic_type` VALUES 
    (1,'SFP',0,NULL,NULL),
    (2,'SFP+',0,NULL,NULL),
    (3,'QSFP',0,NULL,NULL),
    (4,'XFP',0,NULL,NULL);
    
INSERT INTO `optic_connector` VALUES 
    (1,'LC',0,NULL,NULL),
    (2,'SC',0,NULL,NULL),
    (3,'FC',0,NULL,NULL),
    (4,'RJ45',0,NULL,NULL);

INSERT INTO `optic_distance` VALUES 
    (1,'10km',0,NULL,NULL),
    (2,'300m',0,NULL,NULL),
    (3,'100m',0,NULL,NULL),
    (4,'550M',0,NULL,NULL),
    (5,'80Km',0,NULL,NULL),
    (6,'10Km',0,NULL,NULL),
    (7,'40Km',0,NULL,NULL);

INSERT INTO `optic_speed` VALUES 
    (1,'100M',0,NULL,NULL),
    (2,'1G',0,NULL,NULL),
    (3,'4G',0,NULL,NULL),
    (4,'8G',0,NULL,NULL),
    (5,'10G',0,NULL,NULL),
    (6,'25G',0,NULL,NULL),
    (7,'40G',0,NULL,NULL),
    (8,'50G',0,NULL,NULL),
    (9,'100G',0,NULL,NULL),
    (10,'200G',0,NULL,NULL),
    (11,'400G',0,NULL,NULL),
    (12,'800G',0,NULL,NULL);

