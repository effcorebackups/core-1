#!/usr/bin/env bash

cd ../

while true; do
    read -p 'Do "git reset --hard" before creating the bundle? [y/n]: ' answer
    case $answer in
        y ) git clean  -f -d
            git reset --hard
            git pull
            break;;
        n ) break;;
        * ) echo 'Please answer "y" or "n".';;
    esac
done

build=$(grep 'build: [0-9]\{4,5\}' system/bundle.data | sed 's/  build: //g')
bundle_name="../effcore-$build.zip"

rm $bundle_name

{ find .                        -maxdepth 1 -type f      -name '.editorconfig'        & \
  find .                        -maxdepth 1 -type f      -name '.gitattributes'       & \
  find .                        -maxdepth 1 -type f      -name '.gitignore'           & \
  find .                        -maxdepth 1 -type f      -name '.htaccess'            & \
  find .                        -maxdepth 1 -type f      -name '.nginx'               & \
  find .                        -maxdepth 1 -type f      -name 'composer.json'        & \
  find .                        -maxdepth 1 -type f      -name 'index.php'            & \
  find .                        -maxdepth 1 -type f      -name 'license.md'           & \
  find .                        -maxdepth 1 -type f      -name 'web.config'           & \
  find dynamic                  -maxdepth 1 -type f      -name 'readme.md'            & \
  find dynamic/cache            -maxdepth 1 -type f      -name 'readme.md'            & \
  find dynamic/data             -maxdepth 1 -type f      -name 'readme.md'            & \
  find dynamic/files            -maxdepth 1 -type f      -name 'readme.md'            & \
  find dynamic/logs             -maxdepth 1 -type f      -name 'readme.md'            & \
  find dynamic/tmp              -maxdepth 1 -type f      -name 'readme.md'            & \
  find modules                  -maxdepth 1 -type f      -name 'readme.md'            & \
  find modules/vendors          -maxdepth 1 -type f      -name 'module.data'          & \
  find modules/vendors/packages -maxdepth 1 -type f      -name 'readme.md'            & \
  find modules/vendors/packages -maxdepth 1 -type f      -name 'post-install-cmd.php' & \
  find modules/vendors/backend              -type f -not -path '*/.*'                 & \
  find modules/vendors/data                 -type f -not -path '*/.*'                 & \
  find modules/examples                     -type f -not -path '*/.*'                 & \
  find readme                               -type f -not -path '*/.*'                 & \
  find shell                                -type f -not -path '*/.*'                 & \
  find system                               -type f -not -path '*/.*';                  \
} | zip -9 $bundle_name -@
