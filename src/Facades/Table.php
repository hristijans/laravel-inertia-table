<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Hristijans\LaravelInertiaTable\Table make(string $name)
 * @method static \Hristijans\LaravelInertiaTable\Table columns(array $columns)
 * @method static \Hristijans\LaravelInertiaTable\Table actions(array $actions)
 * @method static \Hristijans\LaravelInertiaTable\Table filters(array $filters)
 * @method static \Hristijans\LaravelInertiaTable\Table query(\Illuminate\Database\Eloquent\Builder $query)
 * @method static \Hristijans\LaravelInertiaTable\Table perPage(int $perPage)
 * @method static \Hristijans\LaravelInertiaTable\Table preserveState(bool $preserve = true)
 * @method static void render()
 *
 * @see \Hristijans\LaravelInertiaTable\Table
 */
final class Table extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Hristijans\LaravelInertiaTable\Table::class;
    }
}
