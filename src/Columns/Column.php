<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Columns;

use Illuminate\Support\Str;

abstract class Column
{
    protected string $name;

    protected ?string $label = null;

    protected bool $sortable = false;

    protected bool $searchable = false;

    public static function make(string $name): static
    {
        $instance = new static;
        $instance->name = $name;
        $instance->label = Str::headline($name);

        return $instance;
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
