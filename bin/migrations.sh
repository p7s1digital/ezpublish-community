#!/bin/bash -e

SCRIPT_NAME=`readlink -f $0`
BASE_PATH=`dirname $SCRIPT_NAME`/../src/MyVideo

echo $@
php -d memory_limit=1G $BASE_PATH/migrations/doctrine-migrations.phar --configuration=$BASE_PATH/migrations/migrations.yml --db-configuration=$BASE_PATH/migrations/migrations-db.php  $@