<?php

namespace App\Providers;

use App\Models\Customer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        // add information to all admin dashboard views
        View::composer('admin.*', function ($view) {
            $view->with('new_user_count', Customer::doesntHave('contactHistory')->count());
        });
    }
}