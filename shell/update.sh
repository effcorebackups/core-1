#!/usr/bin/env bash

git -C ../ clean  -f -d
git -C ../ reset --hard
git -C ../ pull

./cache_clear.sh