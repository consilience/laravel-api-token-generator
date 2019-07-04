<?php

namespace Consilience\Laravel\ApiTokenGenerator\Providers;

use Consilience\Laravel\ApiTokenGenerator\Commands\GenerateApiToken;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    // Define an array of command classes to register.
    protected $commands = [
        GenerateApiToken::class
    ];

    public function boot()
    {
        // Publish config file to application config directory.

        $this->publishes([
            __DIR__ . '/../../config/apitokens.php' => $this->configPath('apitokens.php'),
        ], 'config');
    }

    public function register()
    {
        // Register any commands.

        $this->commands($this->commands);

        // Default configuration.

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/apitokens.php', 'apitokens'
        );
    }

    /**
     * Fallback for lack of config_path() on Lumen.
     */
    function configPath($path = '')
    {
        if (function_exists('config_path')) {
            return config_path($path);
        } else {
            return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
        }
    }
}
