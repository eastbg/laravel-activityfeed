<?php

namespace East\LaravelActivityfeed\Console\Commands;

use East\LaravelActivityfeed\Actions\AfPollAction;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;

class AfPoll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'afpoll:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This should be a schedule action. Prepares notifications & sends to channels.';

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
        $obj = new AfPollAction();
        $obj->runPoll();
    }
}
