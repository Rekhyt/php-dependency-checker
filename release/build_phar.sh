#!/usr/bin/env bash

rm -rf php-dependency-checker.phar

cd release
wget "https://github.com/MacFJA/PharBuilder/releases/download/0.2.6/phar-builder.phar"
chmod a+x phar-builder.phar
./phar-builder.phar package ../composer.json
rm phar-builder.phar
