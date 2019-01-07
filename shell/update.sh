#!/usr/bin/env bash

git -C ../ clean  -d  -f .
git -C ../ reset --hard
git -C ../ pull
./cache_clear.sh