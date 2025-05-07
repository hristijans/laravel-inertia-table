<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Columns;

final class TextColumn extends Column
{
    protected ?string $default = null;

    protected ?int $truncate = null;

    public function default(string $value): self
    {
        $this->default = $value;

        return $this;
    }

    public function truncate(int $length): self
    {
        $this->truncate = $length;

        return $this;
    }

    public function getType(): string
    {
        return 'text';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'default' => $this->default,
            'truncate' => $this->truncate,
        ]);
    }
}
