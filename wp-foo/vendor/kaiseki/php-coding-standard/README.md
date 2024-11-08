# Kaiseki PHP Coding Standard

## PHP-CS-Fixer
Add the following `.php-cs-fixer.dist.php` file to your project's root. This will create a basic configuration with a Finder that includes everything in . and excludes vendor.:

```php
<?php

declare(strict_types=1);

use Kaiseki\CodingStandard\PhpCsFixer\Config;

return Config::get();
```

 If you need a more granular directory specification, you can pass a custom Finder. You can also override rules by passing your own as the second argument:

```php
<?php

declare(strict_types=1);

use Kaiseki\CodingStandard\PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__ . '/src');

$rules = [
    // Your custom rules here
];

return Config::get($finder, $rules);
```

Add this script to your `composer.json`:
```json
{
    "scripts": {
        "cs-check": "php-cs-fixer fix --dry-run",
        "cs-fix": "php-cs-fixer fix --allow-risky=yes"
    }
}
```
