<?php

namespace East\LaravelActivityfeed\Actions;

use App\Models\Email\Emailer;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\Helpers\AfCachingHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\App;

class AfPollAction extends Model
{
    use HasFactory;

    public function runPoll(){
        $records = AfEvent::where('processed','=','0')->with('afRule')->get();
        foreach($records as $record){
            if($record->afRule->to_admins){
                $this->addToAdmins($record);
            }
        }
    }

    private function addToAdmins($record){
        $users = User::where('admin','=',1)->get();
        foreach($users as $user){
            $this->addToUser($user->id,$record);
        }
    }

    private function addToUser(int $id,AfEvent $record){
        $obj = new AfNotification();
        $obj->id_user_recipient = $id;
        $obj->id_user_creator = $record->id_user_creator;
        $obj->id_rule = $record->id_rule;
        $obj->id_event = $record->id;
        $obj->save();
    }

}
