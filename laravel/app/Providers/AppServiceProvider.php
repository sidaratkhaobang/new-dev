<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use App\Classes\PermissionManager;
use App\Classes\StepApproveManagement;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('permission.manager', function () {
            return new PermissionManager();
        });
        $this->app->bind('step-approve.management', function () {
            return new StepApproveManagement();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Blueprint::macro('userFields', function () {
            $this->timestamps();
            $this->softDeletes();
            $this->uuid('created_by')->nullable();
            $this->uuid('updated_by')->nullable();
            $this->uuid('deleted_by')->nullable();

            $this->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $this->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $this->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
        });

        Blueprint::macro('userFieldsApproved', function () {
            $this->uuid('approved_by')->nullable();
            $this->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $this->timestamp('approved_at')->nullable();
        });

        Blueprint::macro('status', function () {
            $this->tinyInteger('status')->default(STATUS_ACTIVE);
        });

        Blueprint::macro('refId', function () {
            $this->bigInteger('ref_id')->nullable();
        });

        if (!App::environment('local')) {
            URL::forceScheme('https');
        }
    }
}
