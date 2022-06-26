<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfTemplateHelper extends Model
{
    use HasFactory;

    public static $caches = [
        'af_rules',
        'af_template_files'
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

    public function compileTemplates(){
        //if(Cache::get('af_template_files')){ return true; }
        $path = resource_path('views/vendor/activity-feed');

        if(!is_dir($path)){
            @mkdir($path,0777,true);
        }

        $templates = AfTemplate::where('enabled','=',1)->get();

        foreach($templates as $template){
            $template_path = $path.'/'.$template->id;
            if(!is_dir($template)){
                @mkdir($template_path);
            }

            file_put_contents($template_path.'/email-notification.blade.php',$template->email_template);
            file_put_contents($template_path.'/notification.blade.php',$template->notification_template);
            file_put_contents($template_path.'/admin-notification.blade.php',$template->admin_template);
        }

        Cache::set('af_template_files',true);
    }




}


