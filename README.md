# memorize

![Packagist Version](https://img.shields.io/packagist/v/hyqo/memorize?style=flat-square)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/hyqo/memorize?style=flat-square)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/hyqo/memorize/run-tests?style=flat-square)

A small function for memorize a heavy computed value

## Install

```sh
composer require hyqo/memorize
```

## Usage
Without `memorize`, each call `getValue()` method will return a new `$counter` value, nothing changes:
```php
class Foo() {
    private $counter = 0;

    public function getValue(): int
    {
        return $this->counter++;
    }
};

$foo = new Foo();
$foo->getValue(); // 0
$foo->getValue(); // 1
$foo->getValue(); // 2
```

With `memorize`, each call will return the first calculated value:
```php
use function Hyqo\Memorize\memorize;

class Foo() {
    private $counter = 0;

    public function getValue(): int
    {
        return memorize(function () {
            return $this->counter++;
        });
    }
};

$foo = new Foo();
$foo->getValue(); // 0
$foo->getValue(); // 0
$foo->getValue(); // 0
```
