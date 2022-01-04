<?php

namespace App\Providers;

use App\Permissions;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();

        // Gate::before(function ($user) {
        //     return $user->hasPermissionTo('list user','web') ? true : null;
        // });

        // Gate::before(function ($user, $ability) {
        //     if ($user->hasRole('Admin')) {
        //         return true;
        //     }
        // });

        // Gate::after(function ($user) {
        //     $permission = Permissions::all();
        //     foreach($permission as $per){
        //         return $user->hasPermissionTo($per->name,'web') ? true : null;
        //     }
        // });
    }
}
