#!/usr/bin/env bash

while true; do
  read -p 'All data will be lost. Are you sure?: ' answer
  case $answer in
    y ) find ../dynamic/cache/   -not -name 'readme.md' -delete
        find ../dynamic/data/    -not -name 'readme.md' -delete
        find ../dynamic/files/   -not -name 'readme.md' -delete
        find ../dynamic/logs/    -not -name 'readme.md' -delete
        find ../dynamic/modules/ -not -name 'readme.md' -delete
        find ../dynamic/tmp/     -not -name 'readme.md' -delete
        cp /dev/null ../dynamic/data/data.sqlite
        break;;
    n ) exit;;
    * ) echo 'Please answer "y" or "n".';;
  esac
done
