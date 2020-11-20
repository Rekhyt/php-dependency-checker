# Changelog

## [Unreleased]


## [1.0.4]
### Fixed
- empty CVE causes checker to crash
- PHAR execution not working anymore when file was renamed

### Changed
- when the exclusion file cannot be found that will just show an error but won't break anymore

## [1.0.3]
### Changed
- lowest required PHP version changed from 7.2 to 5.6
- uses phpunit/phpunit ^5
- uses rekhyt/ddd-base ^1.0.1

## [1.0.2]
### Fixed
- auto-loading when run as composer vendor

## [1.0.1]
### Added
- `php-dependency-checker` to the Composer `bin` section

### Changed
- all dependencies are now required in stable versions

## [1.0.0]
### Added
- test coverage tracking / badge via PHPUnit & coveralls.io

### Changed
- uses PHPUnit 7.x
- minor changes in README.md

## [1.0.0-beta.1]
### Fixed
- updated the default Sensiolabs API URL as the old one has gone out of service
- introducing proper semver versioning, huh?

## [0.2-beta]
### Changed
- refactoring
- code cleanup

## [0.1-beta1]
### Added
- this changelog

### Changed
- fixed a bug that caused the exit code to always be 127 in case of an error
- replaced "to be done" comment in the "Changelog" section of README.md with a link to CHANGELOD.md

### Fixed
- project would not be executable on some configurations due to a bug in PHP 5.6

## 0.1-beta
### Added
- first beta candidate of the php-dependency-checker

[Unreleased]: https://github.com/Rekhyt/php-dependency-checker/compare/1.0.4...HEAD
[1.0.4]: https://github.com/Rekhyt/php-dependency-checker/compare/1.0.4...1.0.3
[1.0.3]: https://github.com/Rekhyt/php-dependency-checker/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/Rekhyt/php-dependency-checker/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/Rekhyt/php-dependency-checker/compare/1.0.0...1.0.1
[1.0.0]: https://github.com/Rekhyt/php-dependency-checker/compare/1.0.0-beta.1...1.0.0
[1.0.0-beta.1]: https://github.com/Rekhyt/php-dependency-checker/compare/0.2-beta...1.0.0-beta.1
[0.2-beta]: https://github.com/Rekhyt/php-dependency-checker/compare/0.1-beta1...0.2-beta
[0.1-beta1]: https://github.com/Rekhyt/php-dependency-checker/compare/0.1-beta...0.1-beta1
