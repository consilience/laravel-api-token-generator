# laravel-api-token-generator

Laravel supports API token authentication out of the box.
When developing, and for internal (machine-to-machine) APIs,
it is useful to be able to generate tokens for a user.
This package provides a simple Artisan command to generate a token.

The token will be hashed using the `sha256` algortithm.

This package does not provide a database migration for the `api_token` column.
That is left to your application.

## Installation

### Installing with composer:

`composer require consilience/laravel-api-token-generator`

### Publishing assets:

`php artisan vendor:publish --provider="Consilience\ApiTokenGenerator\ApiTokenGeneratorServiceProvider"`

### Configuration

You can change the model you wish to use to generate API Tokens for.
Just make sure the model has a _String_ column named by default `api_token`.
You can change the field name you wish to use for searching. eg. Email address.

    'model' => \App\User::class,
    'field' => 'name'
    
### Usage

Once you have defined the model and field you wish to use, simply run the artisan command included.

#### Model ID:

`php artisan apitoken:generate --id={$x}`

eg `php artisan apitoken:generate --id=31`

#### Model Name Search:

`php artisan apitoken:generate --value={$y}`

eg `php artisan apitoken:generate --value="Joe Bloggs"`
