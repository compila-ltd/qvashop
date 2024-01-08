<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //Ignore default migration from here
    Sanctum::ignoreMigrations();
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    //Schema Default String Length
    Schema::defaultStringLength(191);

    // User Bootstrap as paginator
    Paginator::useBootstrap();

    if ($this->app->environment('production')) {
      URL::forceScheme('https');
    }
  }
}
