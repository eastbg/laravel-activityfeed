<?php

namespace East\LaravelActivityfeed\Console\Commands;

use East\LaravelActivityfeed\Models\Helpers\AfGenerator;
use Illuminate\Console\Command;

class Generator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'af:discover';

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
        $obj = new AfGenerator();
        $obj->writeRules();
    }
}
