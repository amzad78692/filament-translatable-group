<?php

namespace Amzad\FilamentTranslatableGroup;

use Filament\Forms\Components\Field;

trait HasExtraConfigs
{
    public function addDirectionByLocale(): static
    {
        $this->modifyFieldsUsing(function (Field $component, string $locale) {
            $dir = str($locale)->startsWith('ar') ? 'rtl' : 'ltr';
            $component->extraAttributes(['style' => "direction: $dir;"], true);
        });

        return $this;
    }
}
