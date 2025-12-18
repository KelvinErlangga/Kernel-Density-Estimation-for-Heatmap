<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CurriculumVitaeUser;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        View::composer('pelamar.dashboard.partials.modal_lamaran', function ($view) {
        $user = Auth::user();

        $cvs = collect();
        if ($user) {
            $cvs = CurriculumVitaeUser::with('templateCurriculumVitae')
                ->where('user_id', $user->id)
                ->get();
        }

        $view->with('cvs', $cvs);
    });
    }
}
