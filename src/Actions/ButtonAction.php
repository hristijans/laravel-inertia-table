<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Actions;

final class ButtonAction extends Action
{
    protected ?string $color = 'primary';

    protected string $size = 'md';

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function size(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getType(): string
    {
        return 'button';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'color' => $this->color,
            'size' => $this->size,
        ]);
    }
}
