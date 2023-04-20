<?php

namespace Celysium\Launcher;

use Celysium\Launcher\Middleware\Authenticate;
use Celysium\Seeder\Commands\GenerateSecretCommand;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LauncherServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutes();

        $this->publishConfig();
    }

    public function register()
    {
        $this->registerConfig();
        $this->registerMiddlewares();
        $this->registerCommands();
    }

    public function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
    }

    public function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/launcher.php' => config_path('launcher.php'),
        ], 'launcher-config');
    }


    public function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/launcher.php', 'launcher'
        );
    }

    /**
     * @throws BindingResolutionException
     */
    public function registerMiddlewares()
    {
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('api', Authenticate::class);
        $router->aliasMiddleware('auth:launcher', Authenticate::class);
    }

    public function registerCommands()
    {
        $this->commands([
            GenerateSecretCommand::class
        ]);
    }
}
