#!/usr/bin/env bash

cd ../

hostname
whoami
git --version
git clean  -f -d
git reset --hard
git pull

find dynamic/cache -type f -not -name 'readme.md' -delete
