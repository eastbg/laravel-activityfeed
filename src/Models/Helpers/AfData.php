<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfData extends Model
{
    use HasFactory;

    public static $caches = [

    ];

    public $random;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function flushCaches()
    {
        foreach (self::$caches as $cache) {
            Cache::delete($cache);
        }
    }

    public function getTables()
    {
        $tables = DB::select('SHOW TABLES');
        $output = [];
        $include = config('activity-feed.af_tables') ?? [];
        $exclude = config('activity-feed.af_exclude_tables') ?? [];

        foreach ($tables as $key => $table) {

            foreach ($table as $t) {
                if (!stristr($t, 'af_')) {

                    if (!empty($include) and in_array($t, $include)) {
                        $output[$t] = $t;
                    } elseif (!empty($exclude) and !in_array($t, $exclude)) {
                        $output[$t] = $t;
                    } elseif (empty($exclude) and empty($include)) {
                        $output[$t] = $t;
                    }

                }
            }

        }

        return $output;
    }

    public function getColumns($table)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        $output = [];

        foreach ($columns as $col) {
            $output[] = $col;
        }

        return $output;
    }


}


