<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Facades\AfRender;
use East\LaravelActivityfeed\Facades\AfTemplating;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
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
        AfTemplating::compileTemplates();
        $this->cache = App::make(AfCachingHelper::class);

        if (!$this->cache->random) {
            $this->cache->random = rand(12, 239329329329);
        }

        parent::__construct($attributes);
    }


    public function mockVarReplacer($data,$id=null,$template='')
    {
        preg_match_all('/{{\$(.*?)}}/i', $data, $regs);
        $parts = [];
        $replace = [];

        foreach ($regs[1] as $key => $val) {
            $parts[] = $val;
        }

        foreach ($parts as $k => $part) {
            $path = explode('->', $part);
            $obj = $this->getMockObject($path[0]);
            $key = '{{$' . $part . '}}';

            if ($obj) {
                $pointer = $path[1];
                $result = $obj->{$pointer} ?? null;
                if ($result) {
                    $replace[$key] = $obj->{$pointer};
                    continue;
                }
            }

            $replace[$key] = 'TABLE NOT FOUND!';
        }

        if(!$replace){ return json_encode($data); }

        foreach ($replace as $key => $value) {
            $data = str_replace($key, $value, $data);
        }

        if($id){
            $vars['content'] = $data;

            try {
                $return = view('vendor.activity-feed.' .$id.'.'.$template .'notification', $vars)->render();
            } catch (\Throwable $exception) {
                return json_encode($this->cleanUpCss($data));
            }

            return json_encode($this->cleanUpCss($return));
        }

        return json_encode($this->cleanUpCss($data));
    }

    private function cleanUpCss($data){
        $data = str_replace('body {','.af-box-preview-notification {',$data);
        $data = str_replace('body{','.af-box-preview-notification {',$data);
        return $data;
    }

    public function getMockObject($table)
    {
        global $webhook;
        $webhook = true;

        $class = AfHelper::getTableClass($table);

        if ($class) {
            return $class::all()->first();
        }
    }

    public function renderTemplate(AfTemplate $template,$vars){

        $output = '';

        try {
            $output = view('vendor.activity-feed.' . $template->id . '.email-notification', $vars)->render();
        } catch (\Throwable $exception) {
            $template->error = $exception->getMessage();
            $template->save();
            return false;
        }

        if ($template->error) {
            $template->error = null;
            $template->save();
        }

        return $output;
    }

    public function eventObjectReplacement(AfEvent $event_obj,$vars=[]) : array {
        // if we have a originating table & key for record, we'll load the object
        if ($event_obj->dbtable AND $event_obj->dbkey) {
            $class = config('af-config.af_model_path') . '\\' . $event_obj->dbtable;
            if (class_exists($class)) {
                $obj = $class::find($event_obj->dbkey);
                if ($obj) {
                    $vars[$event_obj->dbtable] = $obj;
                }
            }
        }

        return $vars;
    }

    /**
     * @param AfNotification $notification
     * @return string
     */
    public function getMessage(AfNotification $notification) : string
    {
        $template_obj = $notification->afEvent->afRule->afTemplate ?? null;
        $master_template_id = $notification->afEvent->afRule->afTemplate->id_parent ?? null;
        $parent_obj = $notification->afEvent->afRule->afTemplate->afParent ?? null;
        $event_obj = $notification->afEvent ?? null;

        $vars = [
            'user' => $notification->recipient,
            'creator' => $notification->creator,
            'notification' => $notification
        ];

        $vars = $this->eventObjectReplacement();

        $template = $this->renderTemplate($template_obj,$vars);
        if(!$template){ return ''; }

        // if we have a master template, we'll load that also, sending the already
        // loaded template as "content"
        if ($master_template_id) {
            $vars['content'] = $template;
            if($output = $this->renderTemplate($parent_obj,$vars)){
                return $output;
            }
        }

        return $template;
    }

    public function getFeed()
    {
        if (!$this->id_user) {
            $this->id_user = auth()->user()->id;
        }
        if (!$this->id_user) {
            return '';
        }

        $feed = AfNotification::where('id_user_recipient', '=', $this->id_user)->with([
            'afRule', 'recipient', 'creator', 'afRule.afEvent', 'afRule.AfEvent.afTemplate'
        ])->get();

        $items = [];

        foreach ($feed as $item) {
            $items[] = view('vendor.activity-feed.' . $item->AfRule->AfTemplate->id . '.notification', [
                'recipient' => $item->recipient,
                'creator' => $item->creator,
                'notification' => $item
            ])->render();
        }

        return view('af_feed::af-components.feed', ['feed' => $items]);
    }

    public function setUser($id)
    {
        $this->id_user = $id;
        return new static;
    }


}
