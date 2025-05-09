<?php

declare(strict_types=1);

namespace Hristijans\LaravelInertiaTable\Columns;

use Carbon\Carbon;

final class DateColumn extends Column
{
    protected ?string $default = null;

    protected string $format = 'd/m/Y';

    public function default(string $value): self
    {
        $this->default = Carbon::parse($value)->format($this->format);

        return $this;
    }

    public function format(string $format): self
    {
        $this->format = $format;

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
            'format' => $this->format
        ]);
    }
}
