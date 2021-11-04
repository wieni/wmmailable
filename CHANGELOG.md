# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.3.2] - 2021-11-04
### Fixed
- Override the active theme when rendering mails. The `theme` value from settings was previously only used to look for
  templates, but now the theme is actually being activated before rendering mails.

## [2.3.1] - 2021-10-22
### Fixed
- Fix the `lorenzo/pinky` integration

## [2.3.0] - 2021-10-21
### Fixed
- Add support for the `lorenzo/pinky` package to be used in the Foundation for Emails formatter

## [2.2.3] - 2021-10-19
### Fixed
- Fix content in mail templates not being rendered in the right language

## [2.2.2] - 2021-10-18
### Fixed
- Fix error when trying to send a mailable with backslashes in its plugin ID

## [2.2.1] - 2021-07-09
### Fixed
- Fix backslashes not being allowed in plugin ID's

## [2.2.0] - 2020-12-22
### Added
- Add logging of sent mails
- Add PHPStan config

## [2.1.1] - 2020-07-17
### Fixed
- Fix 'Notice: Undefined index: id' when no id parameter is specified in the annotation

## [2.1.0] - 2020-07-16
### Added
- Allow using class name as plugin id
- Add documentation on theming mails

### Changed
- Use class name as plugin id when id is not specified

## [2.0.2] - 2020-05-14
### Fixed
- Fix wrong return types

## [2.0.1] - 2020-05-14
### Fixed
- Add missing mailsystem dependency

## [2.0.0] - 2020-02-17
### Added
- Add a changelog & upgrade guide

### Changed
- Change the mailer service to allow sending more than one mail
  ([#4](https://github.com/wieni/wmmailable/issues/4))
- Change the PHP version constraint to 7.1
- Change the mailer services to inject logger channel directly instead
  of injecting the logger factory
- Remove maintainers section & update security email address in README
  
## [1.9.1] - 2019-11-22
### Changed
- Increase drupal/core version constraint to support version 9

## [1.9.0] - 2019-11-22
### Added
- Add the possibility to specify mailable from & reply-to
- Add language-specific template support
- Add core_version_requirement parameter to info.yml

### Changed
- Render mails before queuing instead of after

### Fixed
- Fix issue with FoundationForEmailsMailableFormatter where css is
  inlined before the mail being compiled
 
## [1.8.1] - 2019-02-07
### Changed
- Remove duplicate code

## [1.8.0] - 2019-01-28
### Added
- Add the possibility to specify the mailable content type and charset
- Add config for default content type and charset
 
## [1.7.3] - 2019-01-28
### Fixes
- Fix issue where HTML emails are double encoded

## [1.7.2] - 2019-01-08
### Changed
- Change the PHP version constraint

## [1.7.1] - 2019-01-08
### Fixed
- Improve handling of plain text mails in the mail formatter

## [1.7.0] - 2019-01-08
### Added
- Add PHP 7.0 version constraint
- Add alter events

### Changed
- Change the hook name

### Fixed
- Fix the documentation about hooks

## [1.6.0] - 2019-12-21
### Added
- Add the possibility to configure the theme used for templates

## [1.5.0] - 2019-12-21
### Added
- Allow taking over other module's mails at the time of sending

### Changed
- Make subject & langcode optional
- Replace the mail body instead of appending

## [1.4.0] - 2019-12-11
### Added
- Add the possibility to specify a custom template location

## [1.3.1] - 2019-12-10
### Changed
- Set default value for the from property on mailables

### Removed
- Remove unused template property on mailables

## [1.3.0] - 2019-12-10
### Added
- Add the possibility to throw a DiscardMailException in the build
  method of a mailable ([#2](https://github.com/wieni/wmmailable/issues/2))

## [1.2.1] - 2019-11-30
### Changed
- Change formatter to use the configured line endings for mails

## [1.2.0] - 2019-11-23
### Added
- Add ability to override mail sender

## [1.1.1] - 2019-11-22
### Fixed
- Fix notice

## [1.1.0] - 2019-11-21
### Added
- Add mail formatter plugins

## [1.0.1] - 2019-11-19
### Removed
- Remove wmcontroller dependency

## [1.0.0] - 2019-11-19
Initial release
