git -C ../ reset --hard
git -C ../ pull
./cache_clear.sh

{ find ../dynamic     -type f      -name 'readme.mark'    & \
  find ../shell       -type f -not -name '.*'             & \
  find ../system      -type f -not -name '.*'             & \
  find .. -maxdepth 1 -type f -not -name '.*'             & \
  find .. -maxdepth 1 -type f      -name '.editorconfig'  & \
  find .. -maxdepth 1 -type f      -name '.gitattributes' & \
  find .. -maxdepth 1 -type f      -name '.gitignore'     & \
  find .. -maxdepth 1 -type f      -name '.htaccess'      & \
  find .. -maxdepth 1 -type f      -name '.nginx'; } \
| zip ../../bundle-0000.zip -@