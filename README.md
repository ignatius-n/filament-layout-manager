# Not published - Under active development.

# FilamentPHP User Customizable Page

[![Latest Version on Packagist](https://img.shields.io/packagist/v/asosick/reorder-widgets.svg?style=flat-square)](https://packagist.org/packages/asosick/reorder-widgets)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/asosick/reorder-widgets/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/asosick/reorder-widgets/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/asosick/reorder-widgets/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/asosick/reorder-widgets/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/asosick/reorder-widgets.svg?style=flat-square)](https://packagist.org/packages/asosick/reorder-widgets)


### Allows users to customize and save their own dashboards composed of livewire components.
![demo.gif](demo.gif)
## Installation

You can install the package via composer:

```bash
#COMING SOON
#composer require asosick/reorder-widgets
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="reorder-widgets-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="reorder-widgets-views"
```

[//]: # (This is the contents of the published config file:)

[//]: # ()
[//]: # (```php)

[//]: # (return [)

[//]: # (];)

[//]: # (```)
[//]: # ()
[//]: # (## Usage)

[//]: # ()
[//]: # (```php)

[//]: # ($reorderWidgets = new Asosick\ReorderWidgets&#40;&#41;;)

[//]: # (echo $reorderWidgets->echoPhrase&#40;'Hello, Asosick!'&#41;;)

[//]: # (```)

[//]: # ()
[//]: # (## Testing)

[//]: # ()
[//]: # (```bash)

[//]: # (composer test)

[//]: # (```)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [August](https://github.com/asosick)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
