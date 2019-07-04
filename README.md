# laravel-api-token-generator


## Installation

### Installing with composer:

`composer require consilience/laravel-api-token-generator`

### Publishing assets:

`php artisan vendor:publish --provider=Consilience/ApiTokenGenerator/ApiTokenGeneratorServiceProvider`

### Configuration

You can change the model you wish to use to generate API Tokens for. Just make sure the model has a _String_ column called `api_token`.
You can also change the field name you wish to use for searching. eg. Email address.

    'model' => \App\User::class,
    'field' => 'name'
    
### Usage

Once you have defined the model and field you wish to use, simply run the artisan command included.

#### odel ID:
`php artisan apitoken:generate --id={$x}`

eg `php artisan apitoken:generate --id=31`

#### Model Name Search:
`php artisan apitoken:generate --value={$y}`

eg `php artisan apitoken:generate --value="Joe Bloggs"`
