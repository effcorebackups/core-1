#!/usr/bin/env bash

cd ../

git clean  -f -d
git reset --hard
git pull

find dynamic/cache -type f -not -name 'readme.md' -delete