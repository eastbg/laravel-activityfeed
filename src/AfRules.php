<?php

namespace East\LaravelActivityfeed;

use Illuminate\Support\Facades\Facade;

class AfRules {

    public $random;
    public $id_user;

    public function getFeed(){
        if(!$this->random){
            $this->random = rand(12,239329329329);
        }

        return view('af_feed::components.feed',[
            'random' => $this->random .' - '.$this->id_user
        ]);
    }

    public function setUser(int $id){
        $this->id_user = $id;
        return $this;
    }




}