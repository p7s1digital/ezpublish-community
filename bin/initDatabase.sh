#!/bin/sh
# These statements are done by puppet by default. 
echo "DROP DATABASE IF EXISTS ezpublish; CREATE DATABASE ezpublish; SELECT name,installed_version FROM pg_available_extensions WHERE name = 'pgcrypto' AND installed_version = '1.0'" | psql
pg_restore -d ezpublish -Fc -c data/0000-base-schema-and-data.sql | psql
