#!/bin/bash
set -o nounset -o pipefail -o errexit
cd "$(dirname "$0")/.."

################################################################################
# Install all development dependencies.
################################################################################

header() {
    echo -ne "\e[94m"
    echo -n "$@"
    echo -e "\e[0m"
}

# Install/update PHP dependencies
if [ -d vendor ]; then
    header 'Updating PHP dependencies...'
else
    header 'Installing PHP dependencies...'
fi

composer update
echo

# Install/update frontend dependencies
if [ -d node_modules ]; then
    header 'Updating frontend dependencies...'
else
    header 'Installing frontend dependencies...'
fi

yarn install
