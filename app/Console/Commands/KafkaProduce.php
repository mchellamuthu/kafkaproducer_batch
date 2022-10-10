<?php

namespace App\Console\Commands;

use App\Jobs\ProduceMessageJob;
use Illuminate\Console\Command;

class KafkaProduce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:produce {limit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $limit = $this->argument('limit');
        ProduceMessageJob::dispatch($limit);
        return Command::SUCCESS;
    }
}
