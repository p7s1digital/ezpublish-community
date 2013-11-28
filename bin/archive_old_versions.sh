#!/bin/bash

if [ -z $1 ]
then
  echo "Missing argument - directory"
  exit 1
fi

if [ -n $2 ]
then
  # cache directory (relative to $1)
  cachedir=$2
fi

echo "Change direcotory $1"
cd $1
if [ $? -ne 0 ]
then
  echo "Wrong directory"
  exit 1
fi

numbers=$(ls -1d ???????-[0-9][0-9]* | awk -F- '{ print $2 }' | sort -nr | tail -n +10)

if [ ! -d archive ]
then
  echo "Archive directory missing"
  exit 1
fi

for version in $numbers
do
  versionname=$(ls -d ???????-$version)
  cachepath=$versionname/$cachedir
  if [ -n $cachepath -a -d $cachepath ]
  then
    echo "Delete $cachepath/prod/sessions"
    /bin/rm -rf "$cachepath/prod/sessions"
  fi
  echo "Create tar archive/$versionname.tar.gz"
  tar czf archive/$versionname.tar.gz $versionname
  if [ -s archive/$versionname.tar.gz ]
  then
    echo "Remove $versionname"
    sudo /bin/chown -R myvideo $(pwd)/$versionname
    /bin/rm -rf "$versionname"
  else
    echo "Tar archive/$versionname.tar.gz empty or doesn't exist."
    exit 1
  fi
done
exit 0
