<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Filters;

final class SelectFilter extends Filter
{
    protected array $options = [];

    protected bool $multiple = false;

    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function multiple(bool $multiple = true): self
    {
        $this->multiple = $multiple;

        return $this;
    }

    public function getType(): string
    {
        return 'select';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'options' => $this->options,
            'multiple' => $this->multiple,
        ]);
    }
}
