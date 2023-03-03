# Changelog

All notable changes to this app will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

## [2.5.0] - 2023-03-03

### Added

- [#382](https://github.com/owncloud/files_texteditor/issues/382) - Implement persistent locking

### Changed

- Bump firebase/php-jwt from 5.5.1 to 6.0.0


## [2.4.1] - 2021-10-12

### FIXED
-  v2.4.0 is causing issues on ownCloud 10.8 [#361](https://github.com/owncloud/files_texteditor/issues/361)

## [2.4.0] - 2021-10-05

### Added
- Implement public link support - [#355](https://github.com/owncloud/files_texteditor/pull/355)


## [2.3.1] - 2021-04-28

### Added

- Adjusted the text shown for opening a file from "Edit" to "Open in Text Editor". - [#331](https://github.com/owncloud/files_texteditor/pull/331)
- Extra detection of codings for Chinese and Japanese character sets. [#333](https://github.com/owncloud/files_texteditor/pull/333)


## [2.3.0] - 2018-11-30

### Added
- Support for PHP 7.1 and 7.2 - [#216](https://github.com/owncloud/files_texteditor/pull/216)

### Changed
- Set max version to 10 because core platform switches to semver

### Removed
- Removed unused imports in the code - [#215](https://github.com/owncloud/files_texteditor/pull/215)

## [2.2.1] - 2017-09-15

### Fixed

#### UI Related
- Add viewBox to app icon for proper scaling in firefox [#187] (https://github.com/owncloud/files_texteditor/pull/187)
- Revert "added mime type for .htaccess files" [#200] (https://github.com/owncloud/files_texteditor/pull/200)

#### App Build and Test
- Implement automated UI test environment [#204] (https://github.com/owncloud/files_texteditor/pull/204)

### Updated
- Update ace and replace searchbox extension for search support [#196] (https://github.com/owncloud/files_texteditor/pull/196)

[Unreleased]: https://github.com/owncloud/files_texteditor/compare/v2.5.0...master
[2.5.0]: https://github.com/owncloud/files_texteditor/compare/v2.4.1...v2.5.0
[2.4.1]: https://github.com/owncloud/files_texteditor/compare/v2.4.0...v2.4.1
[2.4.0]: https://github.com/owncloud/files_texteditor/compare/v2.3.2...v2.4.0
[2.3.2]: https://github.com/owncloud/files_texteditor/compare/v2.3.1...v2.3.2
[2.3.1]: https://github.com/owncloud/files_texteditor/compare/v2.3.0...v2.3.1
[2.3.0]: https://github.com/owncloud/files_texteditor/compare/v2.2.1...v2.3.0
[2.2.1]: https://github.com/owncloud/files_texteditor/compare/v2.2...v2.2.1
