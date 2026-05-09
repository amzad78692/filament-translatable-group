<?php

namespace Amzad\FilamentTranslatableGroup;

use Closure;
use Filament\Forms\Components\Field;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTranslatableGroupServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translatable-group';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->askToStarRepoOnGitHub('amzad/filament-translatable-group');
            });
    }

    public function packageBooted(): void
    {
        Field::macro('translatableGroup', function (
            Closure | array | null $locales = null,
            ?Closure $modifyFieldsUsing = null
        ) {

            /** @phpstan-ignore-next-line  */
            return TranslatableGroup::make()
                ->when(! is_null($locales), fn (TranslatableGroup $group) => $group->locales($locales))
                ->when(! is_null($modifyFieldsUsing), fn (TranslatableGroup $group) => $group->modifyFieldsUsing($modifyFieldsUsing))
                ->schema([$this]);
        });
    }
}
