find ../dynamic/data/ -not -name 'readme.md' -delete
cp /dev/null ../dynamic/data/data.sqlite
./cache_clear.sh
