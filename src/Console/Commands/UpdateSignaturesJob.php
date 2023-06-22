<?php

namespace Codificar\LaravelSubscriptionPlan\Console\Commands;

use Illuminate\Console\Command;
use Codificar\LaravelSubscriptionPlan\Models\Signature;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class UpdateSignaturesJob extends Command implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    
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
    protected $description = 'Update signatures expired to deactivate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Signature::updateSignatures();
        
    }

    /**
	 * Add a Tag to Laravel Horizon
	 */
	public function tags()
	{
		return ['LibSignature.updateExpired'];
	}
}