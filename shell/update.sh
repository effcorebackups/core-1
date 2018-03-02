git -C ../ reset --hard
git -C ../ pull
git -C ../ stash apply
rm -f ../dynamic/cache/*.php