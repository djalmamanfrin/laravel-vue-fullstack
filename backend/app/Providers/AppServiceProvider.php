<?php

namespace App\Providers;

use App\Models\Board;
use App\Models\Collection;
use App\Models\Image;
use App\Observers\BoardObserver;
use App\Observers\CollectionObserver;
use App\Observers\ImageObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Board::observe(BoardObserver::class);
        Collection::observe(CollectionObserver::class);
        Image::observe(ImageObserver::class);
    }
}
