<?php

namespace East\LaravelActivityfeed\Console\Commands;

use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use East\LaravelActivityfeed\Models\Helpers\AfGenerator;
use Illuminate\Console\Command;

class Cache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'af_cache:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates rules based on database structure';

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
        $obj = new AfCachingHelper();
        $obj->flushCaches();
    }
}
