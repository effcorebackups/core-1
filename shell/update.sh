git reset --hard
git pull
git stash apply stash@{0}
git stash apply stash@{1}
rm dynamic/cache/*