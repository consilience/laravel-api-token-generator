<?php

return [
    // Changes behaviour of console command.
    // this tells the command which model to create the token against.

    'model' => \App\User::class,

    // A field that uniquely identifies the model instance, alternative to
    // the model ID.
    // `php artisan apitoken:generate --value="example@app.com"

    'name_field' => 'email_address',

    'token_field' => 'api_token',
];
