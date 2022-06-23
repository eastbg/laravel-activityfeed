<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfCachingHelper extends Model
{
    use HasFactory;

    public static $caches = [

    ];

    public $random;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function flushCaches(){
        foreach(self::$caches as $cache){
            Cache::delete($cache);
        }
    }






}


