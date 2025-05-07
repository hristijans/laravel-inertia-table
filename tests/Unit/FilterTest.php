<?php

declare(strict_types=1);
// tests/Unit/FilterTest.php

use Hristijans\LaravelInertiaTable\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

it('can create a select filter', function (): void {
    $selectFilter = SelectFilter::make('status');

    expect($selectFilter)->toBeFilter(SelectFilter::class);
    expect($selectFilter->toArray()['name'])->toBe('status');
    expect($selectFilter->toArray()['type'])->toBe('select');
});

it('can set options for select filter', function (): void {
    $options = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    $selectFilter = SelectFilter::make('status')->options($options);

    expect($selectFilter->toArray()['options'])->toBe($options);
});

it('can apply filter to query', function (): void {
    $query = $this->mock(Builder::class);
    $query->expects('where')->with('status', 'active')->andReturnSelf();

    $selectFilter = SelectFilter::make('status');
    $builder = $selectFilter->apply($query, 'active');

    expect($builder)->toBe($query);
});
