<?php

namespace App\Providers;

use App\Helpers\Helpers;
use App\Services\HeadOfSchoolService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use \App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

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
        app()->bindif(Controller::class, function($app){
            return new Controller(intval(Helpers::instance()->getCurrentAccademicYear()));
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
        View::share('helpers', \App\Helpers\Helpers::instance());
        View::share('is_school_director', \App\Models\HeadOfSchool::where('user_id', auth()->id())->where('status', 1)->count() > 0);
    }
}
