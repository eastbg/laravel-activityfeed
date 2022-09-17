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

    /**
     * Does replacement for all columns in the template, including those behind relationship.
     *
     * @param $baseobj
     * @param $data string
     * @param $vars array
     * @return string
     */
    public function varReplacer($baseobj, $data, $vars = []) : array
    {
        $parts = $this->extractReplacementParts($data);

        foreach ($parts as $k => $part) {
            $parts = explode('->', $part);
            if(isset($parts[0])){
                $subpart = $parts[0];
                if(isset($vars[$subpart])) {
                    $vars = $this->getObjectColumn($part,$vars,$vars[$subpart]);
                    continue;
                }
            }

            $vars = $this->getObjectColumn($part,$vars,$baseobj);
        }

        return $vars;
    }

    public function getRenderedNotification(AfEvent $record){

        $class = AfHelper::getTableClass($record->dbtable);
        $template_obj = $record->afRule->afTemplate;
        $new_vars = [];

        if(class_exists($class)){
            $obj = $class::find($record->dbkey);

            if($obj){
                $vars[$record->dbtable] = $obj;

                // make sure the variable array is "reset" for each record
                $new_vars = AfRender::varReplacer($obj,$template_obj->notification_template,$vars);
            }
        }

        return AfRender::renderTemplate($record->afRule->afTemplate,$new_vars,'');
    }


    private function extractReplacementParts($data)
    {
        preg_match_all('/{{\$(.*?)}}/i', $data, $regs);
        $parts = [];

        foreach ($regs[1] as $key => $val) {
            $parts[] = $val;
        }

        return $parts;
    }

    /**
     * Returns json encoded string
     *
     * @param $data
     * @param $template_id
     * @param $template
     * @return string - Returns json encoded string
     */
    public function mockVarReplacer($data, $template_id = null, $template = '',$debug=true) : string
    {
        $replace = [];
        $parts = $this->extractReplacementParts($data);

        foreach ($parts as $k => $part) {
            $replace = $this->getObjectColumn($part, $replace,false,$debug);
        }

        if (!$replace) {
            return json_encode($data);
        }

        foreach ($replace as $key => $value) {
            $data = str_replace($key, $value, $data);
        }

        if ($template_id) {
            $vars['content'] = $data;

            try {
                $return = view('vendor.activity-feed.' . $template_id . '.' . $template . 'notification', $vars)->render();
            } catch (\Throwable $exception) {
                return json_encode($this->cleanUpCss($data));
            }

            return json_encode($this->cleanUpCss($return));
        }

        return json_encode($this->cleanUpCss($data));
    }

    public function getObjectColumn($column_string, $replace,$obj=false,$debug=false)
    {
        $path = explode('->', $column_string);

        if(!$obj){
            $obj = $this->getMockObject($path[0]);
        }

        $key = '{{$' . $column_string . '}}';

        if ($obj AND isset($path[1])) {
            $pointer = $path[1];
            $final = false;

            // this means it's the last one
            if (stristr($pointer, ' ')) {
                $pointer = explode(' ', $pointer);
                $pointer = $pointer[0];
                $final = $obj->{$pointer};
            } else {
                $final = $obj;

                array_shift($path);

                foreach ($path as $item) {
                    if (stristr($item, ' ')) {
                        $item = explode(' ', $item);
                        if(isset($final->{$item[0]})){
                            $final = $final->{$item[0]};
                        }
                    } elseif(isset($final->{$item})) {
                        $final = $final->{$item};
                    }
                }
            }

            if (is_string($final)) {
                $replace[$key] = $final;
            } else {
                if($debug){
                    $replace[$key] = $this->returnFieldError($key.' NOT FOUND "' . $path[0].'"!');
                } else {
                    $replace[$key] = '?';
                }
            }
        } else {
            if($debug){
                $replace[$key] = $this->returnFieldError($key.' NOT FOUND "' . $path[0].'"!');
            } else {
                $replace[$key] = '?';
            }
        }

        return $replace;
    }

    private function returnFieldError($msg){
        return '<span class="text-danger">'.$msg.'</span>';
    }

    private function cleanUpCss($data)
    {
        $data = str_replace('body {', '.af-box-preview-notification {', $data);
        $data = str_replace('body{', '.af-box-preview-notification {', $data);
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

        return null;
    }

    public function renderTemplate(AfTemplate $template, $vars = [], $type = 'email-')
    {
        $output = '';


        try {
            $output = view('vendor.activity-feed.' . $template->id . '.' . $type . 'notification', $vars)->render();
        } catch (\Throwable $exception) {
            $template->error = $exception->getMessage();
            $template->save();
            return false;
        }

        if ($output and $template->error) {
            $template->error = null;
            $template->save();
        }

        return $output;
    }

    public function eventObjectReplacement(AfEvent $event_obj, $vars = []): array
    {
        // if we have a originating table & key for record, we'll load the object
        if ($event_obj->dbtable and $event_obj->dbkey) {
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
     *
     *
     * @param AfNotification $notification
     * @return string
     */
    public function getMessage(AfNotification $notification): string
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

        $vars = $this->eventObjectReplacement($event_obj, $vars);
        $template = $this->renderTemplate($template_obj, $vars);

        $template = $this->mockVarReplacer($template, $notification->afEvent->afRule->id, $notification->afEvent->afRule->afTemplate->template);

        if (!$template) {
            return '';
        }

        // if we have a master template, we'll load that also, sending the already
        // loaded template as "content"
        if ($master_template_id) {
            $vars['content'] = $template;
            if ($output = $this->renderTemplate($parent_obj, $vars)) {
                return $output;
            }
        }

        return $template;
    }

    public function getFeedUnreadCount()
    {
        if (!$this->id_user) {
            $this->id_user = auth()->user()->id;
        }

        if (!$this->id_user) {
            return 0;
        }

        return AfNotification::where('id_user_recipient', '=', $this->id_user)
            ->where('read', '=', 0)
            ->count();
    }

    public function getFeed($with_template = false)
    {
        if (!$this->id_user) {
            $this->id_user = auth()->user()->id;
        }
        if (!$this->id_user) {
            return '';
        }

        $feed = AfNotification::where('id_user_recipient', '=', $this->id_user)->with([
            'afRule', 'recipient', 'creator', 'afRule.afEvent', 'afRule.afTemplate','afEvent'
        ])->get();

        $items = [];

        foreach ($feed as $item) {
            if ($with_template) {
                $items[] = view('vendor.activity-feed.' . $item->AfRule->AfTemplate->id . '.notification', [
                    'recipient' => $item->recipient,
                    'creator' => $item->creator,
                    'notification' => $item
                ])->render();
            } else {
                $items[] = [
                    'link' => str_replace('{id}',$item->afEvent->dbkey,$item->AfRule->AfTemplate->url_template),
                    'short_message' => $item->AfRule->AfTemplate->notification_template,
                    'time' => $item->created_at,
                    'read' => $item->read,
                    'id' => $item->id
                ];
            }
        }


        if ($with_template) {
            return view('af_feed::af-components.feed', ['feed' => $items]);
        }

        return $items;
    }

    public function setUser($id)
    {
        $this->id_user = $id;
        return new static;
    }


}
