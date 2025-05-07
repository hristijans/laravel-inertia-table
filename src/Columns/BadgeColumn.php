<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Columns;

final class BadgeColumn extends Column
{
    protected ?string $color = null;

    protected ?string $icon = null;

    protected array $states = [];

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function states(array $states): self
    {
        $this->states = $states;

        return $this;
    }

    public function getType(): string
    {
        return 'badge';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'color' => $this->color,
            'icon' => $this->icon,
            'states' => $this->states,
        ]);
    }
}
