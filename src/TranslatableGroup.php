<?php

namespace Amzad\FilamentTranslatableGroup;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use RuntimeException;

class TranslatableGroup extends Group
{
    use HasExtraConfigs;

    /**
     * @var array<string, string>|Closure(): array<string, string>
     */
    protected array | Closure $localeLabels;

    /**
     * @var array<string|int, string>|Closure(): array<string|int, string>
     */
    protected array | Closure $locales;

    /**
     * @var array<Closure>
     */
    protected array $modifyFieldsUsing = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->columns(2);
    }

    /**
     * @param  array<string, string>|Closure(): array<string, string>  $localesLabels
     */
    public function localesLabels(array | Closure $localesLabels): static
    {
        $this->localeLabels = $localesLabels;

        return $this;
    }

    /**
     * @param  array<string|int, string>|Closure(): array<string|int, string>  $locales
     * @return $this
     */
    public function locales(array | Closure $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getLocales(): array
    {
        $localeLabels = $this->evaluate($this->localeLabels);

        return collect($this->evaluate($this->locales))
            ->mapWithKeys(
                fn ($label, $locale) => is_int($locale)
                ? [$label => $localeLabels[$label]]
                : [$locale => $label]
            )
            ->toArray();
    }

    public function modifyFieldsUsing(Closure $closure, bool $merge = true): static
    {
        if ($merge) {
            $this->modifyFieldsUsing[] = $closure;
        } else {
            $this->modifyFieldsUsing = [$closure];
        }

        return $this;
    }

    public function handleModifyFieldsUsing(string $locale, Field $field): void
    {
        foreach ($this->modifyFieldsUsing as $closure) {
            $field->evaluate($closure, ['locale' => $locale]);
        }
    }

    /**
     * @return array<Component>
     */
    public function getDefaultChildComponents(): array
    {
        /**
         * @var array $components
         */
        $components = parent::getDefaultChildComponents();

        if (collect($components)->contains(fn ($component) => ! $component instanceof Field)) {
            throw new RuntimeException('Only instances of type ' . Field::class . ' supported');
        }

        $fields = [];

        foreach ($this->getLocales() as $locale => $label) {
            foreach ($components as $component) {
                $field = $component
                    ->getClone()
                    ->name("{$component->getName()}.$locale")
                    ->label($component->getLabel() . " ({$label})")
                    ->statePath("{$component->getStatePath(false)}.$locale");

                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getChildSchema($key = null): Schema
    {
        $schema = parent::getChildSchema($key);

        /**
         * @var Field $field
         */
        foreach ($schema->getComponents() as $field) {
            $locale = (string) str($field->getName())->afterLast('.');
            $this->handleModifyFieldsUsing($locale, $field);
        }

        return $schema;
    }
}
