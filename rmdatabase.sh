#! /usr/bin/sh

ENV_DIR_PATH=~/environment
REPOSITORY_DIR_PATH=${ENV_DIR_PATH}/codegym-php-challenge
DB_DATA_DIR_PATH=${REPOSITORY_DIR_PATH}/docker/db/var_lib_mysql

if [ -e "${DB_DATA_DIR_PATH}" ]; then
    sudo rm -fr ${DB_DATA_DIR_PATH}
fi
