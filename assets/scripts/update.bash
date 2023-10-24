#!/bin/bash

# Define the name of the remote repository and your desired branch prefix
REMOTE_REPO="http://git.ajrich.co.uk/web/inventory.git"
BRANCH_PREFIX=""

# Fetch the latest information from the remote repository
git fetch origin

# Get the current local branch
CURRENT_BRANCH=$(git symbolic-ref --short HEAD)

# Get the latest remote branches with the specified prefix
REMOTE_BRANCHES=($(git ls-remote --heads $REMOTE_REPO $BRANCH_PREFIX* | cut -f 3 | cut -d '/' -f 3))

# Check if there are any remote branches
if [ ${#REMOTE_BRANCHES[@]} -eq 0 ]; then
  echo "No remote branches found."
  exit 1
fi

# Find the latest version among remote branches
LATEST_VERSION=${REMOTE_BRANCHES[0]}
for branch in "${REMOTE_BRANCHES[@]}"; do
  if [[ $branch > $LATEST_VERSION ]]; then
    LATEST_VERSION=$branch
  fi
done

# Compare the local branch with the latest version
if [ $CURRENT_BRANCH != $LATEST_VERSION ]; then
  echo "A newer version is available: $LATEST_VERSION"
else
  echo "You have the latest version: $CURRENT_BRANCH"
fi
