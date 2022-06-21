<?php

namespace East\LaravelActivityfeed\Models\Helpers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AfGenerator extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function writeRules(){
        $this->getDatabaseInfo();
    }


    /**
     *
     * @param $module_name
     * @return void
     */
    private function getDatabaseInfo()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = DB::select('SHOW TABLES');

        foreach($tables as $key=>$table){
            if(stristr($table,'af_')){
                $tables[] = $table;
            }
        }

        die();
        $columns = DB::getSchemaBuilder()->getColumnListing($module_name);

        if ($id) {
            $records[] = $this->getRecord($module_name,$id);
        } else {
            $builder = CriteriaBuilder::where('Sync', 'Yes');
            $records = $this->getAllZohoRecords($builder, $module_name);
        }

        $this->outputMessage('info', 'Got ' . count($records) . ' records for ' . $module_name);
        $existing = [];

        foreach ($records as $record) {
            try {
                $id = $record->getEntityId();
            } catch (\Throwable $e){
                $this->outputMessage('error','Could not get ID');
                continue;
            }

            $data = $record->getData();
            $values = [];

            // this is for deleting
            $existing[$id] = true;

            foreach ($columns as $col) {
                if (isset($data[$col]) and is_array($data[$col])) {
                    if (!empty($data[$col])) {
                        if (isset($data[$col][0]['download_Url'])) {
                            $values[$col] = $data[$col][0]['download_Url'];
                        } else {
                            $values[$col] = json_encode($data[$col]);
                        }
                    }
                } elseif (isset($data[$col]) and is_object($data[$col])) {
                    $values[$col] = $data[$col]->getEntityId();
                } elseif (isset($data[$col])) {
                    $values[$col] = $data[$col];
                }
            }

            $class = '\App\Models\Zoho\Modules\Models\\' . $module_name;
            $test = $class::where('id_zoho', '=', $id)->first();
            $values['id_zoho'] = $id;

            try {
                $class = 'App\Models\Zoho\Modules\Models\\' . $module_name;
                $test = $class::where('id_zoho', '=', $id)->first();
            } catch (\Throwable $exception) {
                $this->outputMessage('warn', $exception->getMessage());
            }

            // update or insert
            if ($test) {
                try {
                    $test->setRawAttributes($values);
                    $test->save([
                        'no_zoho_update' => true,
                        'no_relations_update' => true,
                        'no_email_update' => true
                    ]);
                } catch (\Throwable $exception) {
                    $this->outputMessage('error', $exception->getMessage());
                }
            } else {
                try {
                    DB::table($module_name)->insert($values);
                } catch (\Throwable $exception) {
                    $this->outputMessage('error', $exception->getMessage());
                }
            }
        }

        if ($with_delete and $records > 0) {
            $this->deleteNonExistingFromModule($module_name, $existing);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        return $id;
    }

    private function DiscoverRules(){

    }



}


