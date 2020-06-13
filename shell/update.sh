#!/usr/bin/env bash

cd ../

git clean  -f -d
git reset --hard
git pull

find dynamic/cache -not -name 'readme.md' -not -path 'dynamic/cache' -delete