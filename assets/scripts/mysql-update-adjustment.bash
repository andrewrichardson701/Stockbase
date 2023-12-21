#!/bin/bash

usage() {
  echo "Usage: $0 [-c <current_branch>] [-n <new_branch>] [-r <repository>] [--help]"
  echo "Options:"
  echo "  -c, --current               Set the current branch"
  echo "  -n, --new                   Set the new branch"
  echo "  -r, --repository, --repo    Set the repository"
  echo "  -h, --help                  Display this help message"
  echo ""
  exit 1
}

current_branch=""
new_branch=""
repo=""

OPTS=$(getopt -o c:n:r: --long current:,new:,repository:,repo:,help -n "$0" -- "$@")
if [ $? != 0 ]; then
  echo "Failed to parse command line options."
  exit 1
fi

eval set -- "$OPTS"

while true; do
  case "$1" in
    -c|--current)
      current_branch="$2"
      shift 2
      ;;
    -n|--new)
      new_branch="$2"
      shift 2
      ;;
    -r|--repository|--repo)
      repo="$2"
      shift 2
      ;;
    -h|--help)
      usage
      ;;
    --)
      shift
      break
      ;;
    *)
      echo "Internal error!"
      exit 1
      ;;
  esac
done

if [ -z "$current_branch" ] || [ -z "$new_branch" ] || [ -z "$repo" ]; then
  echo "Error: Required options not provided."
  usage
  exit
fi

echo "Current branch is set to: $current_branch"
echo "New branch is set to: $new_branch"
echo "Repository is set to: $repo"

# Get remote repo branches
branch_array=($(git ls-remote --heads $repo | cut -f 2 | cut -d '/' -f 3))

# for testing, print all branches found
# for key in "${!branch_array[@]}"; do
#   echo "Key: $key, Value: ${branch_array[$key]}"
# done

# Check if branch_array is empty - if yes, error
if [ ${#branch_array[@]} -eq 0 ]; then
    echo "No branches found. Check repository URL or permissions."
    exit 
fi

# Check if branches exist.
for branch in "${branch_array[@]}"; do
    if [ "$branch" == "$current_branch" ]; then
        current_branch_exists=true
    fi
    if [ "$branch" == "$new_branch" ]; then
        new_branch_exists=true
    fi
done

# Check if the current branch exists
if [ "$current_branch_exists" == true ]; then
    echo "Current branch found."
else
    echo "Error: Current branch '$current_branch' not found in the repository."
    exit 1
fi

# Check if the new branch exists
if [ "$new_branch_exists" == true ]; then
    echo "New branch found."
else
    echo "Error: New branch '$new_branch' not found in the repository."
    exit 1
fi

# Check if branches match each other - do not continue
if [ "$current_branch" == "$new_branch" ]; then
    echo "Error: Current and New branch match. No update required."
    exit
fi

# Check if branch is below beta 1.3.2-beta branch. This is when the script was introduced.
if [ "$new_branch" \< "1.3.2-beta" ]; then
    echo ""
    echo "Selected new branch is too old. The database structure is unconfirmed."
    echo "No changes will be made."
    exit
fi

dbh='../../includes/dbh.inc.php'
db_username=$(grep -oP "(?<=\$dBUsername = ')[^']+" "$dbh")
db_password=$(grep -oP "(?<=\$dBPassword = ')[^']+" "$dbh
")
# run the correct function based on the new branch
case "$new_branch" in
    0.3.2-beta|0.3.3-beta ) 
            0.3.X-beta "$current_branch"
            break;;
    0.4.0-beta )
            0.4.0-beta "$current_branch"
            break;;
    0.4.1-beta|0.4.2-beta )
            0.4.1-beta "$current_branch"
            break;;
    0.5.0-beta )
            0.5.0-beta "$current_branch"
            break;;
    0.6.0-beta )
            0.6.0-beta "$current_branch"
            break;;
esac


###### When a DB change happens, a new minor version should change - e.g. 0.1.5 > 0.2.0, not 0.1.5.> 0.1.6 #######


0.3.X-beta() {
    # This is for all 0.3.X-beta branches/versions past 0.3.2-beta

    current_branch=$1

    case "$current_branch" in
    0.3.2-beta|0.3.3-beta ) 
            echo "No SQL changes to be made."
            break;;
    0.4.0-beta )
            echo "SQL Changes to be made:"
            echo " - 'tag' table becomes 'label'"
            echo " - 'stock_tag' table becomes 'stock_label'"
            echo "    - 'stock_tag.tag_id' becomes 'stock_label.label_id'"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE tag RENAME label; ALTER TABLE stock_tag RENAME stock_label; ALTER TABLE stock_label RENAME COLUMN tag_id TO label_id;"
            break;;
    0.4.1-beta|0.4.2-beta )
            echo "SQL Changes to be made:"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be removed from config table"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be removed from config_default table".
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be removed from config table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be removed from config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                              ALTER TABLE config_default DROP COLUMN cost_enable_normal; 
                                              ALTER TABLE config_default DROP COLUMN cost_enable_cable; 
                                              ALTER TABLE config DROP COLUMN cost_enable_normal; 
                                              ALTER TABLE config DROP COLUMN cost_enable_cable;
                                              ALTER TABLE config_default DROP COLUMN footer_enable; 
                                              ALTER TABLE config_default DROP COLUMN footer_left_enable;
                                              ALTER TABLE config_default DROP COLUMN footer_right_enable
                                              ALTER TABLE config DROP COLUMN footer_enable; 
                                              ALTER TABLE config DROP COLUMN footer_left_enable;
                                              ALTER TABLE config DROP COLUMN footer_right_enable;"
            0.3.X-beta "0.4.0-beta"  
            break;;
    0.5.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.5.0-beta) Tables: 'sessionlog' to be removed"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          DROP TABLE `sessionlog`;"
            0.3.X-beta "0.4.1-beta"                                             
            break;;
    0.6.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.6.0-beta) Database to be renamed from stockbase to inventory"
            echo " - (0.6.0-beta) Tables: optic_speed, optic_transaction, optic_vendor, optic_item, optic_type, optic_comment, optic_connector to be removed"
            mysql -u "$db_username" -p "$db_password" -e "CREATE DATABASE inventory;"
            mysqldump -u "$db_username" -p "$db_password" stockbase | mysql inventory
            mysqldump -u "$db_username" -p "$db_password" stockbase > /tmp/stockbase-backup.sql
            mysql -u "$db_username" -p "$db_password" -e "DROP DATABASE stockbase;"
            mysql -u "$db_username" -p "$db_password" -e "DROP TABLE optic_type;
                                                                DROP TABLE optic_transaction;
                                                                DROP TABLE optic_vendor;
                                                                DROP TABLE optic_item;
                                                                DROP TABLE optic_connector;
                                                                DROP TABLE optic_comment
                                                                DROP TABLE optic_speed;"
            0.3.X-beta "0.5.0-beta"                                             
            break;;
    esac 
}

0.4.0-beta() {
    # This is for all 0.3.X-beta branches/versions past 0.3.2-beta

    current_branch=$1

    case "$current_branch" in
    0.3.2-beta|0.3.3-beta ) 
            echo "SQL Changes to be made:"
            echo " - 'tag' table becomes 'label'"
            echo " - 'stock_tag' table becomes 'stock_label'"
            echo "    - 'stock_tag.tag_id' becomes 'stock_label.label_id'"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE tag RENAME label; ALTER TABLE stock_tag RENAME stock_label; ALTER TABLE stock_label RENAME COLUMN tag_id TO label_id;"
            break;;
            
    0.4.0-beta )
            echo "No SQL changes to be made."
            break;;
    0.4.1-beta|0.4.2-beta )
            echo "SQL Changes to be made:"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be removed from config table"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be removed from config_default table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be removed from config table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be removed from config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          ALTER TABLE config_default DROP COLUMN cost_enable_normal; 
                                                          ALTER TABLE config_default DROP COLUMN cost_enable_cable; 
                                                          ALTER TABLE config DROP COLUMN cost_enable_normal; 
                                                          ALTER TABLE config DROP COLUMN cost_enable_cable;
                                                          ALTER TABLE config_default DROP COLUMN footer_enable; 
                                                          ALTER TABLE config_default DROP COLUMN footer_left_enable;
                                                          ALTER TABLE config_default DROP COLUMN footer_right_enable
                                                          ALTER TABLE config DROP COLUMN footer_enable; 
                                                          ALTER TABLE config DROP COLUMN footer_left_enable;
                                                          ALTER TABLE config DROP COLUMN footer_right_enable;"
            break;;
    0.5.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.5.0-beta) Tables: 'sessionlog' to be remove"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          DROP TABLE `sessionlog`;"
            0.4.0-beta "0.4.1-beta"                                             
            break;;
    0.6.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.6.0-beta) Database to be renamed from stockbase to inventory"
            echo " - (0.6.0-beta) Tables: optic_speed, optic_transaction, optic_vendor, optic_item, optic_type, optic_comment, optic_connector to be removed"
            mysql -u "$db_username" -p "$db_password" -e "CREATE DATABASE inventory;"
            mysqldump -u "$db_username" -p "$db_password" stockbase | mysql inventory
            mysqldump -u "$db_username" -p "$db_password" stockbase > /tmp/stockbase-backup.sql
            mysql -u "$db_username" -p "$db_password" -e "DROP DATABASE stockbase;"
            mysql -u "$db_username" -p "$db_password" -e "DROP TABLE optic_type;
                                                                DROP TABLE optic_transaction;
                                                                DROP TABLE optic_vendor;
                                                                DROP TABLE optic_item;
                                                                DROP TABLE optic_connector;
                                                                DROP TABLE optic_comment
                                                                DROP TABLE optic_speed;"
            0.4.0-beta "0.5.0-beta"                                             
            break;;
    esac 
}

0.4.1-beta() {
    # This is for all 0.3.X-beta branches/versions past 0.3.2-beta

    current_branch=$1

    case "$current_branch" in
    0.3.2-beta|0.3.3-beta ) 
            echo "SQL Changes to be made:"
            echo " - 'tag' table becomes 'label'"
            echo " - 'stock_tag' table becomes 'stock_label'"
            echo "    - 'stock_tag.tag_id' becomes 'stock_label.label_id'"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE tag RENAME label; ALTER TABLE stock_tag RENAME stock_label; ALTER TABLE stock_label RENAME COLUMN tag_id TO label_id;"
            0.4.1-beta "0.4.0-beta"
            break;;
            
    0.4.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added from config table"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config_default table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be added to config table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be added to config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          ALTER TABLE config_default ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1;
                                                          ALTER TABLE config_default ADD COLUMN footer_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN footer_left_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN footer_right_enable BOOLEAN NOT NULL DEFAULT 1;
                                                          ALTER TABLE config ADD COLUMN footer_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN footer_left_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN footer_right_enable BOOLEAN NOT NULL DEFAULT 1;"
            break;;
    0.4.1-beta|0.4.2-beta )
            echo "No SQL changes to be made."
            break;;
    0.5.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.5.0-beta) Tables: 'sessionlog' to be remove"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          DROP TABLE `sessionlog`;"                                           
            break;;
    0.6.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.6.0-beta) Database to be renamed from stockbase to inventory"
            echo " - (0.6.0-beta) Tables: optic_speed, optic_transaction, optic_vendor, optic_item, optic_type, optic_comment, optic_connector to be removed"
            mysql -u "$db_username" -p "$db_password" -e "CREATE DATABASE inventory;"
            mysqldump -u "$db_username" -p "$db_password" stockbase | mysql inventory
            mysqldump -u "$db_username" -p "$db_password" stockbase > /tmp/stockbase-backup.sql
            mysql -u "$db_username" -p "$db_password" -e "DROP DATABASE stockbase;"
            mysql -u "$db_username" -p "$db_password" -e "DROP TABLE optic_type;
                                                                DROP TABLE optic_transaction;
                                                                DROP TABLE optic_vendor;
                                                                DROP TABLE optic_item;
                                                                DROP TABLE optic_connector;
                                                                DROP TABLE optic_comment
                                                                DROP TABLE optic_speed;
                                                                ALTER TABLE stock DROP INDEX name;
                                                                ALTER TABLE stock DROP INDEX description;"
            0.4.1-beta "0.5.0-beta"                                             
            break;;
    esac 
}

0.5.0-beta() {
    # This is for all 0.3.X-beta branches/versions past 0.3.2-beta

    current_branch=$1

    case "$current_branch" in
    0.3.2-beta|0.3.3-beta ) 
            echo "SQL Changes to be made:"
            echo " - 'tag' table becomes 'label'"
            echo " - 'stock_tag' table becomes 'stock_label'"
            echo "    - 'stock_tag.tag_id' becomes 'stock_label.label_id'"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE tag RENAME label; ALTER TABLE stock_tag RENAME stock_label; ALTER TABLE stock_label RENAME COLUMN tag_id TO label_id;"
            0.5.0-beta "0.4.0-beta"
            break;;
            
    0.4.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config table"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config_default table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be added to config table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be added to config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          ALTER TABLE config_default ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1;
                                                          ALTER TABLE config_default ADD COLUMN footer_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN footer_left_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN footer_right_enable BOOLEAN NOT NULL DEFAULT 1;
                                                          ALTER TABLE config ADD COLUMN footer_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN footer_left_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN footer_right_enable BOOLEAN NOT NULL DEFAULT 1;"
            0.5.0-beta "0.4.1-beta"
            break;;
    0.4.1-beta|0.4.2-beta )
            echo "SQL Changes to be made:"
            echo " - (0.5.0-beta) Tables: 'sessionlog' to be added"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          CREATE TABLE `sessionlog` (
                                                            `id` BIGINT NOT NULL AUTO_INCREMENT,
                                                            `user_id` INT NOT NULL,
                                                            `login_time` INT NOT NULL,
                                                            `logout_time` INT,
                                                            `last_activity` INT NOT NULL,
                                                            `ipv4` INT unsigned,
                                                            `ipv6` VARBINARY(16),
                                                            `browser` TEXT NOT NULL,
                                                            `os` TEXT NOT NULL,
                                                            `status` text NOT NULL,
                                                            PRIMARY KEY (`id`)
                                                          );"
            break;;

    0.5.0-beta )
            echo "No SQL changes to be made."
            break;;
    0.6.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.6.0-beta) Database to be renamed from stockbase to inventory"
            echo " - (0.6.0-beta) Tables: optic_speed, optic_transaction, optic_vendor, optic_item, optic_type, optic_comment, optic_connector to be removed"
            mysql -u "$db_username" -p "$db_password" -e "CREATE DATABASE inventory;"
            mysqldump -u "$db_username" -p "$db_password" stockbase | mysql inventory
            mysqldump -u "$db_username" -p "$db_password" stockbase > /tmp/stockbase-backup.sql
            mysql -u "$db_username" -p "$db_password" -e "DROP DATABASE stockbase;"
            mysql -u "$db_username" -p "$db_password" -e "DROP TABLE optic_type;
                                                                DROP TABLE optic_transaction;
                                                                DROP TABLE optic_vendor;
                                                                DROP TABLE optic_item;
                                                                DROP TABLE optic_connector;
                                                                DROP TABLE optic_comment
                                                                DROP TABLE optic_speed;
                                                                ALTER TABLE stock DROP INDEX name;
                                                                ALTER TABLE stock DROP INDEX description;"                                          
            break;;
    esac 
}

0.6.0-beta() {
    # This is for all 0.3.X-beta branches/versions past 0.3.2-beta

    current_branch=$1

    case "$current_branch" in
    0.3.2-beta|0.3.3-beta ) 
            echo "SQL Changes to be made:"
            echo " - 'tag' table becomes 'label'"
            echo " - 'stock_tag' table becomes 'stock_label'"
            echo "    - 'stock_tag.tag_id' becomes 'stock_label.label_id'"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE tag RENAME label; ALTER TABLE stock_tag RENAME stock_label; ALTER TABLE stock_label RENAME COLUMN tag_id TO label_id;"
            0.5.0-beta "0.4.0-beta"
            break;;
            
    0.4.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config table"
            echo " - (0.4.1-beta) Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config_default table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be added to config table"
            echo " - (0.4.2-beta) Columns: 'footer_enable', 'footer_left_enable' and 'footer_right_enable' to be added to config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          ALTER TABLE config_default ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1;
                                                          ALTER TABLE config_default ADD COLUMN footer_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN footer_left_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config_default ADD COLUMN footer_right_enable BOOLEAN NOT NULL DEFAULT 1;
                                                          ALTER TABLE config ADD COLUMN footer_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN footer_left_enable BOOLEAN NOT NULL DEFAULT 1; 
                                                          ALTER TABLE config ADD COLUMN footer_right_enable BOOLEAN NOT NULL DEFAULT 1;"
            0.5.0-beta "0.4.1-beta"
            break;;
    0.4.1-beta|0.4.2-beta )
            echo "SQL Changes to be made:"
            echo " - (0.5.0-beta) Tables: 'sessionlog' to be added"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; 
                                                          CREATE TABLE `sessionlog` (
                                                            `id` BIGINT NOT NULL AUTO_INCREMENT,
                                                            `user_id` INT NOT NULL,
                                                            `login_time` INT NOT NULL,
                                                            `logout_time` INT,
                                                            `last_activity` INT NOT NULL,
                                                            `ipv4` INT unsigned,
                                                            `ipv6` VARBINARY(16),
                                                            `browser` TEXT NOT NULL,
                                                            `os` TEXT NOT NULL,
                                                            `status` text NOT NULL,
                                                            PRIMARY KEY (`id`)
                                                          );"
            break;;

    0.5.0-beta )
            echo "SQL Changes to be made:"
            echo " - (0.6.0-beta) Database to be renamed from inventory to stockbase"
            echo " - (0.6.0-beta) Tables: optic_speed, optic_transaction, optic_vendor, optic_item, optic_type, optic_comment, optic_connector to be added"
            echo " - (0.6.0-beta) Default data to be added to optic_speed, optic_connector, optic_type."
            mysql -u "$db_username" -p "$db_password" -e "CREATE DATABASE stockbase;"
            mysqldump -u "$db_username" -p "$db_password" inventory | mysql stockbase
            mysqldump -u "$db_username" -p "$db_password" inventory > /tmp/inventory-backup.sql
            mysql -u "$db_username" -p "$db_password" -e "DROP DATABASE inventory;"
            mysql -u "$db_username" -p "$db_password" -e "USE stockbase; 
                                                                CREATE TABLE `optic_comment` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `item_id` bigint NOT NULL,
                                                                        `comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `user_id` int NOT NULL,
                                                                        `timestamp` datetime NOT NULL,
                                                                        `deleted` tinyint(1) NOT NULL DEFAULT '0',
                                                                        PRIMARY KEY (`id`)
                                                                );
                                                                CREATE TABLE `optic_connector` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `deleted` tinyint(1) NOT NULL DEFAULT '0',
                                                                        PRIMARY KEY (`id`)
                                                                );
                                                                CREATE TABLE `optic_item` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `model` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `vendor_id` int NOT NULL,
                                                                        `serial_number` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `type_id` int NOT NULL,
                                                                        `connector_id` int NOT NULL,
                                                                        `mode` tinytext CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `speed_id` int NOT NULL,
                                                                        `site_id` int NOT NULL,
                                                                        `quantity` int NOT NULL DEFAULT '1',
                                                                        `deleted` tinyint(1) NOT NULL DEFAULT '0',
                                                                        PRIMARY KEY (`id`)
                                                                );
                                                                CREATE TABLE `optic_speed` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        PRIMARY KEY (`id`)
                                                                );
                                                                CREATE TABLE `optic_transaction` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `table_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `item_id` bigint NOT NULL,
                                                                        `type` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `date` date NOT NULL,
                                                                        `time` time NOT NULL,
                                                                        `username` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `site_id` bigint NOT NULL,
                                                                        PRIMARY KEY (`id`)
                                                                );
                                                                CREATE TABLE `optic_type` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `deleted` tinyint(1) NOT NULL DEFAULT '0',
                                                                        PRIMARY KEY (`id`)
                                                                );
                                                                CREATE TABLE `optic_vendor` (
                                                                        `id` bigint NOT NULL AUTO_INCREMENT,
                                                                        `name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
                                                                        `deleted` tinyint(1) NOT NULL DEFAULT '0',
                                                                        PRIMARY KEY (`id`)
                                                                );
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
                                                                ALTER TABLE stock ADD FULLTEXT(name);
                                                                ALTER TABLE stock ADD FULLTEXT(description);"
            break;;
    0.6.0-beta )
            echo "No SQL changes to be made."                                        
            break;;
    esac 
}