<?php

declare(strict_types=1);
// tests/Unit/FilterTest.php

use Hristijans\LaravelInertiaTable\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

it('can create a select filter', function () {
    $filter = SelectFilter::make('status');

    expect($filter)->toBeFilter(SelectFilter::class);
    expect($filter->toArray()['name'])->toBe('status');
    expect($filter->toArray()['type'])->toBe('select');
});

it('can set options for select filter', function () {
    $options = [
        'active' => 'Active',
        'inactive' => 'Inactive',
    ];

    $filter = SelectFilter::make('status')->options($options);

    expect($filter->toArray()['options'])->toBe($options);
});

it('can apply filter to query', function () {
    $query = $this->mock(Builder::class);
    $query->expects('where')->with('status', 'active')->andReturnSelf();

    $filter = SelectFilter::make('status');
    $result = $filter->apply($query, 'active');

    expect($result)->toBe($query);
});
