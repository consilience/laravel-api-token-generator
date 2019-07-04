<?php

namespace Consilience\Laravel\ApiTokenGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class GenerateApiToken extends Command
{
    protected $signature = "apitoken:generate
        {--id= : Unique ID of model to generate an API token for.}
        {--name= : Unique name of model to generate an API token for.}
    ";

    protected $description = "Generate an API token for a user";

    protected $model;
    protected $nameField;

    public function handle()
    {
        $model = config('apitokens.model', \App\User::class);
        $nameField = config('apitokens.name_field');
        $tokenField = config('apitokens.token_field');

        $id = $this->option('id');
        $name = $this->option('name');

        if ($id !== null) {
            // If given an ID, find the record using that ID.

            $record = $model::find($id);

            if (! $record) {
                $this->error(sprintf(
                    'No %s found with ID %s',
                    $model,
                    $id
                ));

                return -1;
            }
        }

        if (empty($record) && $name !== null) {
            // Otherwise search the named field using its name.

            $record = $model::where($nameField, '=', $name)->first();

            if (! $record) {
                $this->error(sprintf(
                    'No %s found with %s "%s"',
                    $model,
                    $nameField,
                    $name
                ));

                return -1;
            }
        }

        // No records found.

        if (empty($record)) {
            $this->error('No model matching this $fieldName or ID');
            return -1;
        }

        // Generate an API token.

        $token = $token = Str::random(60);

        // Store the encryoted token against the record.

        try {
            $record->{$tokenField} = hash('sha256', $token);
            $record->save();
        } catch (\Exception $e) {
            $this->error(sprintf(
                'Error saving to %s column on %s record (ID %s).',
                $tokenField,
                $model,
                (string)$record->id
            ));
        }

        $this->log(sprintf(
            "API token generated for %s (ID %s). Please note this token:\n%s",
            $model,
            (string)$record->id,
            $token
        ));
    }

    public function log(string $message)
    {
        // Function outputs to logs as well as to console.

        Log::info($message);
        $this->info($message);

        return;
    }

    public function generateApiToken()
    {
        // Generate a random 60-character string.
        $token = Str::random(60);

        // Return a hash of that string to be used as the token.
        return hash('sha256', $token);
    }
}
