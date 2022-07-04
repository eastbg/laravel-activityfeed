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
use East\LaravelActivityfeed\Models\Helpers\AfDataHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
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
    public function handleDigestible(AfEvent $event)
    {

        // find other digestibles, the digesting time is determined by the first record
        $records = AfEvent::where('digested', '=', '0')
            ->where('digestible', '=', '1')
            ->where('id_rule', '=', $event->id_rule)
            ->with('afRule', 'afRule.afTemplate', 'afRule.afTemplate.afParent')->get();

        foreach ($records as $record) {
            $timing = Carbon::now()->addSeconds($record->afRule->digest_delay)->toDateTimeString();

            if ($record->created < $timing) {
                $this->digest($event, $records);
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
    private function digest(AfEvent $event, Collection $records)
    {
        $template = '';
        $class = AfHelper::getTableClass($event->dbtable);
        $events = [];

        //$models, Collection $results, $relation


        

        foreach ($records as $record) {
            $events[] = AfEvent::match([$event->'Candidates_X_Technologies'],$records,'try');
        }

        print_r($events);

        $vars = [
            'creator' => $event->creator,
            'events' => $events            // this is a collection including all records
        ];

        print_r($vars);die();

        $template_obj = $event->afRule->afTemplate;
        $parent = AfTemplate::find($template_obj->id_parent);






        $vars = AfRender::eventObjectReplacement($event, $vars);
        $template = AfRender::renderTemplate($template_obj, $vars, 'digest-');

        if ($event->afRule->afTemplate->afParent()) {
            $vars = [];
            $vars['content'] = $template;
            $template = AfRender::renderTemplate($parent, $vars);
        }

        print_r($template);

        foreach ($records as $record) {
            if ($record->id == $event->id) {
                $record->digest_content = $template;
                $record->digestible = 0;         // this is no longer digestible as the digestion is complete
                $record->processed = 0;          // not needed, just put here for clarity
                $record->digested = 1;
                //$record->save();
                continue;
            }

            try {
                //$record->delete();
            } catch (\Throwable $exception) {
                Log::error('DIGEST DELETION PROBLEM: ' . $exception->getMessage());
            }
        }

        echo('done');
        die();

    }


}