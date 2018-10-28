#!/usr/bin/env bash

git -C ../ reset --hard
git -C ../ pull
# git -C ../ stash apply
./cache_clear.sh