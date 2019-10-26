# PhpNetRandom
A port of .Net Random class to php. Useful for migration purposes or comparison.  Using the same seed, you get the same random values on .Net and php.

## Installation

PhpNetRandom has no external dependencies. It can be included in a project directly adding this line to a php file:
```php
<?php
include 'NetRandom.php';

```
It's also available on [Packagist](https://packagist.org/packages/rodrigoq/phpnetrandom), and can be installed via [Composer](https://getcomposer.org). Just add this line to your `composer.json` file:

```json
"rodrigoq/phpnetrandom": "~1.0"
```

or run

```sh
composer require rodrigoq/phpnetrandom
```

## Details
The [NetRandom.php](https://github.com/rodrigoq/phpnetrandom/blob/master/src/NetRandom.php) class is a direct port of the public code of [original C# .Net Random class](https://referencesource.microsoft.com/#mscorlib/system/random.cs).

There are many [tests](https://github.com/rodrigoq/phpnetrandom/tree/master/tests) that can be run with [PHPUnit](https://phpunit.de). There is also a [C# .Net solution](https://github.com/rodrigoq/phpnetrandom/tree/master/tests/NetRandom) with a Random command line utility for testing purposes. To run the full test suite you have to compile that solution.

## Usage
```php
<?php

include 'NetRandom.php';

use NetRandom\NetRandom;

$random = new NetRandom(); //seed is optional.

// You can add max or, min and max parameters.
echo $random->Next() . PHP_EOL;

echo $random->NextDouble() . PHP_EOL;

$bytes = array_fill(0, 10, 0);
$random->NextBytes($bytes);
var_dump($bytes);

```

## License
This software is distributed under the [LGPL 3.0](http://www.gnu.org/licenses/lgpl-3.0.html) license. Please read LICENSE for information on the software availability and distribution.

