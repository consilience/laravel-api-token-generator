<?php


namespace Consilience\ApiTokenGenerator\Commands;

use Illuminate\Console\Command;
use Str;
use Log;

class GenerateApiToken extends Command
{
    protected $signature = "apitoken:generate
    {--id= : Unique ID of model to generate an API token for.}
    {--value= : A field(defined in configuration) value to search for.}
    ";

    protected $description = "Generate an API token for a given model";

    protected $model;
    protected $field;

    public function __construct()
    {
        $this->model = config('apitokens.model');
        $this->field = config('apitokens.field');

        parent::__construct();
    }

    public function handle()
    {
        $model = $this->model;
        $field = $this->field;

        // If neither is set by config or otherwise, we need to error out here.
        if (!$model || !$field) {
            $this->log("Missing definitions for model or field. Please check your config/apitokens.php");
            return -1;
        }

        // Grab $id and $value from input.
        $id = $this->option('id');
        $value = $this->option('value');

        if ($id !== null) {
            // If given an ID, find the record via that ID.
            $record = $model::find($id);
        } else {
            // Otherwise search the named field using the value given.
            $record = $model::where($field, $value)->first();
        }

        // If we can't find any records, quit.
        if (! $record) {
            $this->log("No model matching this $field or ID");
            return -1;
        }

        // Generate an API token.
        $token = $this->generateApiToken();

        // Store it against the record.
        try {
            $record->api_token = $token;
            $record->save();
        } catch (\Exception $e) {
            $this->log("Error saving to api_token column on $model record.");
        }

        $this->log("New API token generated for $model $field '$record->{$field}' (ID $record->id):\n$token",);
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
