<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait HasOptions
{
    public function getOption(): Collection
    {
        return collect([
            'value' => $this->value,
            'label' => $this->getLabel(),
        ]);
    }

    public static function getOptions(): Collection
    {
        return collect(self::cases())->map(fn ($case) => $case->getOption());
    }
}
