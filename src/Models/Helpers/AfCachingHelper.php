<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use East\LaravelActivityfeed\Models\ActiveModels\AfUsers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfCachingHelper extends Model
{
    use HasFactory;

    public static $caches = [
        'af_rules',
        'af_template_files'
    ];

    public static $user_caches = [
        'notifications-{{$id}}-unread',
        'notifications-{{$id}}-read',
        'notifications-{{$id}}'
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

        $users = AfUsers::all()->pluck(['id'])->toArray();

        foreach ($users as $user){
            foreach (self::$user_caches as $uc){
                Cache::delete(str_replace('{{$id}}',$user,$uc));
            }
        }
    }






}


