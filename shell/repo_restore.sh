#!/usr/bin/env bash

repo_url=$(grep 'repo_url: ' ../system/bundle.data | sed 's/  repo_url: //g')
repo_branch=$(grep 'repo_branch: ' ../system/bundle.data | sed 's/  repo_branch: //g')

rm -rf ../dynamic/tmp/repo_system
git clone --branch=$repo_branch $repo_url ../dynamic/tmp/repo_system
rm -rf ../.git
mv ../dynamic/tmp/repo_system/.git ../
rm -rf ../dynamic/tmp/repo_system