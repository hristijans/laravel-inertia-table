<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Columns;

final class DateColumn extends Column
{
    protected ?string $default = null;

    protected string $format = 'd/m/Y';

    protected ?string $formatted = null;

    protected ?string $timezone = null;

    public function default(string $value): self
    {
        $this->default = $value;

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getType(): string
    {
        return 'date';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'default' => $this->default,
            'format' => $this->format,
        ]);
    }
}
