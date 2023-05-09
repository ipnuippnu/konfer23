<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //Super Admin
        Gate::before(function(User $user){
           return preg_match("/^\[('|\")\*('|\")\]$/", $user->getAttributes()['permission']); 
        });

        //Ganti Password
        Gate::define('change-password', function(User $user, User $account){
            return $user->id === $account->id || in_array('change-password', $user->permission);
        });

        //Ganti Foto Profil
        Gate::define('change-picture', function(User $user, User $account){
            return $user->id === $account->id || in_array('change-picture', $user->permission);
        });
    }
}
