<?php

namespace PragmaRX\TestsWatcher\Package;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use PragmaRX\TestsWatcher\Package\Console\Commands\ClearCommand;
use PragmaRX\TestsWatcher\Package\Console\Commands\TestCommand;
use PragmaRX\TestsWatcher\Package\Console\Commands\WatchCommand;
use PragmaRX\TestsWatcher\Package\Events\TestsFailed;
use PragmaRX\TestsWatcher\Package\Events\UserNotifiedOfFailure;
use PragmaRX\TestsWatcher\Package\Listeners\MarkAsNotified;
use PragmaRX\TestsWatcher\Package\Listeners\Notify;
use PragmaRX\TestsWatcher\Package\Support\Notifier;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot Service Provider.
     */
    public function boot()
    {
        $this->publishConfiguration();

        $this->loadMigrations();

        $this->loadRoutes();

        $this->loadViews();
    }

    /**
     * Configure migrations path.
     */
    private function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Configure views path.
     */
    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pragmarx/ci');
    }

    /**
     * Configure config path.
     */
    private function publishConfiguration()
    {
        $this->publishes([
            __DIR__.'/../config/ci.php' => config_path('ci.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!defined('CI_PATH')) {
            define('CI_PATH', realpath(__DIR__.'/../../'));
        }

        $this->registerResourceWatcher();

        $this->registerService();

        $this->registerWatcher();

        $this->registerTester();

        $this->registerConfig();

        $this->registerWatchCommand();

        $this->registerTestCommand();

        $this->registerClearCommand();

        $this->registerNotifier();

        $this->registerEventListeners();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['ci'];
    }

    /**
     * Register the clear command.
     */
    private function registerClearCommand()
    {
        $this->app->singleton('ci.clear.command', function ($app) {
            return new ClearCommand();
        });

        $this->commands('ci.clear.command');
    }

    /**
     * Register event listeners.
     */
    private function registerEventListeners()
    {
        Event::listen(TestsFailed::class, Notify::class);

        Event::listen(UserNotifiedOfFailure::class, MarkAsNotified::class);
    }

    /**
     * Register the watch command.
     */
    private function registerNotifier()
    {
        $this->app->singleton('ci.notifier', function () {
            return new Notifier();
        });
    }

    /**
     * Register the watch command.
     */
    private function registerWatchCommand()
    {
        $this->app->singleton('ci.watch.command', function ($app) {
            return new WatchCommand();
        });

        $this->commands('ci.watch.command');
    }

    /**
     * Register the test command.
     */
    private function registerTestCommand()
    {
        $this->app->singleton('ci.test.command', function () {
            return new TestCommand();
        });

        $this->commands('ci.test.command');
    }

    /**
     * Register service service.
     */
    private function registerService()
    {
        $this->app->singleton('ci', function ($app) {
            return app('PragmaRX\TestsWatcher\Package\Service');
        });
    }

    /**
     * Register service watcher.
     */
    private function registerWatcher()
    {
        $this->app->singleton('ci.watcher', function ($app) {
            return app('PragmaRX\TestsWatcher\Package\Services\Watcher');
        });
    }

    /**
     * Register service tester.
     */
    private function registerTester()
    {
        $this->app->singleton('ci.tester', function ($app) {
            return app('PragmaRX\TestsWatcher\Package\Services\Tester');
        });
    }

    /**
     * Register service tester.
     */
    private function registerConfig()
    {
        $this->app->singleton('ci.config', function ($app) {
            return app('PragmaRX\TestsWatcher\Package\Services\Config');
        });
    }

    /**
     * Register the resource watcher.
     */
    private function registerResourceWatcher()
    {
        $this->app->register('JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider');
    }

    /**
     * Register all routes.
     */
    private function loadRoutes()
    {
        Route::group([
            'prefix'     => config('ci.url_prefix'),
            'namespace'  => 'PragmaRX\TestsWatcher\Package\Http\Controllers',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Get the root directory for this ServiceProvider.
     *
     * @return string
     */
    public function getRootDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..';
    }
}
