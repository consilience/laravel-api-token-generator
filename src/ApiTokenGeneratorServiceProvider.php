<?php


namespace Consilience\ApiTokenGenerator;

use Consilience\ApiTokenGenerator\Commands\GenerateApiToken;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ApiTokenGeneratorServiceProvider extends BaseServiceProvider
{
    // Define an array of command classes to register.
    protected $commands = [
        GenerateApiToken::class
    ];

    public function boot()
    {
        // Publish config file to application config directory.
        $this->publishes([
            __DIR__ . '/../config/apitokens.php' => config_path('apitokens.php'),
        ], 'config');
    }

    public function register()
    {
        // Register any commands.
        $this->commands($this->commands);
    }
}
