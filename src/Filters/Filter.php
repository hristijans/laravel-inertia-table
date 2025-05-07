<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class Filter
{
    protected ?string $label = null;

    protected $default = null;

    protected ?Closure $query = null;

    /**
     * Create a new column instance.
     */
    public function __construct(protected string $name)
    {
        $this->label = Str::headline($this->name);
    }

    /**
     * Create a new column instance.
     */
    public static function make(string $name): self
    {
        return app(static::class, ['name' => $name]);
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function default($value): static
    {
        $this->default = $value;

        return $this;
    }

    public function query(Closure $callback): static
    {
        $this->query = $callback;

        return $this;
    }

    public function apply(Builder $builder, $value): Builder
    {
        if ($this->query instanceof \Closure) {
            return call_user_func($this->query, $builder, $value);
        }

        return $builder->where($this->name, $value);
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->getType(),
            'default' => $this->default,
        ];
    }
}
