<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Hristijans\LaravelInertiaTable\Tests\TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeColumn', function ($columnClass = null) {
    if ($columnClass) {
        return $this->toBeInstanceOf($columnClass);
    }

    return $this->toBeInstanceOf(\Hristijans\LaravelInertiaTable\Columns\Column::class);
});

expect()->extend('toBeAction', function ($actionClass = null) {
    if ($actionClass) {
        return $this->toBeInstanceOf($actionClass);
    }

    return $this->toBeInstanceOf(\Hristijans\LaravelInertiaTable\Actions\Action::class);
});

expect()->extend('toBeFilter', function ($filterClass = null) {
    if ($filterClass) {
        return $this->toBeInstanceOf($filterClass);
    }

    return $this->toBeInstanceOf(\Hristijans\LaravelInertiaTable\Filters\Filter::class);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getTable(): \Hristijans\LaravelInertiaTable\Table
{
    return \Hristijans\LaravelInertiaTable\Table::make('test');
}
