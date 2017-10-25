# TJSON Parser

**A TJSON parser written in PHP, using [Phlexy](https://github.com/nikic/Phlexy) as JSON lexer.**

<a href="https://github.com/qligier/tjson-parser" alt="GitHub release">
    <img src="https://img.shields.io/github/release/qligier/tjson-parser.svg" />
</a>
<a href="https://travis-ci.org/qligier/tjson-parser" alt="Build Status">
    <img src="https://travis-ci.org/qligier/tjson-parser.svg" />
</a>
<a href="https://www.gnu.org/licenses/gpl-3.0" alt="License: GPL v3">
    <img src="https://img.shields.io/badge/License-GPL%20v3-blue.svg" />
</a>

## Requirements

- PHP 7.0 at minimum is needed;
- The GMP extension.

## Quick start

### Installation

Install the library with [composer](https://getcomposer.org):

`composer require qligier/tjson-parser`

### Usage

### Data mapping

- _Boolean_ values are returned as boolean;
- _Binary_ values are decoded and returned as string;
- _FloatingPoint_ values are returned as float;
- _Integer_ values are returned as _GMP_ instance;
- _UnicodeString_ values are returned as string;
- _Timestamp_ values are returned as _DateTime_ instance;
- _Array_ values are returned as indexed array;
- _Object_ values are returned as associative array;
- _Set_ values are returned as indexed array.

## Compliance

This library tries to be fully compliant with the current draft-tjson-spec (April 15, 2017).
Nonetheless, the following non-compliances are not excluded:

- The library accepts various formatting for `FloatingPoint` and `Integer` values that [could be
forbidden](https://github.com/tjson/tjson-spec/issues/53) by the spec.


## Development

To check the library with [Psalm](https://github.com/vimeo/psalm) (a static analysis tool for
finding errors):
```
./vendor/bin/psalm
```

To execute the unit tests with [PHPUnit](https://github.com/sebastianbergmann/phpunit) (an unit
testing framework):
```
./vendor/bin/phpunit
```

To check the library with [Infection](https://github.com/infection/infection) (a mutation
testing framework):
```
./vendor/bin/infection --min-msi=100 --min-covered-msi=100
```

To check and fix the coding style with [PHP Coding Standards Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
(a tool to automatically fix PHP coding standards issues):
```
./vendor/bin/php-cs-fixer fix . --dry-run --diff
./vendor/bin/php-cs-fixer fix .
```
