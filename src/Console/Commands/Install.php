<?php

namespace East\LaravelActivityfeed\Console\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'af:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configures ActivityFeed and publishes resources';

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
     * @return mixed
     */
    public function handle()
    {
        echo('here');
    }
}
