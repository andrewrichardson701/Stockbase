#!/bin/bash

# Define the name of the remote repository and your desired branch prefix
REMOTE_REPO="https://git.ajrich.co.uk/web/inventory.git"

# Navigate to the root directory of the Git repository
cd "$(git rev-parse --show-toplevel)"
install_dir=$PWD # this gets the current directory path

# Fetch the latest information from the remote repository
git fetch origin

# Get the current local branch
CURRENT_BRANCH=$(git symbolic-ref --short HEAD)

# Get the latest remote branches with the specified prefix
REMOTE_BRANCHES=($(git ls-remote --heads $REMOTE_REPO | cut -f 2 | cut -d '/' -f 3))

# Check if there are any remote branches
if [ ${#REMOTE_BRANCHES[@]} -eq 0 ]; then
  echo "No remote branches found."
  exit 1
elif [ ${#REMOTE_BRANCHES[@]} -ge 1 ]; then
  echo "Branches:"
fi

# Separate branches without numbers and place them at the top
branches_without_numbers=()
branches_with_numbers=()
for branch in "${REMOTE_BRANCHES[@]}"; do
  branch_name=$(echo "$branch" | cut -d ' ' -f 2)
  if [[ ! $branch_name =~ [0-9] ]]; then
    branches_without_numbers+=("$branch_name")
  else
    branches_with_numbers+=("$branch_name")
  fi
done

# Sort branches without numbers
IFS=$'\n' sorted_branches_without_numbers=($(sort -V <<<"${branches_without_numbers[*]}"))
unset IFS

# Sort branches with numeric versions based on version
IFS=$'\n' sorted_branches_with_numbers=($(sort -V <<<"${branches_with_numbers[*]}"))
unset IFS

# Combine branches without numbers and branches with numbers
sorted_branches=("${sorted_branches_without_numbers[@]}" "${sorted_branches_with_numbers[@]}")

LATEST_VERSION=${sorted_branches[-1]}

branch_count=${#sorted_branches[@]}

i=$branch_count
declare -A install_branches

for branch in "${sorted_branches[@]}"; do
  
  branch_echo="$branch"
  
  if [[ $branch == $CURRENT_BRANCH ]]; then
    branch_echo+=" (current)"
    current_branch_number=$i
  fi
  
  if [[ $branch == $LATEST_VERSION ]]; then
    branch_echo+=" (latest)"
    latest_branch_number=$i
  fi

  echo "  $i - $branch_echo"

  install_branches["$i"]="$branch"

  ((i--))
done

# Compare the local branch with the latest version
if [[ $CURRENT_BRANCH != $LATEST_VERSION ]]; then
  echo "A newer version is available: $LATEST_VERSION"
else
  echo "You have the latest version: $CURRENT_BRANCH"
fi

# for key in "${!install_branches[@]}"; do
#   echo "Key: $key, Value: ${install_branches[$key]}"
# done


while true; do
  read -p "Select branch number to install ($branch_count - 1): " install_branch_number
  if ((install_branch_number >= 1 && install_branch_number <= branch_count)); then
    selected_branch="${install_branches["$install_branch_number"]}"
    echo ""
    echo "Branch '$selected_branch' selected."
    if ((install_branch_number <= current_branch_number)); then
      echo "The selected version is older than the currently installed version (${install_branches["$current_branch_number"]} ($current_branch_number))"
      echo "The database structure may differ, causing a loss of data."
      read -p "Are you sure you want to continue? (Y/N): " downgrade_confirm
        while true; do
          case "$downgrade_confirm" in
            [Yy]* ) 
                    echo ""
                    echo "The version will be downgraded to $selected_branch."
                    downgrade_confirm="Y"
                    break;;
            [Nn]* ) 
                    echo ""
                    break;;
            * ) echo "Please answer Y or N.";;
          esac
        done
      if [[ $downgrade_confirm == "Y" ]]; then
        break
      fi
    else
      break
    fi
  else
    echo ""
    echo "Please select a value between $branch_count and 1."
  fi
done
sleep 1

# Dependent info
dbName=$(grep -oP "\$dBName = '\K[^']+" $install_dir/includes/dbh.inc.php)
dbUsername=$(grep -oP "\$dBUsername = '\K[^']+" $install_dir/includes/dbh.inc.php)
dbPassword=$(grep -oP "\$dBPassword = '\K[^']+" $install_dir/includes/dbh.inc.php)


# Download new branch to /tmp/inventory
download_dir="/tmp/inventory-$selected_branch"
echo ""
echo "Downloading branch: $selected_branch to $tmp_dir ..."
sleep 1
git clone --branch $selected_branch http://git.ajrich.co.uk/web/inventory.git $tmp_dir
echo "Done."
sleep 1

# Create backup folder, and make a backup in it.
current_date_time=$(date +"%Y-%m-%d-%H-%M-%S")
backup_folder="inventory_backup-$CURRENT_BRANCH-$current_date_time"
backup_dir="/tmp/$back_folder"
mkdir $backup_dir

echo ""
db_backup_folder=/assets/sql/mysql-full-backup.sql
db_backup_dir="$install_dir$db_backup_dir"
echo "Backing up the database to $db_backup_dir."
mysqldump -u$dbUsername -p$dbPassword $dbName > $db_backup_dir
sleep 5
echo "Done."
sleep 1

echo "Backing up currently installed version to $backup_dir."
mv $install_dir/* $backup_dir
echo "Done."
sleep 1


# Move new version to the install directory.
echo ""
echo "Moving new version to the install directory."
mv $download_dir/* $install_dir
rm $download_dir
echo "Done."
sleep 1


# Run SQL script to update the DB.
echo ""
mysql_adjustment_script="$install_dir/assets/scripts/mysql-update-adjustment.bash"
echo "Running mysql adjustment script ($mysql_adjustment_script), to adjust the database to accomodate the changes..."
/bin/bash $mysql_adjustment_script --currentbranch $CURRENT_BRANCH --newbranch $selected_branch
sleep 5
echo "Done."


# Set permissions to the folder for www-data
echo ""
echo "Setting file permissions..."
chown www-data:www-data $install_dir -R
chmod 700 $install_dir -R
echo "Done!"


# Update complete!
echo "Successfully updated from $CURRENT_BRANCH to $selected_branch!"
echo "All previous files and database backup are located in $backup_dir ."
echo "To recover, copy the backup back to $install_dir and run the mysqldump from $install_dir$db_backup_folder."