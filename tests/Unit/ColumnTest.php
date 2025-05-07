<?php

declare(strict_types=1);

use Hristijans\LaravelInertiaTable\Columns\BadgeColumn;
use Hristijans\LaravelInertiaTable\Columns\TextColumn;

it('can create a text column', function (): void {
    $textColumn = TextColumn::make('name');

    expect($textColumn)->toBeColumn(TextColumn::class);
    expect($textColumn->getName())->toBe('name');
    expect($textColumn->getType())->toBe('text');
});

it('can make text column sortable', function (): void {
    $textColumn = TextColumn::make('name')->sortable();

    expect($textColumn->isSortable())->toBeTrue();
});

it('can set default value for text column', function (): void {
    $textColumn = TextColumn::make('name')->default('N/A');

    $array = $textColumn->toArray();
    expect($array)->toHaveKey('default');
    expect($array['default'])->toBe('N/A');
});

it('can create a badge column with states', function (): void {
    $states = [
        'active' => ['color' => 'success', 'icon' => 'fas fa-check'],
        'inactive' => ['color' => 'danger', 'icon' => 'fas fa-times'],
    ];

    $badgeColumn = BadgeColumn::make('status')->states($states);

    $array = $badgeColumn->toArray();
    expect($array)->toHaveKey('states');
    expect($array['states'])->toBe($states);
});
