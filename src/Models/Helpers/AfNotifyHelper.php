<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AfNotifyHelper extends Model
{

    use HasFactory;

    private $id_user_creator;
    private $digestible;
    private $dbtable;
    private $dbfield;
    private $dbkey;

    private $extra_html;


    public function add(string $rule_slug) : bool{

        $rule = AfRule::where('slug','=',$rule_slug)->first();

        if(!$rule){
            return false;
        }

        $obj = new AfEvent();
        $obj->id_user_creator = $this->id_user_creator;
        $obj->digestible = $this->digestible;
        $obj->dbtable = $this->dbtable;
        $obj->dbkey = $this->dbkey;
        $obj->html = $this->extra_html;
        $obj->id_rule = $rule->id;

        try {
            $obj->save();
        } catch (\Throwable $exception){
            Log::log('error', $exception->getMessage());
            return false;
        }

        return true;
    }

    /**
     * @param mixed $dbfield
     * @return AfNotifyHelper
     */
    public function setDbField(string $dbfield) : AfNotifyHelper
    {
        $this->dbfield = $dbfield;
        return $this;
    }

    /**
     * @param mixed $dbkey
     * @return AfNotifyHelper
     */
    public function setDbKey(int $dbkey) : AfNotifyHelper
    {
        $this->dbkey = $dbkey;
        return $this;
    }

    /**
     * @param mixed $dbkey
     * @return AfNotifyHelper
     */
    public function setExtraHtml(string $html) : AfNotifyHelper
    {
        $this->extra_html = $html;
        return $this;
    }

    /**
     * @param mixed $dbtable
     * @return AfNotifyHelper
     */
    public function setDbTable(string $dbtable) : AfNotifyHelper
    {
        $this->dbtable = $dbtable;
        return $this;
    }

    /**
     * @param mixed $digestible
     * @return AfNotifyHelper
     */
    public function setDigestible(bool $digestible) : AfNotifyHelper
    {
        $this->digestible = $digestible ? 1 : 0;
        return $this;
    }

    /**
     * @param mixed $id_user_creator
     * @return AfNotifyHelper
     */
    public function setUser(int $id_user_creator) : AfNotifyHelper
    {
        $this->id_user_creator = $id_user_creator;
        return $this;
    }


}


