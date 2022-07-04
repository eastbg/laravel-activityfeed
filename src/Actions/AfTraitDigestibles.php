<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use Carbon\Carbon;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Facades\AfRender;
use East\LaravelActivityfeed\Facades\AfTemplating;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/*
 * 1. Create custom rules (takes grace period from the class)
 * 2. Runs unprocessed custom rules, creating notifications
 * 3. Feed items are just entries in the database, external sends are handled by another cron (afpoll:send)
 * */

trait AfTraitDigestibles
{

    /**
     * Takes an event, finds related events that haven't been digested.
     *
     * Unlike normal events, the digests are rendered and saved to notification
     * database at this point. Reason for this is performance and clarity.
     *
     * If it's time to digest, it creates a new event for that.
     *
     * @param AfEvent $event
     * @return void
     */
    public function handleDigestible(AfEvent $event){

        // find other digestibles, the digesting time is determined by the first record
        $records = AfEvent::where('digested','=','0')
            ->where('digestible','=','1')
            ->where('id_rule', '=', $event->id_rule)
            ->with('afRule','afRule.afTemplate','afRule.afTemplate.afParent')->get();

        foreach($records as $record){
            $timing = Carbon::now()->addSeconds($record->afRule->digest_delay)->toDateTimeString();

            if($record->created < $timing){
                $this->digest($event,$records);
                break;
            }
        }
    }

    /**
     * Now we merge all digestibles into one event and delete the
     * individual events. The events are available as an array,
     * which you can @foreach in the template.
     *
     * @param AfEvent $event
     * @param array $records
     * @return void
     */
    private function digest(AfEvent $event, Collection $records){
        $template = '';

        $vars = [
            'creator' => $event->creator,
            'events' => $records
        ];

        $template_obj = $event->afRule->afTemplate;
        $parent = AfTemplate::find($template_obj->id_parent);

        foreach ($records as $record) {
            $vars = AfRender::eventObjectReplacement($record,$vars);
            $template .= AfRender::renderTemplate($record->afRule->afTemplate,$vars,'digest-');
        }

        if($event->afRule->afTemplate->afParent()){
            $vars = [];
            $vars['content'] = $template;
            $template = AfRender::renderTemplate($parent,$vars);
        }

        $event->digest_content = $template;
        $event->processed = 1;
        $event->digested = 1;
        $event->save();

        foreach ($records as $record) {
            if($record->id == $event->id){
                continue;
            }

            try {
                $record->delete();
            } catch (\Throwable $exception){
                Log::error('DIGEST DELETION PROBLEM: '.$exception->getMessage());
            }
        }

    }




}