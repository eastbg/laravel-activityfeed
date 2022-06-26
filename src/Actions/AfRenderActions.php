<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
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

    public function getFeed() {
        if(!$this->id_user) { $this->id_user = auth()->user()->id; } if(!$this->id_user){ return ''; }

        AfTemplating::compileTemplates();

        $feed = AfNotification::where('id_user_recipient','=',$this->id_user)->with([
            'AfRule','recipient','creator','AfRule.AfTemplate'
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
