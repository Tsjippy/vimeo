# Changelog
## [Unreleased] - yyyy-mm-dd

### Added

### Changed
- pass sanitized request to tsjippy-frontend-content-after-post-save
- sanitize post on original function

### Fixed

### Updated

## [10.3.0] - 2026-06-25


## [10.2.9] - 2026-06-24


## [10.2.8] - 2026-06-23


## [10.2.7] - 2026-06-23


## [10.2.6] - 2026-06-23


## [10.2.5] - 2026-06-23


## [10.2.4] - 2026-06-21


## [10.2.3] - 2026-06-19


### Added
- use wp_file_system

## [10.2.2] - 2026-06-18


### Changed
- hook and filter name update
- prefix all hooks with plugin name

## [10.2.1] - 2026-06-15


## [10.2.0] - 2026-06-15


## [10.1.9] - 2026-06-15


## [10.1.8] - 2026-06-15


### Changed
- transform inputdata now requires element

## [10.1.7] - 2026-06-13


### Changed
- replaced parse_url with wp_parse_url
- prefix meta key in get_users

### Fixed
- shared code loader

## [10.1.6] - 2026-06-11


### Added
- placeholder for textdomain

### Changed
- prefixed post metas and shortcodes

### Fixed
- prefix meta_query

## [10.1.5] - 2026-06-09


### Added
- shared functionality loader

### Changed
- comply to coding standards
- code layout
- namespaced all constants
- sanitize all posts and get vars
- js update

### Fixed
- spacing problem
- space before dot bug

## [10.1.4] - 2026-06-03


### Added
- echo escaping

### Changed
- addSaveButton with echo param

## [10.1.3] - 2026-06-01


### Changed
- merged hooks.md into readme.md

### Fixed
- added domain to __ function

## [10.1.2] - 2026-06-01


### Changed
- loading libraries is now done in shared-functionality plugin

## [10.1.1] - 2026-05-30


### Changed
- do not store get_plugin_data in global variable

## [10.1.0] - 2026-05-29


### Added
- wp_unslash

## [10.0.9] - 2026-05-17


### Added
- deactivation hook

## [10.0.8] - 2026-05-12


### Changed
- permission callback for rest api

## [10.0.7] - 2026-05-11


### Changed
- js update

### Updated
- readme

## [10.0.6] - 2026-05-07


### Changed
- replaced sweetalert
- js update

## [10.0.5] - 2026-05-06


### Fixed
- textdomain

## [10.0.4] - 2026-05-05


## [10.0.2] - 2026-05-05


### Fixed
- vimeo api client id

## [10.0.1] - 2026-05-03


### Changed
- removed the redirection at activation as it is done by the share plugin
- print array vs error_log
- use shared github workflows

## [10.0.0] - 2026-05-01


### Changed
- main plugin name from sim-base to tsjippy-shared-functionality
- module to plugin  
- exclude .vscode from releases
- updated github workflow versions

## [8.1.9] - 2025-12-01


### Changed
- mkdir replace

## [8.1.8] - 2025-11-26


### Changed
- composer updated
- lib update

## [8.1.7] - 2025-11-21


### Added
- support for Local

## [8.1.6] - 2025-11-04


### Changed
- render loader image using js
- clearer data attributes

## [8.1.4] - 2025-10-16


### Fixed
- bug in receiving rest api response

## [8.1.3] - 2025-10-13


### Changed
- classnames
- data attribute names

### Fixed
- bugs

## [8.1.2] - 2025-09-26


### Changed
- _ for - in classnames

## [8.1.1] - 2025-09-25


### Changed
- loader images
- js generated loader

## [8.0.9] - 2025-08-06


### Changed
- less niceselect code

## [8.0.8] - 2025-08-01


### Fixed
- imports

## [8.0.7] - 2025-07-25


### Fixed
- better error handling

## [8.0.6] - 2025-05-15


### Added
- respapi prefix for downloading videos

### Fixed
- filenames of downloaded videos

## [8.0.5] - 2025-02-13


### Changed
- module menu hooks

## [8.0.4] - 2024-11-22


### Changed
- removed anonymous functions

## [8.0.3] - 2024-11-21


### Changed
- removed unanymous functions

## [8.0.2] - 2024-10-11


### Changed
- redering of asset urls

## [8.0.1] - 2024-10-07


### Changed
- deps

## [8.0.0] - 2024-10-03
    ### Added
    - First commit
