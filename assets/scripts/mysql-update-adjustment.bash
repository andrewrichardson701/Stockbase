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
            echo "SQL Changes to be made:"
            echo " - 'label' table becomes 'tag'"
            echo " - 'stock_label' table becomes 'stock_tag'"
            echo "    - 'stock_label.label_id' becomes 'stock_tag.tag_id'"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE label RENAME tag; ALTER TABLE stock_label RENAME stock_tag; ALTER TABLE stock_tag RENAME COLUMN label_id TO tag_id;"
            break;;
    0.4.1-beta )
            echo "SQL Changes to be made:"
            echo " - Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config table"
            echo " - Columns: 'cost_enable_normal' and 'cost_enable_cable' to be added to config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE config_default ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; ALTER TABLE config ADD COLUMN cost_enable_normal BOOLEAN NOT NULL DEFAULT 1; ALTER TABLE config_default ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1; ALTER TABLE config ADD COLUMN cost_enable_cable BOOLEAN NOT NULL DEFAULT 1;"
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
    0.4.1-beta )
            echo "SQL Changes to be made:"
            echo " - Columns: 'cost_enable_normal' and 'cost_enable_cable' to be removed from config table"
            echo " - Columns: 'cost_enable_normal' and 'cost_enable_cable' to be removed from config_default table"
            mysql -u "$db_username" -p "$db_password" -e "USE inventory; ALTER TABLE config_default DROP COLUMN cost_enable_normal; ALTER TABLE config_default DROP COLUMN cost_enable_cable; ALTER TABLE config DROP COLUMN cost_enable_normal; ALTER TABLE config DROP COLUMN cost_enable_cable;"
            break;;
    esac 
    
}