# Laravel Inertia Table

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hristijans/laravel-inertia-table.svg?style=flat-square)](https://packagist.org/packages/hristijans/laravel-inertia-table)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/hristijans/laravel-inertia-table/run-tests?label=tests)](https://github.com/hristijans/laravel-inertia-table/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/hristijans/laravel-inertia-table/Check%20&%20fix%20styling?label=code%20style)](https://github.com/hristijans/laravel-inertia-table/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hristijans/laravel-inertia-table.svg?style=flat-square)](https://packagist.org/packages/hristijans/laravel-inertia-table)

A robust and flexible package for displaying tables in Laravel with Inertia.js and Vue.js. Inspired by FilamentPHP's table builder, but specifically designed for Inertia.js applications.

## Features

- Easy to use table builder API
- Fluent syntax for adding columns, actions, and filters
- Support for pagination and sorting
- Preservable state in session
- Built for Inertia.js with Vue 3 using Composition API
- Compatible with Laravel 10, 11, 12 and Inertia.js v1 and v2
- Automatic query building based on filters, sorting, and search

## Installation

You can install the package via composer:

```bash
composer require hristijans/laravel-inertia-table
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-inertia-table-config"
```

If you need to customize the views:

```bash
php artisan vendor:publish --tag="laravel-inertia-table-views"
```

## Usage

### Basic Example

```php
use Hristijans\LaravelInertiaTable\Table;
use Hristijans\LaravelInertiaTable\Columns\TextColumn;
use Hristijans\LaravelInertiaTable\Columns\BadgeColumn;
use Hristijans\LaravelInertiaTable\Actions\ButtonAction;
use Hristijans\LaravelInertiaTable\Filters\SelectFilter;

public function index()
{
    return inertia('Users/Index', [
        'users' => Table::make('users')
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                BadgeColumn::make('status')
                    ->color('primary')
                    ->states([
                        'active' => ['color' => 'success', 'icon' => 'fas fa-check'],
                        'inactive' => ['color' => 'danger', 'icon' => 'fas fa-times'],
                    ]),
                TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function ($query, $value) {
                        return $query->where('status', $value);
                    }),
            ])
            ->actions([
                ButtonAction::make('edit')
                    ->label('Edit')
                    ->icon('fas fa-edit')
                    ->url(route('users.edit', [':id'])),
                ButtonAction::make('delete')
                    ->label('Delete')
                    ->icon('fas fa-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->url(route('users.destroy', [':id'])),
            ])
            ->query(User::query())
            ->perPage(10)
            ->preserveState()
            ->render()
    ]);
}
```

### In Your Inertia Vue Component

```vue
<template>
  <div>
    <h1>Users</h1>
    <InertiaTable :table="$page.props.table" />
  </div>
</template>

<script setup>
import { InertiaTable } from 'hristijans/laravel-inertia-table';
</script>
```

## Available Column Types

### TextColumn

```php
TextColumn::make('name')
    ->label('Full Name') // Optional, defaults to title-cased name
    ->sortable() // Makes column sortable
    ->searchable() // Makes column searchable
    ->default('N/A') // Default value if empty
    ->truncate(20) // Truncates text to specified length
```

### BadgeColumn

```php
BadgeColumn::make('status')
    ->color('primary') // Default color: primary, success, danger, warning
    ->icon('fas fa-check') // Optional icon
    ->states([
        'active' => ['color' => 'success', 'icon' => 'fas fa-check'],
        'inactive' => ['color' => 'danger', 'icon' => 'fas fa-times'],
    ])
```

## Available Action Types

### ButtonAction

```php
ButtonAction::make('edit')
    ->label('Edit User') // Optional, defaults to title-cased name
    ->icon('fas fa-edit') // Optional icon
    ->url('/users/:id/edit') // URL with :id placeholder
    ->color('primary') // primary, danger, success, warning
    ->size('md') // sm, md, lg
    ->requiresConfirmation() // Shows confirmation dialog
```

## Available Filter Types

### SelectFilter

```php
SelectFilter::make('status')
    ->label('User Status') // Optional, defaults to title-cased name
    ->options([
        'active' => 'Active Users',
        'inactive' => 'Inactive Users',
    ])
    ->default('active') // Default selected value
    ->multiple() // Allow multiple selections
    ->query(function ($query, $value) {
        return $query->where('status', $value);
    })
```

## Preserving State

You can enable state preservation in the session:

```php
Table::make('users')
    ->preserveState() // Preserves filters, sorting, and pagination in session
    // ...
```

## Customization

### Custom Column Types

You can create your own column types by extending the `Column` abstract class:

```php
use Hristijans\LaravelInertiaTable\Columns\Column;

final class ImageColumn extends Column
{
    protected ?int $width = 40;
    
    protected ?int $height = 40;
    
    protected bool $rounded = false;
    
    public function width(int $width): self
    {
        $this->width = $width;
        
        return $this;
    }
    
    public function height(int $height): self
    {
        $this->height = $height;
        
        return $this;
    }
    
    public function rounded(bool $rounded = true): self
    {
        $this->rounded = $rounded;
        
        return $this;
    }
    
    public function getType(): string
    {
        return 'image';
    }
    
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'width' => $this->width,
            'height' => $this->height,
            'rounded' => $this->rounded,
        ]);
    }
}
```

Then create a corresponding Vue component in your application.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Hristijan Stojanoski](https://github.com/hristijans)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
