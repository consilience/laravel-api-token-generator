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

    protected $description = "Generate an API token for a user";

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

        if (!$model || !$field) {
            $this->log("Missing definitions for model or field. Please check your config/apitokens.php");
            return -1;
        }

        $id = $this->option('id');
        $value = $this->option('value');

        if ($id !== null) {
            $record = $model::find($id);
        } else {
            $record = $model::where($field, $value)->first();
        }

        if (! $record) {
            $this->log("No model matching this $field or ID");
            return -1;
        }

        $token = $this->generateApiToken();

        $record->api_token = $token;
        $record->save();

        $this->log(sprintf(
            "New API token generated for $model $field '%s' (ID %d):\n%s",
            $record->{$field},
            $record->id,
            $token
        ));
    }

    public function log(string $message)
    {
        Log::info($message);
        $this->info($message);

        return;
    }

    public function generateApiToken()
    {
        $token = Str::random(60);

        return hash('sha256', $token);
    }
}
