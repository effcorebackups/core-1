#!/usr/bin/env bash

git -C ../ clean  -d  -fx .
git -C ../ reset --hard
git -C ../ pull

while true; do
  read -p 'Apply stash? [y/n]: ' answer
  case $answer in
    y ) git -C ../ stash apply
        break;;
    n ) exit;;
    * ) echo 'Please answer "y" or "n".';;
  esac
done

./cache_clear.sh