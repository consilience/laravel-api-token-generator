<?php

return [
    // Changes behaviour of console command.
    // this tells the command which model to create the token against.
    'model' => \App\User::class,

    // Field: Here's the field on the model in which you search with the `--value=` option.
    // Example: 'field' => 'email'
    // `php artisan apitoken:generate --value="example@app.com"
    'field' => 'name'
];
