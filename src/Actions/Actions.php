<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Actions;

use Closure;
use Illuminate\Support\Str;

abstract class Action
{
    protected string $name;

    protected ?string $label = null;

    protected ?string $icon = null;

    protected ?string $url = null;

    protected ?Closure $action = null;

    protected bool $requiresConfirmation = false;

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

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function action(Closure $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function requiresConfirmation(bool $confirmation = true): static
    {
        $this->requiresConfirmation = $confirmation;

        return $this;
    }

    abstract public function getType(): string;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->getType(),
            'icon' => $this->icon,
            'url' => $this->url,
            'requiresConfirmation' => $this->requiresConfirmation,
        ];
    }
}
