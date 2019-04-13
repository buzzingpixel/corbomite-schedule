# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2019-04-12
## New
- APP_BASE_PATH constant is now no longer required to be defined and your project's base path will be determined  based on the location of the composer vendor directory if it is not defined.
### Fixed
- Fixed a bug where an error could be thrown on populating schedule models with DB values if a model for a record did not exist
### Changed
- Removed deprecated calls to Corobomite DI methods
- Added 100% code coverage unit testing

## [1.1.0] - 2019-01-22
### Fixed
- Fixed minor issues with migration
### Changed
- Updated table to not use auto-incrementing integer as primary key

## [1.0.2] - 2019-01-11
### Changed
- Internal refactoring to use the Corbomite Config Collector

## [1.0.1] - 2019-01-11
### Fixed
- Fixed an incorrectly namespaced service

## [1.0.0] - 2019-01-11
### New
- Initial Release
