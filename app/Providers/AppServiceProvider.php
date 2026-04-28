<?php

namespace App\Providers;

use App\Models\SiteContent;
use App\Models\SiteSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('date', date('Y'));
        View::share('siteContentMap', []);
        View::share('currentTheme', 'classic');

        View::composer('user*', function ($view) {
            $view->with('balance', 12345);
        });

        View::composer(['includes.header', 'home.index'], function ($view) {
            if (!Schema::hasTable('site_contents')) {
                return;
            }

            $map = SiteContent::query()
                ->pluck('content', 'key')
                ->toArray();

            $view->with('siteContentMap', $map);
        });

        View::composer('*', function ($view) {
            if (!Schema::hasTable('site_settings')) {
                return;
            }

            $currentTheme = SiteSetting::query()
                ->where('key', 'theme')
                ->value('value') ?? 'classic';

            $view->with('currentTheme', $currentTheme);
        });

        // Model::preventSilentlyDiscardingAttributes(app()->isLocal());

        Paginator::useBootstrapFive();
    }
}
