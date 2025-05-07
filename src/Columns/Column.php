<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Columns;

use Illuminate\Support\Str;

abstract class Column
{
    protected ?string $label = null;

    protected bool $sortable = false;

    protected bool $searchable = false;

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

    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSortable(): bool
    {
        return $this->sortable;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->getType(),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
        ];
    }
}
