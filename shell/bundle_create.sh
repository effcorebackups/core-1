#!/usr/bin/env bash

cd ../

git clean  -f -d
git reset --hard
git pull

build=$(grep 'build: [0-9]\{4,5\}' system/bundle.data | sed 's/  build: //g')
bundle_name="../effcore-$build.zip"

rm $bundle_name

{ find .             -maxdepth 1 -type f -not -name '.*'             & \
  find .             -maxdepth 1 -type f      -name '.editorconfig'  & \
  find .             -maxdepth 1 -type f      -name '.gitattributes' & \
  find .             -maxdepth 1 -type f      -name '.gitignore'     & \
  find .             -maxdepth 1 -type f      -name '.htaccess'      & \
  find .             -maxdepth 1 -type f      -name '.nginx'         & \
  find dynamic/cache -maxdepth 1 -type f      -name 'readme.md'      & \
  find dynamic/data  -maxdepth 1 -type f      -name 'readme.md'      & \
  find dynamic/files -maxdepth 1 -type f      -name 'readme.md'      & \
  find dynamic/logs  -maxdepth 1 -type f      -name 'readme.md'      & \
  find dynamic/tmp   -maxdepth 1 -type f      -name 'readme.md'      & \
  find modules       -maxdepth 1 -type f      -name 'readme.md'      & \
  find readme                    -type f -not -name '.*'             & \
  find shell                     -type f -not -name '.*'             & \
  find system                    -type f -not -name '.*';              \
} | zip -9 $bundle_name -@
