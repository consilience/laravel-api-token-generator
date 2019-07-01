<?php


namespace Consilience\ApiTokenGenerator\Commands;

use App\User;
use Illuminate\Console\Command;
use Str;
use Log;

class GenerateApiToken extends Command
{
    protected $signature = "apitoken:generate {string-or-id}";
    protected $description = "Generate an API token for a user";

    protected $model;
    protected $field;

    public function __construct()
    {
        $this->model = config('apitokens.model') ?? User::class;
        $this->field = config('apitokens.field') ?? 'name';

        parent::__construct();
    }

    public function handle()
    {
        $model = $this->model;
        $field = $this->field;

        $stringOrId = $this->argument('string-or-id');

        if (empty($stringOrId)) {
            $this->log("$field or ID must be supplied");
            return -1;
        }

        if (is_numeric($stringOrId)) {
            $record = $model::find($stringOrId);
        } else {
            $record = $model::where($field, $stringOrId)->first();
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
