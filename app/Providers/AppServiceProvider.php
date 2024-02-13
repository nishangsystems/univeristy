<?php

namespace App\Providers;

use App\Helpers\Helpers;
use App\Services\HeadOfSchoolService;
use Illuminate\Support\Facades\Schema;
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
        app()->bind(HeadOfSchoolService::class, function($app){
            return new HeadOfSchoolService(Helpers::instance()->getCurrentAccademicYear());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
