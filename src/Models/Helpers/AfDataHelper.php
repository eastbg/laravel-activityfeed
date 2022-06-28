<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use App\ActivityFeed\AfUsersModel;
use East\LaravelActivityfeed\Models\ActiveModels\AfNotification;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfUsers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfDataHelper extends Model
{
    use HasFactory;

    public static $caches = [
        'af_rules',
        'af_template_files'
    ];

    public $random;
    public $rules;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function flushCaches()
    {
        foreach (self::$caches as $cache) {
            Cache::delete($cache);
        }
    }

    public function getTableRules(string $table,string $rule_type) : array{
        $rules = $this->getRules();
        if(isset($rules[$table][$rule_type])){
            return $rules[$table][$rule_type];
        }

        return [];
    }

    public function getRules() : array {
        if($this->rules){ return $this->rules; }

        $cache = Cache::get('af_rules');

        if($cache){
            $this->rules = $cache;
            return $cache;
        }

        return $this->makeRules();
    }

    private function makeRules() : array{

        $rules = AfRule::all();
        $output = [];

        foreach($rules as $rule){
            if(!$rule->table_name){ continue; }
            if(!$rule->rule_type){ continue; }
            if(!$rule->enabled){ continue; }
            $output[$rule->table_name][$rule->rule_type][] = $rule;
        }

        Cache::set('af_rules',$output);
        $this->rules = $output;

        return $output;
    }

    public function getTables(): array
    {
        $tables = DB::select('SHOW TABLES');
        $output = [];
        $include = config('af-config.af_tables') ?? [];
        $exclude = config('af-config.af_exclude_tables') ?? [];

        $output['AfNotification'] = 'AfNotification';
        $output['AfUsers'] = 'AfUsers';

        foreach ($tables as $key => $table) {

            foreach ($table as $t) {
                if (!stristr($t, 'af_')) {

                    try {
                        $class = config('af-config.af_model_path') . '\\' . $t;
                        $reflector = new \ReflectionClass($class);
                    } catch (\Throwable $e){
                        continue;
                    }

                    if (!empty($include) and in_array($t, $include)) {
                        $output[$t] = $t;
                    } elseif (!empty($exclude) and !in_array($t, $exclude)) {
                        $output[$t] = $t;
                    } elseif (empty($exclude) and empty($include)) {
                        $output[$t] = $t;
                    }
                }
            }
        }

        return $output;
    }

    public function getColumns(string $table): array
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        $output[] = '-- Any --';

        foreach ($columns as $col) {
            $output[] = $col;
        }

        return $output;
    }


    public function getRelationships(string $table): array
    {
        $reflector = new \ReflectionClass($this->getTableClass($table));
        $output = [];
        $exclude = ['booted', 'save', 'delete', 'update'];

        foreach ($reflector->getMethods() as $method) {
            if($table == 'AfUsers' AND stristr($method->class,'Models\ActiveModels\AfUsers')){
                $output[] = $method->name;
            }elseif (stristr($method->class, config('af-config.af_model_path'))) {
                if (!in_array($method->name, $exclude)) {
                    $output[] = $method->name;
                }
            }
        }

        return $output;
    }

    public function getTableFields(string $table){
        $obj = $this->getTableModel($table);
        if(!$obj){ return []; }
        $output = [];

        foreach($obj->getFillable() as $attribute){
            $output[] = $attribute;
        }

        return $output;
    }

    public function getTableClass(string $table){
        if($table == 'AfUsers' OR $table == 'creator'){
            $class = '\\'.AfUsers::class;
        } elseif($table == 'notification' OR $table == 'AfNotification') {
            $class = AfNotification::class;
        } else {
            $class = config('af-config.af_model_path') . '\\' . $table;
        }

        return $class;

    }

    public function getTableModel(string $table){
        $class = $this->getTableClass($table);

        if(class_exists($class)){
            return new $class;
        }

        return null;
    }

    public function getTableTargeting(string $table)
    {
        $output = [];

        $output[''] = '-- No targeting, only admins --';

        foreach ($this->getRoutings() as $tbl => $config) {
            if ($table == $tbl) {
                foreach ($config as $k => $v) {
                    $output[$v['id']] = $v['title'];
                }
            }
        }

        return $output;
    }

    public function getTargeting(string $table, string $rule_id)
    {
        $routings = $this->getRoutings();

        foreach($routings as $tbl => $route){
            if($tbl == $table){
                foreach($route as $rule){
                    if(isset($rule['id']) AND $rule['id'] == $rule_id){
                        return $rule;
                    }
                }
            }
        }

        return [];
    }

    public function getRoutings(): array
    {
        return config('af-database-targeting.tables');
    }

    public function getChannels(): array
    {
        return $this->getFiles('Channels', ['ChannelBase.php', 'ChannelTemplate.php']);
    }

    public function getRuleScripts(): array
    {
        return $this->getFiles('Rules', ['RuleBase.php', 'RuleTemplate.php']);
    }

    public function getRuleOperators() : array {
        return [
            '' => '-- No operator --',
            'empty' => 'Is empty',
            'not_empty' => 'Is not empty',
            '=' => '=',
            '<' => '< (value smaller than)',
            '>' => '> (value bigger than)',
        ];
    }

    private function getFiles(string $directory, array $exclusions = [])
    {
        $custom = app_path('ActivityFeed/' . $directory . '/');
        $custom = scandir($custom);

        $built_in = app_path('../vendor/east/laravel-activityfeed/src/ActivityFeed/' . $directory . '/');
        $built_in = scandir($built_in);

        $files = array_merge($built_in,$custom);
        $output = [];

        foreach ($files as $name) {
            if (stristr($name, '.php') and !in_array($name, $exclusions)) {
                $name = str_replace('.php', '', $name);
                $name = str_replace('Channel', '', $name);
                $output[$name] = $name;
            }
        }

        return $output;

    }


}


