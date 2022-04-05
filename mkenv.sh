#! /usr/bin/sh

ENV_DIR_PATH=~/environment
TMP_DIR_PATH=${ENV_DIR_PATH}/codegym-scripts
REPOSITORY_DIR_PATH=${ENV_DIR_PATH}/codegym-php-challenge
ENV_FILE_PATH=${REPOSITORY_DIR_PATH}/.env
PHPDB_FILE_PATH=${REPOSITORY_DIR_PATH}/html/db.php

if [ -e "${ZIP_FILE_NAME}" ]; then
    echo 'password file exists.' ${ZIP_FILE_NAME}
    exit;
fi

PASSWORD=`pwmake --help`

sed -i -e "s/{{MYSQL_ROOT_PASSWORD}}/${PASSWORD}/g" ${ENV_FILE_PATH}
sed -i -e "s/{{MYSQL_ROOT_PASSWORD}}/${PASSWORD}/g" ${PHPDB_FILE_PATH}

git update-index --assume-unchanged ${ENV_FILE_PATH}
git update-index --assume-unchanged html/db.php
