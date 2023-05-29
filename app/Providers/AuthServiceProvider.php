<?php

namespace App\Providers;

use App\Models\Delegator;
use App\Models\User;
use App\Permissions\FilePermission as File;
use App\Permissions\AdminPermission as Admin;
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

        $this->filePolicies();
        $this->adminPolicies();
        
    }

    private function filePolicies()
    {
        //Surat Pengesahan
        Gate::define(File::SURAT_PENGESAHAN_READ, function(?User $user, string $url){
            if($user && in_array(File::SURAT_PENGESAHAN_READ, $user->permission))
                return true;

            if($this->getDelegator()?->getAttributes()['surat_pengesahan'] === $url)
                return true;
        });
        //Surat Tugas
        Gate::define(File::SURAT_TUGAS_READ, function(?User $user, string $url){
            if($user && in_array(File::SURAT_TUGAS_READ, $user->permission))
                return true;

            if($this->getDelegator()?->getAttributes()['surat_tugas'] === $url)
                return true;
        });
        //Bukti Transfer
        Gate::define(File::BUKTI_TRANSFER_READ, function(?User $user, string $url){
            if($user && in_array(File::BUKTI_TRANSFER_READ, $user->permission))
                return true;

            if($this->getDelegator()?->payment?->getAttributes()['bukti_transfer'] === $url)
                return true;
        });
    }

    private function getDelegator() : Delegator|null
    {
        return Delegator::find(\Sso::credential()?->id);
    }

    private function adminPolicies()
    {
        Gate::define(Admin::DASHBOARD_READ, fn(User $user) => in_array(Admin::DASHBOARD_READ, $user->permission));
    }

}
