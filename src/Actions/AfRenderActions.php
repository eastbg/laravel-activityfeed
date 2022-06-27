<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Facades\AfTemplating;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;

class AfRenderActions extends Model
{
    use HasFactory;

    private $triggers;
    private $schema;
    public $random;
    public $id_user;
    public $cache;

    public function __construct(array $attributes = [])
    {
        $this->cache = App::make(AfCachingHelper::class);

        if(!$this->cache->random){
            $this->cache->random = rand(12,239329329329);
        }

        parent::__construct($attributes);
    }



    public function mockVarReplacer($data){

        preg_match_all('/{{\$(.*?)}}/i', $data, $regs);
        $parts = [];

        foreach($regs as $key=>$val){
            if(!stristr($val[0],'{{')){
                $parts[] = $val[0];
            }
        }

        foreach($parts as $k=>$part){
            $path = explode('->',$part);
            $obj = $this->getMockObject($path[0]);
            $key = '{{$'.$part.'}}';

            if($obj){
                $pointer = $path[1];
                $result = $obj->{$pointer} ?? null;
                if($result){
                    $replace[$key] = $obj->{$pointer};
                    continue;
                }
            }

            $replace[$key] = 'TABLE NOT FOUND!';
        }

        foreach($replace as $key=>$value){
            $data = str_replace($key,$value,$data);
        }

        return json_encode($data);
    }

    public function getMockObject($table){
        global $webhook;
        $webhook = true;

        $class = AfHelper::getTableClass($table);

        if($class){
            return $class::all()->first();
        }
    }


    /**
     * @param AfNotification $notification
     * @return string
     */
    public function getMessage(AfNotification $notification){
        AfTemplating::compileTemplates();
        $template_id = $notification->afEvent->afRule->afTemplate->id ?? null;
        $master_template_id = $notification->afEvent->afRule->afTemplate->id_parent ?? null;
        $parent_obj = $notification->afEvent->afRule->afTemplate->afParent ?? null;

        $vars = [
            'user' => $notification->recipient,
            'creator' => $notification->creator,
            'notification' => $notification
        ];

        // if we have a originating table & key for record, we'll load the object
        if($notification->AfEvent->dbtable AND $notification->AfEvent->dbkey){
            $class = config('af-config.af_model_path') . '\\' . $notification->AfEvent->dbtable;
            if(class_exists($class)){
                $obj = $class::find($notification->AfEvent->dbkey);
                if($obj){
                    $vars[$notification->AfEvent->dbtable] = $obj;
                }
            }
        }

        try {
            $template = view('vendor.activity-feed.'.$template_id.'.email-notification',$vars)->render();
        } catch (\Throwable $exception){
            $notification->afEvent->afRule->afTemplate->error = $exception->getMessage();
            $notification->afEvent->afRule->afTemplate->save();
            return false;
        }

        if($notification->afEvent->afRule->afTemplate->error){
            $notification->afEvent->afRule->afTemplate->error = null;
            $notification->afEvent->afRule->afTemplate->save();
        }

        // if we have a master template, we'll load that also, sending the already
        // loaded template as "content"
        if($master_template_id){
            $vars['content'] = $template;

            try {
                $return = view('vendor.activity-feed.'.$master_template_id.'.email-notification',$vars)->render();
            } catch (\Throwable $exception){
                $notification->afEvent->afRule->afTemplate->afParent->error = $exception->getMessage();
                $notification->afEvent->afRule->afTemplate->afParent->save();
                return false;
            }

            if(isset($parent_obj->error) AND $parent_obj->error){
                $parent_obj->error = null;
                $parent_obj->save();
            }

            return $return;
        }

        return $template;

    }

    public function getFeed() {
        if(!$this->id_user) { $this->id_user = auth()->user()->id; } if(!$this->id_user){ return ''; }

        AfTemplating::compileTemplates();

        $feed = AfNotification::where('id_user_recipient','=',$this->id_user)->with([
            'afRule','recipient','creator','afRule.afEvent','afRule.AfEvent.afTemplate'
        ])->get();

        $items = [];

        foreach($feed as $item){
            $items[] = view('vendor.activity-feed.'.$item->AfRule->AfTemplate->id.'.notification',[
                'recipient' => $item->recipient,
                'creator' => $item->creator,
                'notification' => $item
            ])->render();
        }

        return view('af_feed::af-components.feed',['feed' => $items]);
    }

    public function setUser($id){
        $this->id_user = $id;
        return new static;
    }



}
