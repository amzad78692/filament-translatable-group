# Filament Translatable Group

[![Latest Version on Packagist](https://img.shields.io/packagist/v/amzad/filament-translatable-group.svg?style=flat-square)](https://packagist.org/packages/amzad/filament-translatable-group)

Automatically generate side-by-side grouped fields for each locale — no tabs, just columns.

Works seamlessly with [spatie/laravel-translatable](https://github.com/spatie/laravel-translatable) and [lara-zeus/translatable-pro](https://larazeus.com/translatable-pro), but can be used standalone as well.

## Installation

```bash
composer require amzad/filament-translatable-group
```

Then in any registered service provider `boot()` method configure the following:

```php
use Amzad\FilamentTranslatableGroup\TranslatableGroup;

TranslatableGroup::configureUsing(function (TranslatableGroup $component) {
    $component
        ->localesLabels([
            'ar' => __('locales.ar'),
            'en' => __('locales.en'),
        ])
        ->locales(['ar', 'en']);
});
```

## Usage

```php
use Amzad\FilamentTranslatableGroup\TranslatableGroup;

// Single Field — via macro
TextInput::make('title')
    ->translatableGroup();

// Multiple Fields — via component
TranslatableGroup::make()
    ->schema([
        Forms\Components\TextInput::make('title'),
        Forms\Components\Textarea::make('content'),
    ]);
```

Each field is cloned for every locale and labelled `"{Original Label} ({Locale Label})"`. Fields are arranged in a 2-column grid by default (configurable via `->columns()`).

## Customizations

### Modify fields per locale

```php
use Amzad\FilamentTranslatableGroup\TranslatableGroup;
use Filament\Forms\Components\Field;

$customizeField = function (Field $component, string $locale) {
    // ...
};

// Globally in boot method
TranslatableGroup::configureUsing(function (TranslatableGroup $component) use ($customizeField) {
    $component->modifyFieldsUsing($customizeField);
});

// Single Field
TextInput::make('title')
    ->translatableGroup(modifyFieldsUsing: $customizeField);

// Multiple Fields
TranslatableGroup::make()
    ->modifyFieldsUsing($customizeField)
    ->schema([
        Forms\Components\TextInput::make('title'),
        Forms\Components\Textarea::make('content'),
    ]);
```

### Override the default locales

```php
$localesFn = function () {
    return ['ar', 'en'];

    // Also with labels:
    return [
        'ar' => 'Arabic',
        'en' => 'English',
    ];
};

// Single Field
TextInput::make('title')
    ->translatableGroup(locales: $localesFn);

// Multiple Fields
TranslatableGroup::make()
    ->locales($localesFn)
    ->schema([
        Forms\Components\TextInput::make('title'),
        Forms\Components\Textarea::make('content'),
    ]);
```

### Override the column count

```php
TranslatableGroup::make()
    ->columns(3)
    ->schema([...]);
```

## Pre-made configurations

### Direction by locale

Automatically sets `direction: rtl` for Arabic locales, `ltr` for all others:

```php
TranslatableGroup::configureUsing(function (TranslatableGroup $component) {
    $component->addDirectionByLocale();
});
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
