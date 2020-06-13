#!/usr/bin/env bash

while true; do
  read -p '!!!!!!!!!! ALL DATA WILL BE LOST !!!!!!!!!! Are you sure? [y/n]: ' answer
  case $answer in
    y ) find ../dynamic/cache -type f -not -name 'readme.md' -delete
        find ../dynamic/data  -type f -not -name 'readme.md' -delete
        find ../dynamic/files -type f -not -name 'readme.md' -delete
        find ../dynamic/logs  -type f -not -name 'readme.md' -delete
        find ../dynamic/tmp   -type f -not -name 'readme.md' -delete
        break;;
    n ) exit;;
    * ) echo 'Please answer "y" or "n".';;
  esac
done
