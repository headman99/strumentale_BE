<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class runScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:scheduler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the scheduler to retrieve scraper data from Node endpoint';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return Command::SUCCESS;
    }
}
