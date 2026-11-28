<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repository\Menu\MenuRepository;
use App\Repository\Menu\MenuInterface;
use App\Repository\Role\RoleRepository;
use App\Repository\Role\RoleInterface;
use App\Repository\Permission\PermissionRepository;
use App\Repository\Permission\PermissionInterface;
use App\Repository\User\UserRepository;
use App\Repository\User\UserInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MenuInterface::class,MenuRepository::class);
        $this->app->bind(RoleInterface::class,RoleRepository::class);
		$this->app->bind(PermissionInterface::class,PermissionRepository::class);
		$this->app->bind(UserInterface::class,UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
