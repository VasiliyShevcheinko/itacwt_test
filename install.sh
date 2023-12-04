#!/bin/bash

# ================= Lib & functions =====================
source "$(dirname "${BASH_SOURCE[0]}")/bin/lib/base_command"
cd "$DIR" || exit

source "$DIR/bin/lib/backend_env_local"
source "$DIR/bin/lib/ln_if_exists"
source "$DIR/bin/lib/cp_if_exists"

echo '================= Info \ Debug ====================='
echo 'Code Pilots (c)'
docker --version
#printenv
#exit

echo '================= Setup config files ====================='
print_style "### Copy .env if dont exists\n"
ENV_PATH="$DIR/.env"
ENV_DIST_PATH="$DIR/docker/env/.env.dist"
cp_if_exists $ENV_DIST_PATH $ENV_PATH

print_style "### Change UID\GUID in .env\n"
replace-env-var "UID" $UID $ENV_PATH
replace-env-var "GUID" "$(id -g)" $ENV_PATH
print_style "UID and GUID updated in ${ENV_PATH}\n"

print_style "### Create docker-compose.yml, docker-compose.override.yml\n"
ln_if_exist "./docker/env/docker-compose.yml" "$DIR/docker-compose.yml" # relative link
cp_if_exists "$DIR/docker/env/docker-compose.override.yml" "$DIR/docker-compose.override.yml"

echo '================= Start install project ====================='
print_style "### Start containers --build\n"
make start || exit

print_style "### Waiting for db to be ready...\n"
ATTEMPTS_LEFT_TO_REACH_DATABASE=60
ENV_PATH="$DIR/.env"
DB_USER=$(grep '^DB_USER=' "$ENV_PATH" | cut -f 2 -d '=')
until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(docker-compose exec db bash -c "psql -U $DB_USER -c 'SELECT 1'" 2>&1); do
    if [ $? -eq 255 ]; then
        # If the Doctrine command exits with 255, an unrecoverable error occurred
        ATTEMPTS_LEFT_TO_REACH_DATABASE=0
        break
    fi
    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))
    echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
done

if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
    echo "The database is not up or not reachable:"
    echo "$DATABASE_ERROR"
    exit 1
else
    echo "The db is now ready and reachable"
fi

print_style "### Change DB_PASS in pgsql\n"
bin/db_recreate_password || exit

print_style "### Output container info\n"
docker-compose ps

print_style "### Install symfony project\n"
make install-symfony

print_style "Install successfully complete\n" "success"
