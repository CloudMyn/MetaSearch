<?php

namespace CloudMyn\MetaSearch;

use Illuminate\Support\ServiceProvider;

class MetaSearchServiceProvider extends ServiceProvider
{

    /**
     *  Call when app everything in application is ready
     *  including third-party libraries
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . "/../migrations");

        // publish configuration and migration
        // cmd: php artisan vendor:publish --provider="CloudMyn\Logger\LoggerServiceProvider" --tag="config"
        if ($this->app->runningInConsole()) {

            // publish config file
            $this->publishes([
                __DIR__ . '/../config/metasearch.php' => config_path('metasearch.php'),
            ], 'config');

            // ...
        }
    }

    /**
     *  Call before anything setup
     */
    public function register()
    {
        // register helper functions
        // require_once __DIR__ . "/Helpers/helper_functions.php";
    }
}
