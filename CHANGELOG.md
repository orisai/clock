# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/orisai/clock/compare/1.1.1...HEAD)

## [1.1.1](https://github.com/orisai/clock/compare/1.1.0...1.1.1) - 2022-12-09

### Changed

- Composer
	- allows PHP 8.2

## [1.1.0](https://github.com/orisai/clock/compare/1.0.0...1.1.0) - 2022-11-25

- [PSR-20](https://www.php-fig.org/psr/psr-20/) compatibility
  - `Clock` extends `Psr\Clock\ClockInterface`

## [1.0.0](https://github.com/orisai/clock/releases/tag/1.0.0) - 2022-08-19

### Added

- `Clock` interface
	- `SystemClock`
	- `FrozenClock`
	- `MeasurementClock`
- `ClockHolder`
- `now()` shortcut function
