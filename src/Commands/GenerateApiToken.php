<?php

namespace Consilience\Laravel\ApiTokenGenerator\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class GenerateApiToken extends Command
{
    protected $signature = "apitoken:generate
        {--id= : ID of model instance}
        {--name= : Name of model instance}
        {--token= : Set a specific token}
        {--generate : Generate a random token}
        {--check : Check if a model instance has an API token set}
    ";

    protected $description = "Generate an API token for a user or other eloquent model";

    protected $model;
    protected $nameField;

    public function handle()
    {
        $model = config('apitokens.model', \App\User::class);
        $nameField = config('apitokens.name_field');
        $tokenField = config('apitokens.token_field');

        $id = $this->option('id');
        $name = $this->option('name');
        $token = $this->option('token');
        $generate = $this->option('generate');
        $check = $this->option('check');

        if (empty($id) && empty($name)) {
                $this->error(sprintf(
                    'Either the --name or the --id option must be supplied.',
                    $model,
                    $id
                ));

                $this->info(sprintf(
                    'Token to be created on %s::%s identified by "id" or "%s"',
                    $model,
                    $tokenField,
                    $nameField
                ));

                return 1;
        }

        if (! empty($id)) {
            // If given an ID, find the record using that ID.

            $record = $model::find($id);

            if ($record === null) {
                $this->error(sprintf(
                    'No %s found with ID %s',
                    $model,
                    $id
                ));

                return 1;
            }
        }

        if (empty($record) && $name !== null) {
            // Otherwise search the model using its name.

            $record = $model::where($nameField, '=', $name)->first();

            if ($record === null) {
                $this->error(sprintf(
                    'No %s found with %s "%s"',
                    $model,
                    $nameField,
                    $name
                ));

                return 1;
            }
        }

        // We will have found a record if we get to here.

        if ($check) {
            if (! empty($record->{$tokenField})) {
                $this->info(sprintf(
                    '%s::%s has an API token set',
                    $model,
                    $record->id
                ));
            } else {
                $this->info(sprintf(
                    '%s::%s has no API token set',
                    $model,
                    $record->id
                ));
            }
        }

        if (empty($generate) && empty($token)) {
            $this->info('No explicit token supplied (--token=) and no token to be generated (--generate)');
            exit;
        }

        // Generate an API token if requested.

        if (empty($token) && $generate) {
            $this->info(sprintf(
                "API token generated for %s::id %s; this token will be shown once only:",
                $model,
                (string) $record->id
            ));

            $newToken = Str::random(60);
        } else {
            $this->info(sprintf(
                "API token to be set for %s (ID %s):",
                $model,
                (string) $record->id
            ));

            $newToken = $token;
        }

        if ($newToken) {
            // Store the encrypted token against the record.

            try {
                $record->{$tokenField} = hash('sha256', $newToken);
                $record->save();
            } catch (Exception $e) {
                $this->error(sprintf(
                    'Error setting %s::%s for ID %s; maybe a duplicate token',
                    $model,
                    $tokenField,
                    (string) $record->id
                ));

                $Log->error($e);
            }

            $this->info(
                $newToken
            );
        }
    }
}
