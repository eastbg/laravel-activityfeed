<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
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
        $cache = Cache::get('af_rules');

        if($cache){
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
        return $output;
    }

    public function getTables(): array
    {
        $tables = DB::select('SHOW TABLES');
        $output = [];
        $include = config('af-config.af_tables') ?? [];
        $exclude = config('af-config.af_exclude_tables') ?? [];

        foreach ($tables as $key => $table) {

            foreach ($table as $t) {
                if (!stristr($t, 'af_')) {

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
        $class = config('af-config.af_model_path') . '\\' . $table;
        $reflector = new \ReflectionClass($class);
        $output = [];
        $exclude = ['booted', 'save', 'delete', 'update'];

        foreach ($reflector->getMethods() as $method) {
            if (stristr($method->class, config('af-config.af_model_path'))) {
                if (!in_array($method->name, $exclude)) {
                    $output[] = $method->name;
                }
            }
        }

        return $output;
    }

    public function getTargeting(string $table)
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


