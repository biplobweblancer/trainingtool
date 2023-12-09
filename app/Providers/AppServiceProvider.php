<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\TrainingMonitoring\AdminRepository;
use App\Repositories\TrainingMonitoring\CategoryRepository;
use App\Repositories\TrainingMonitoring\CommitteeRepository;
use App\Repositories\TrainingMonitoring\DistrictRepository;
use App\Repositories\TrainingMonitoring\DivisionRepository;
use App\Repositories\TrainingMonitoring\Interfaces\AdminRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\CategoryRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\CommitteeRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\DistrictRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\DivisionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\PermissionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\PreliminarySelectionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\ProviderRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\RoleHasPermissionRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\RoleRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\SubCategoryRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\TraineeEnrollRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\TrainerEnrollRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\UpazilaRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\UserDetailRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\UserlogRepositoryInterface;
use App\Repositories\TrainingMonitoring\Interfaces\UserRepositoryInterface;
use App\Repositories\TrainingMonitoring\PermissionRepository;
use App\Repositories\TrainingMonitoring\PreliminarySelectionRepository;
use App\Repositories\TrainingMonitoring\ProviderRepository;
use App\Repositories\TrainingMonitoring\RoleHasPermissionRepository;
use App\Repositories\TrainingMonitoring\RoleRepository;
use App\Repositories\TrainingMonitoring\SubCategoryRepository;
use App\Repositories\TrainingMonitoring\TraineeEnrollRepository;
use App\Repositories\TrainingMonitoring\TrainerEnrollRepository;
use App\Repositories\TrainingMonitoring\UpazilaRepository;
use App\Repositories\TrainingMonitoring\UserDetailRepository;
use App\Repositories\TrainingMonitoring\UserlogRepository;
use App\Repositories\TrainingMonitoring\UserRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(SubCategoryRepositoryInterface::class, SubCategoryRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(DivisionRepositoryInterface::class, DivisionRepository::class);
        $this->app->bind(DistrictRepositoryInterface::class, DistrictRepository::class);
        $this->app->bind(UpazilaRepositoryInterface::class, UpazilaRepository::class);
        $this->app->bind(UserDetailRepositoryInterface::class, UserDetailRepository::class);
        $this->app->bind(UserlogRepositoryInterface::class, UserlogRepository::class);
        $this->app->bind(ProviderRepositoryInterface::class, ProviderRepository::class);
        $this->app->bind(CommitteeRepositoryInterface::class, CommitteeRepository::class);
        $this->app->bind(PreliminarySelectionRepositoryInterface::class, PreliminarySelectionRepository::class);
        $this->app->bind(TraineeEnrollRepositoryInterface::class, TraineeEnrollRepository::class);
        $this->app->bind(TrainerEnrollRepositoryInterface::class, TrainerEnrollRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoleHasPermissionRepositoryInterface::class, RoleHasPermissionRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
