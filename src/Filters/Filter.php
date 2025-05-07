<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class Filter
{
    protected string $name;

    protected ?string $label = null;

    protected $default = null;

    protected ?Closure $query = null;

    public static function make(string $name): static
    {
        $instance = new static();
        $instance->name = $name;
        $instance->label = Str::headline($name);

        return $instance;
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

    public function apply(Builder $query, $value): Builder
    {
        if ($this->query) {
            return call_user_func($this->query, $query, $value);
        }

        return $query->where($this->name, $value);
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
