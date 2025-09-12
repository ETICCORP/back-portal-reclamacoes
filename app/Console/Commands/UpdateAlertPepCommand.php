<?php

namespace App\Console\Commands;

use App\Jobs\AlertJob;
use Illuminate\Console\Command;

class UpdateAlertPepCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-alert-pep-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        AlertJob::dispatch();
    }
}
