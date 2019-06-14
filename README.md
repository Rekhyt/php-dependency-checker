# PHP Dependency Checker
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://travis-ci.org/Rekhyt/php-dependency-checker.svg?branch=master)](https://travis-ci.org/Rekhyt/php-dependency-checker)
[![Coverage Status](https://coveralls.io/repos/github/Rekhyt/php-dependency-checker/badge.svg?branch=master)](https://coveralls.io/github/Rekhyt/php-dependency-checker?branch=master)
[![GitHub (pre-)release](https://img.shields.io/github/release/Rekhyt/php-dependency-checker/all.svg)](https://github.com/Rekhyt/php-dependency-checker/releases)

A CLI tool to check your PHP project's dependencies for known security flaws.

## How Does It Work?
It will take your composer.lock file, send it to https://security.symfony.com/check_lock
and present the results.

For build & deployment integration it will exit with an exit code of
* 0 if no vulnerabilities were found
* 1 if vulnerabilities found.

To be able to build even if your dependencies have vulnerabilities that don't affect your project (flawed code not used)
or there is no update, yet, an exception list can be defined.

For more information on how that API is working, refer to https://github.com/sensiolabs/security-checker.

## Installation
### Download PHAR Archive
You can download a ready-to-use PHAR from the
[releases page](https://github.com/Rekhyt/php-dependency-checker/releases).

### Install As Dependency
To have this as a dependency in your project require it via composer:

    composer require rekhyt/php-dependency-checker

## Usage
### CLI Tool
Pass the path to your composer.lock file (NOT composer.json). Pass the optional --exclude-from parameter with the path
to a file that contains the exceptions.

    ./php-dependency-checker --exclude-from exclusion-list composer.lock

### Composer Binary
Run directly from console like this:

    ./vendor/bin/php dependency-checker --exclude-from exclusion-list composer.lock

Or add a composer script to your `composer.json` file:

    {
      "scripts": {
        ...,
        "audit": "php-dependency-checker --exclude-from exclusion-list composer.lock"
      }
    }

Then run:

    composer audit

### In Your Code
to be done

## Package Exception List Format
Put one package per line as referenced in composer.json:

    phpmailer/phpmailer
    psr/log
    psr/cache

## Changelog
See the [CHANGELOG](CHANGELOG.md) if you're looking for a list of changes in this project.

## Contributing
See the [CONTRIBUTING](CONTRIBUTING.md) file for more information on how to help making this a better piece of software.

## License
The PHP Dependency Checker is available under the MIT License.

See the [LICENSE](LICENSE) file for more information.
