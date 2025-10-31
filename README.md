# idcard-manage-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/idcard-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)
[![PHP Version Require](https://img.shields.io/packagist/dependency-v/tourze/idcard-manage-bundle/php?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)
[![License](https://img.shields.io/packagist/l/tourze/idcard-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/idcard-manage-bundle/ci.yml?branch=master&style=flat-square)](https://github.com/tourze/idcard-manage-bundle/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/idcard-manage-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/tourze/idcard-manage-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/idcard-manage-bundle?style=flat-square)](https://codecov.io/gh/tourze/idcard-manage-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/idcard-manage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/idcard-manage-bundle)

A Symfony bundle for Chinese ID card management and validation.

## Features

- ✅ Chinese ID card format validation
- ✅ Birthday extraction with custom separator support
- ✅ Gender detection with enum support (GB/T 2261 standard)
- ✅ Full Symfony framework integration
- ✅ Comprehensive test coverage
- ⚠️ Two-element verification (placeholder for external API integration)

## Installation

```bash
composer require tourze/idcard-manage-bundle
```

## Quick Start

```php
<?php

use Tourze\IdcardManageBundle\Service\IdcardService;
use Tourze\GBT2261\Gender;

class YourController
{
    public function __construct(
        private IdcardService $idcardService
    ) {}

    public function validateIdcard(string $idNumber): void
    {
        // Validate ID card number
        if ($this->idcardService->isValid($idNumber)) {
            echo "Valid ID card number";
            
            // Get birthday
            $birthday = $this->idcardService->getBirthday($idNumber);
            if ($birthday !== false) {
                echo "Birthday: " . $birthday;
            }
            
            // Get gender
            $gender = $this->idcardService->getGender($idNumber);
            match ($gender) {
                Gender::MAN => echo "Male",
                Gender::WOMAN => echo "Female",
                default => echo "Unknown gender",
            };
        } else {
            echo "Invalid ID card number";
        }
    }
}
```

## Configuration

Add the bundle to your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    Tourze\IdcardManageBundle\IdcardManageBundle::class => ['all' => true],
];
```

## API Documentation

### Service Methods

#### `isValid(string $number): bool`
Validates if the ID card number follows basic format rules.

#### `getBirthday(string $number, string $sep = '-'): string|false`
Extracts the birthday from the ID card number.
- `$number`: The ID card number
- `$sep`: Date separator (default: '-')
- Returns: Birthday string or `false` if invalid

#### `getGender(string $number): Gender`
Extracts the gender from the ID card number.
- Returns: `Gender` enum value (MAN, WOMAN, UNKNOWN)

#### `twoElementVerify(string $certName, string $certNo): bool`
Verifies the ID card number with the holder's name using external API.
- Currently returns `false` as it requires third-party API integration

## Testing

```bash
# Run tests
./vendor/bin/phpunit packages/idcard-manage-bundle/tests

# Run static analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/idcard-manage-bundle
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Dependencies

- PHP 8.1+
- Symfony 6.4+
- [ionepub/idcard](https://github.com/ionepub/idcard) - Core ID card validation library
- [tourze/gb-t-2261](../gb-t-2261) - Gender enum definitions

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.