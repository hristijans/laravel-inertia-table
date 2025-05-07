<?php


use Hristijans\LaravelInertiaTable\Columns\TextColumn;
use Hristijans\LaravelInertiaTable\Columns\BadgeColumn;

it('can create a text column', function () {
    $column = TextColumn::make('name');

    expect($column)->toBeColumn(TextColumn::class);
    expect($column->getName())->toBe('name');
    expect($column->getType())->toBe('text');
});

it('can make text column sortable', function () {
    $column = TextColumn::make('name')->sortable();

    expect($column->isSortable())->toBeTrue();
});

it('can set default value for text column', function () {
    $column = TextColumn::make('name')->default('N/A');

    $array = $column->toArray();
    expect($array)->toHaveKey('default');
    expect($array['default'])->toBe('N/A');
});

it('can create a badge column with states', function () {
    $states = [
        'active' => ['color' => 'success', 'icon' => 'fas fa-check'],
        'inactive' => ['color' => 'danger', 'icon' => 'fas fa-times'],
    ];

    $column = BadgeColumn::make('status')->states($states);

    $array = $column->toArray();
    expect($array)->toHaveKey('states');
    expect($array['states'])->toBe($states);
});
