#!/usr/bin/env bash

cd ../

git clean  -f -d
git reset --hard
git pull

shell/cache_clear.sh