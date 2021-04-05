#!/usr/bin/env bash

repo_origin=$(grep 'repo_origin: ' ../system/bundle.data | sed 's/  repo_origin: //g')
repo_branch=$(grep 'repo_branch: ' ../system/bundle.data | sed 's/  repo_branch: //g')

rm -rf ../dynamic/tmp/repo_system
git clone --branch=$repo_branch $repo_origin ../dynamic/tmp/repo_system
rm -rf ../.git
mv ../dynamic/tmp/repo_system/.git ../
rm -rf ../dynamic/tmp/repo_system
