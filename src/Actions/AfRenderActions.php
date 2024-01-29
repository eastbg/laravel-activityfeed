<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use App\Models\Zoho\Modules\Models\Candidates;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Facades\AfRender;
use East\LaravelActivityfeed\Facades\AfTemplating;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use East\LaravelActivityfeed\Models\Helpers\AfDataHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AfRenderActions extends Model
{
    use HasFactory;

    private $triggers;
    private $schema;
    public $random;
    public $id_user;
    public $cache;

    public $id_template;

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
     * @param $baseobj --
     * @param $data string -- the content where we extract the parts
     * @param $vars array
     * @param bool $without_tags -- set true to get templating compatible array
     * @return string
     */
    public function varReplacer($baseobj, $data, $vars = [],$without_tags=false): array
    {
        $keys = $this->extractReplacementParts($data);

        foreach ($keys as $k => $key) {

            $parts = explode('->', $key);

            if (isset($parts[0])) {
                // base table
                array_shift($parts);
                $value = $baseobj;

                // loop to the actual value
                foreach ($parts as $column) {
                    try {
                        $value = $value->$column;
                    } catch (\Throwable $exception) {
                        AfHelper::addTemplateError($this->id_template, 'You have incorrectly defined relations in this template.
Please note, that the base class here is ' . $baseobj::class . ' . ' . $exception->getMessage(), false);
                    }
                }

                if (is_string($value) or is_float($value) or is_int($value)) {
                    if($without_tags){
                        $vars[$key] = $value;
                    } else {
                        $vars['{{$' . $key . '}}'] = $value;
                    }
                }
            }
        }

        return $vars;
    }

    public function getTemplateRelations($template, $class)
    {
        $parts = $this->extractReplacementParts($template);
        $output = [];

        foreach ($parts as $part) {
            $vars = explode('->', $part);

            // like this, as the source table is always mentioned in the relation
            if (isset($vars[2])) {

                // base table
                array_shift($vars);

                foreach ($vars as $table) {
                    $line[] = $table;
                }

                // actual field
                array_pop($line);
                $output[] = implode('.', $line);
                unset($line);
            }
        }

        return array_unique($output);
    }


    public function getRenderedNotification(AfNotification $record)
    {

        $class = AfHelper::getTableClass($record->afEvent->dbtable);
        $template_obj = $record->afEvent->afRule->afTemplate;
        $new_vars = [];

        if (!$template_obj) {
            return false;
        }

        if (class_exists($class)) {
            $with = $this->getTemplateRelations($record->afEvent->afRule->afTemplate, $class);

            try {
                $obj = $class::where('id', '=', $record->afEvent->dbkey)
                    ->with($with)
                    ->first();
            } catch (\Throwable $exception) {
                AfHelper::addTemplateError($record->afEvent->afRule->id_template, 'You have incorrectly defined relations in this template.
Please note, that the base class here is ' . $class . ' . ' . $exception->getMessage());
                $obj = $class::find($record->afEvent->dbkey);
            }


            if ($obj) {
                $vars[$record->afEvent->dbtable] = $obj;

                // make sure the variable array is "reset" for each record
                $new_vars = AfRender::varReplacer($obj, $template_obj->notification_template, $vars);
            }
        }

        return AfRender::renderTemplate($record->afEvent->afRule->afTemplate, $new_vars, '');
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
    public function mockVarReplacer($data, $template_id = null, $template = '', $debug = true): string
    {
        $replace = [];
        $parts = $this->extractReplacementParts($data);

        foreach ($parts as $k => $part) {
            $replace = $this->getObjectColumn($part, $replace, false, $debug);
        }

        if (!$replace) {
            return json_encode($data);
        }

        foreach ($replace as $key => $value) {
            $data = str_replace($key, $value, $data);
        }

        if ($template_id) {
            $vars['content'] = $this->varReplacer();

            try {
                $return = view('vendor.activity-feed.' . $template_id . '.' . $template . 'notification', $vars)->render();
            } catch (\Throwable $exception) {
                return json_encode($this->cleanUpCss($data));
            }

            return json_encode($this->cleanUpCss($return));
        }

        return json_encode($this->cleanUpCss($data));
    }

    // deprecated - varReplacer makes this in simpler way
    public function getObjectColumn($column_string, $replace, $obj = false, $debug = false)
    {
        $path = explode('->', $column_string);

        if (!$obj) {
            $obj = $this->getMockObject($path[0]);
        }

        $key = '{{$' . $column_string . '}}';

        if ($obj and isset($path[1])) {
            $pointer = $path[1];

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
                        if (isset($final->{$item[0]})) {
                            $final = $final->{$item[0]};
                        }
                    } elseif (isset($final->{$item})) {
                        $final = $final->{$item};
                    }
                }
            }

            if (is_string($final)) {
                $replace[$key] = $final;
            } else {
                if ($debug) {
                    $replace[$key] = $this->returnFieldError($key . ' NOT FOUND "' . $path[0] . '"!');
                } else {
                    $replace[$key] = '?';
                }
            }
        } else {
            if ($debug) {
                $replace[$key] = $this->returnFieldError($key . ' NOT FOUND "' . $path[0] . '"!');
            } else {
                $replace[$key] = '?';
            }
        }

        return $replace;
    }

    private function returnFieldError($msg)
    {
        return '<span class="text-danger">' . $msg . '</span>';
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

    /**
     * @param AfTemplate $template
     * @param $vars
     * @param $type
     * @return string
     */
    public function renderTemplate(AfTemplate $template, $vars = [], $type = 'email-') : string
    {
        $output = '';

        try {
            $output = view('vendor.activity-feed.' . $template->id . '.' . $type . 'notification', $vars)->render();
        } catch (\Throwable $exception) {
            $template->error = $exception->getMessage();
            $template->save();
            $msg = 'AF-NOTIFY: ' . $template->slug .' '.$exception->getMessage();
            AfHelper::addTemplateError($template->id,$msg);
            return '';
        }

        if ($output and $template->error) {
            $template->error = null;
            $template->save();
        }

        if(!stristr($output,'<html>')){
            $output = '<!DOCTYPE html><html><head></head><body>'.$output.'</body></html>';
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
        $email = $notification->afEvent->afRule->afTemplate->email_template;

        $vars = [
            'user' => $notification->recipient,
            'creator' => $notification->creator,
            'notification' => $notification,
            'field' => $notification->afEvent->field
        ];

        $baseobj = $this->getEventRecord($notification->afEvent);

        if($baseobj){
            // we reference the base object by using table's name
            $vars[$notification->afEvent->dbtable] = $baseobj;
        }

        $template = $this->renderTemplate($template_obj, $vars);

        if($html = $notification->afEvent->html){
            $template = $template.$html;
        }

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


    public function getFeedUnreadCount($date = null)
    {
        if (!$this->id_user) {
            $this->id_user = auth()->user()->id;
        }

        if (!$this->id_user) {
            return 0;
        }

        if (isset(auth()->user()->admin) and auth()->user()->admin) {
            $query = AfNotification::where('id_user_recipient', '=', $this->id_user)->where('read',0);
            if(!empty($date)) {
                $query->where('af_notifications.created_at','>',$date);
            }
            return $query->count();
        }

        return AfNotification::where('id_user_recipient', '=', $this->id_user)
            ->where('read', '=', 0)
            ->count();
    }


    public function getFeed($unread_only = true, $with_template = false, $from = 0, $to = 100, $date = null)
    {
        if (!$this->id_user) {
            $this->id_user = auth()->user()->id;
        }

        if (!$this->id_user) {
            return '';
        }

        $cache_name = $unread_only ? 'notifications-' . $this->id_user . '-unread' : 'notifications-' . $this->id_user . '-read';

        if ($items = Cache::get($cache_name)) {
            if ($with_template) {
                return view('af_feed::af-components.feed', ['feed' => $items]);
            }
            return $items;
        }


        if (isset(auth()->user()->admin) and auth()->user()->admin) {
            $get_query = AfNotification::where('id_user_recipient', '=', $this->id_user)
                ->join('af_rules', 'af_notifications.id_rule', '=', 'af_rules.id')
                ->with(['afRule', 'recipient', 'creator', 'afRule.afEvent', 'afRule.afTemplate', 'afEvent'])
                ->select('af_notifications.*','af_rules.id_template');
            if ($unread_only) {
                $get_query->where('read',0);
            }
            if(!empty($date)) {
                $get_query->where('af_notifications.created_at','>',$date);
            }
            $query = $get_query->orderBy('af_notifications.id', 'desc')
                ->orderBy('af_notifications.id', 'desc')
                ->groupBy('af_notifications.id')
                ->offset($from)
                ->limit($to)
                ->get();
        } else {
            if ($unread_only) {
                $query = AfNotification::where('id_user_recipient', '=', $this->id_user)
                    ->with(['afRule', 'recipient', 'creator', 'afRule.afEvent', 'afRule.afTemplate', 'afEvent'])
                    ->where('read', '=', 0)
                    ->orderBy('id', 'DESC')
                    ->groupBy('af_notifications.id')
                    ->offset($from)
                    ->limit($to)
                    ->get();
            } else {
                $query = AfNotification::where('id_user_recipient', '=', $this->id_user)
                    ->with(['afRule', 'recipient', 'creator', 'afRule.afEvent', 'afRule.afTemplate', 'afEvent'])
                    ->orderBy('id', 'DESC')
                    ->offset($from)
                    ->groupBy('af_notifications.id')
                    ->limit($from + $to)
                    ->offset($from)
                    ->limit($to)
                    ->get();
            }
        }

        $items = $this->getFeedRender($query, true);
        Cache::set($cache_name,$items);
        return $items;
    }

    private function getFeedRender($feed, $with_template = false)
    {

        $items = [];

        foreach ($feed as $item) {

            $vars = [];
            $url_template = $item->afRule->afTemplate->url_template ?? '';
            $this->id_template = $item->afRule->afTemplate->id ?? false;

            if(!$this->id_template){
                continue;
            }

            if (!isset($item->afRule->afTemplate) or !$item->afRule->afTemplate) {
                continue;
            }

            if (!$item->afRule->enabled) {
                continue;
            }

            $obj = $this->getEventRecord($item->afEvent);

            if (isset(auth()->user()->admin) and auth()->user()->admin and $item->id_user_recipient != auth()->user()->id) {
                $msg = $item->afRule->afTemplate->admin_template;
            } else {
                $msg = $item->afRule->afTemplate->notification_template;
            }

            $config = [
                'short_message' => $msg,
                'time' => $item->created_at,
                'read' => $item->read,
                'id' => $item->id,
            ];

            if ($url_template and isset($item->afEvent->dbkey) and $item->afEvent->dbkey) {
                $config['link'] = str_replace('{{$id}}', $item->afEvent->dbkey, $url_template);
                $config['link'] = str_replace('{id}', $item->afEvent->dbkey, $config['link']);
            } else {
                $config['link'] = '';
            }

            if(!empty($msg)) {
                $keys = $this->extractReplacementParts($msg);
                if ($item->relations['creator'] && in_array('creator', $keys)) {
                    $config['short_message'] = str_replace('{{$creator}}', $item->relations['creator']->name, $config['short_message']);
                }
                if (in_array('contact', $keys) && $item->afEvent->field) {
                    $config['short_message'] = str_replace('{{$contact}}', $item->afEvent->field, $config['short_message']);
                }
                $msg = $config['short_message'];
            }

            if ($obj) {
                $replace_vars = AfRender::varReplacer($obj, $msg, $vars);

                foreach ($replace_vars as $key => $v) {
                    $config['short_message'] = str_replace($key, $v, $config['short_message']);
                }
            } elseif ($msg) {
                $config['short_message'] = $msg;
            } else {
                $config['short_message'] = 'Notification template missing!';
            }

            $items[] = $config;
        }

        /*        if($items){
                    print_r($items);die();
                }*/


        /*        if ($with_template) {
                    return view('af_feed::af-components.feed', ['feed' => $items]);
                }*/

        return $items;
    }

    private function getEventRecord(AfEvent $event) {
        $obj = null;

        if ($event->dbtable and $event->dbkey) {
            $class = AfHelper::getTableClass($event->dbtable);
            if ($class) {
                $with = $this->getTemplateRelations($event->afRule->afTemplate, $class);

                try {
                    $obj = $class::where('id', '=', $event->dbkey)
                        ->with($with)
                        ->first();

                } catch (\Throwable $exception) {
                    AfHelper::addTemplateError($event->afRule->afTemplate->id, 'You have incorrectly defined relations in this template.
Please note, that the base class here is ' . $class . '. ' . $exception->getMessage());

                    $obj = $class::find($event->dbkey);
                }
            }
        }

        return $obj;
    }

    public function setUser($id)
    {
        $this->id_user = $id;
        return new static;
    }


}
