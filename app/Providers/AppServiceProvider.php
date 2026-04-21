<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Glhd\Bits\Support\Livewire\SnowflakeSynth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Date::use(
            CarbonImmutable::class
        );

        DB::prohibitDestructiveCommands(
            $this->app->isProduction()
        );

        Model::shouldBeStrict();

        Livewire::propertySynthesizer(SnowflakeSynth::class);

        Password::defaults(
            fn () => $this->app->isProduction()
                ? Password::min(12)->uncompromised()
                : Password::min(8)
        );

        URL::forceHttps();
    }
}
