<?php

namespace Codificar\LaravelSubscriptionPlan\Console\Commands;

use Illuminate\Console\Command;
use Codificar\LaravelSubscriptionPlan\Models\Signature;


class UpdateSignaturesJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'signatures:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update signatures expired to deactive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Signature::updateSignatures();
    }
}