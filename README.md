# laravel-api-token-generator

Laravel supports API token authentication out of the box.
When developing, and for internal (machine-to-machine) APIs,
it is useful to be able to generate tokens for a user.
This package provides a simple Artisan command to generate a token.

The token will be hashed using the `sha256` algorithm.

This package does not provide a database migration for the `api_token` column.
That is left to your application.

## Installation

### Installing with composer:

`composer require consilience/laravel-api-token-generator`

### Lumen

For Laravel, the service provider and configuration file are registered automatically.
With Lumen, additional entried are needed in `bootstrap/app.php`.

The service provider is registered:

    $app->register(Consilience\Laravel\ApiTokenGenerator\Providers\ServiceProvider::class);

If the configuration file is published, add:

    $app->configure('apitokens');

then copy `apitokens.php`:

    cp vender/consilience/laravel-api-token-generator/apitokens.php config/apitokens.php

### Publishing assets:

`php artisan vendor:publish --provider="Consilience\ApiTokenGenerator\ApiTokenGeneratorServiceProvider"`

### Configuration

You can change the model that will hold the API tokens.
By default this will be `App\User`, but yu may want `App\Models\User` for example.

    'model' => App\Models\User::class,

The `name_field` is an alternative column to `id` that can be used to uniquely identify a model instance:

    'name_field' => 'name'

The token column will be `api_token` by default, but can be changed:

    'token_field' => 'my_api_token_column',

Note: this package does not add the API token column to your users table.
That is a step for other packages or your application deployment.

### Usage

Generate a new token or replace the existing token for a user:

    php artisan apitoken:generate --id=123 --generate
    php artisan apitoken:generate --id=5fd40c23-fcda-4bdc-a07c-f2bfeb56bb03 --generate

The `id` is normally an integer, but some this should also work if the `id` is a string such as *UUID*.

A generated token will only be displayed once.
It is encrypted for saving against the model, so cannot be recovered if not recorded immediately.

Where users are uniquely identified by another column,
then that column can be used to identify the model instance to update with a new token:

    php artisan apitoken:generate --name=bloggs@example.com --generate

Rather than generating a random token, you can set your own explicit tokanes.
Use the `--token=` option to do this instead of the `--generate` option.

    php artisan apitoken:generate --id=123 --token=d8a928b2043db77e340b523547bf16cb4aa483f0645fe0a290ed1f20aab76257

If using for automated deployment, you may want to use the `--no-ansi` option to remove control characters.
