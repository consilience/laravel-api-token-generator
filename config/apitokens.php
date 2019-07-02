<?php

return [
    // Model: Here you can define which model you wish to be generating API tokens for.
    'model' => \App\User::class,

    // Field: Here's the field on the model in which you search with the `--value=` option.
    // Example: 'field' => 'email'
    // `php artisan apitoken:generate --value="example@app.com"
    'field' => 'name'
];
