<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfNotifyHelper extends Model
{
    use HasFactory;


    public function addNotification(string $rule_slug,int $id_user){

    }


}


