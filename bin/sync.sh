#!/bin/sh
SOURCE=$(dirname $0)/../
TARGET=~/share/ezpublish

rsync -uavz --force --delete --exclude-from=$SOURCE/config/rsync_local.txt $SOURCE/ $TARGET/
