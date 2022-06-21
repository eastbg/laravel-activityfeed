<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use East\LaravelActivityfeed\Models\Helpers\AfCaching;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

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
        $this->cache = App::make(AfCaching::class);

        if(!$this->cache->random){
            $this->cache->random = rand(12,239329329329);
        }

        parent::__construct($attributes);
    }

    public function getFeed(){
        return view('af_feed::components.feed',['random' => $this->cache->random.' - '.$this->id_user]);
    }

    public function setUser($id){
        $this->id_user = $id;
        return new static;
    }



}
