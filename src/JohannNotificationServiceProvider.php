<?php

namespace JohannDesarrollador\Notifications;

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

use JohannDesarrollador\Notifications\Http\Middleware\ShareInertiaData;


class JohannNotificationServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'jetstream');

        $this->configurePublishing();
        $this->configureRoutes();
        $this->configureCommands();

        if (config('jetstream.stack') === 'inertia')
        {
            $this->bootInertia();
        }

    }

   
    

   

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {

        if (! $this->app->runningInConsole())
        {
            return;
        }

        $this->publishes([
            __DIR__.'/../stubs/config/johann_notifications.php' => config_path('johann_notifications.php'),
        ], 'johann-notifications-config');


        $this->publishes([
            __DIR__.'/../database/migrations/2024_08_04_010224_create_notificaciones_globales_table.php' => database_path('migrations/2024_08_04_010224_create_notificaciones_globales_table.php'),
            __DIR__.'/../database/migrations/2024_08_04_010254_create_notificaciones_usuarios_table.php' => database_path('migrations/2024_08_04_010254_create_notificaciones_usuarios_table.php'),
        ], 'johann-notifications-migrations');

        $this->publishes([
            __DIR__.'/../routes/'.config('jetstream.stack').'.php' => base_path('routes/jetstream.php'),
        ], 'jetstream-routes');

        $this->publishes([
            __DIR__.'/../stubs/inertia/resources/js/Pages/Auth' => resource_path('js/Pages/Auth'),
            __DIR__.'/../stubs/inertia/resources/js/Jetstream/AuthenticationCard.vue' => resource_path('js/Jetstream/AuthenticationCard.vue'),
            __DIR__.'/../stubs/inertia/resources/js/Jetstream/AuthenticationCardLogo.vue' => resource_path('js/Jetstream/AuthenticationCardLogo.vue'),
            __DIR__.'/../stubs/inertia/resources/js/Jetstream/Checkbox.vue' => resource_path('js/Jetstream/Checkbox.vue'),
            __DIR__.'/../stubs/inertia/resources/js/Jetstream/ValidationErrors.vue' => resource_path('js/Jetstream/ValidationErrors.vue'),
        ], 'jetstream-inertia-auth-pages');

    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        Route::group([
            'namespace' => 'JohannDesarrollador\Notifications\Http\Controllers',
            'domain' => config('jetstream.domain', null),
            'prefix' => config('jetstream.prefix', config('jetstream.path')),
        ], function () {

            $this->loadRoutesFrom(__DIR__.'/../routes/.php');

        });
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {

        if (! $this->app->runningInConsole())
        {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);

    }

    /**
     * Boot any Inertia related services.
     *
     * @return void
     */
    protected function bootInertia()
    {
        
        $kernel = $this->app->make(Kernel::class);

        $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);
        $kernel->appendToMiddlewarePriority(ShareInertiaData::class);

        if ( class_exists(HandleInertiaRequests::class) )
        {
            $kernel->appendToMiddlewarePriority(HandleInertiaRequests::class);
        }
        
    }

}
