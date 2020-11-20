#!/usr/bin/env bash

rm -rf php-dependency-checker.phar

cd release || return
wget "https://github.com/MacFJA/PharBuilder/releases/download/0.2.8/phar-builder.phar"
chmod a+x phar-builder.phar
php -d phar.readonly=0 ./phar-builder.phar package ../composer.json
rm phar-builder.phar
