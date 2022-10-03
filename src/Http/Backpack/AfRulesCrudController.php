<?php

namespace East\LaravelActivityfeed\Http\Backpack;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\CloneOperation;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategory;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use East\LaravelActivityfeed\Models\ActiveModels\Users;
use East\LaravelActivityfeed\Requests\AfRulesRequest;

/**
 * Class AfRulesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AfRulesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;

    use CloneOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(AfRule::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/af-rules');
        CRUD::setEntityNameStrings('Rule', 'Rules');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('enabled')->type('check');;
        CRUD::column('digestible')->type('check');;
        CRUD::column('rule_type');
        CRUD::column('table_name');
        CRUD::column('field_name');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    public function fetchFields()
    {
        return $this->fetch(AfTemplate::class);
    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {


        // todo: custom view & component which warns about possible missing
        // variables when template + table have been selected
        //$this->crud->setCreateView('af_feed::backpack.af-views.template-create-form');

        CRUD::setValidation(AfRulesRequest::class);

        $this->crud->addField([
            'type' => 'custom_html',
            'name' => 'scripts',
            'tab' => 'Info',
            'value' => "<script>window.addEventListener('load', function () {
    afTargetingDisplay('rule_type');
    afConfigureTableName();
});</script>"]);

        $this->tabInfo();
        $this->tabSetup();
        $this->tabTargeting();


    }

    private function tabSetup()
    {

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'rule_type',
                'tab' => 'Rule Setup',
                'label' => 'Rule Type',
                'type' => 'af_select_from_array',
                'options' => [
                    '' => '-- Please Select --',
                    'Record change' => 'Record change',
                    'Field change' => 'Field change',
                    'New record' => 'New record',
                    'Delete record' => 'Delete record',
                    'Field value' => 'Field value',
                    'Custom script' => 'Custom script',
                    'Manual notification' => 'Manual event from code',
                ],
                'onchange' => [
                    [
                        'function' => 'afTargetingDisplay',
                        'parameters' => [
                            'rule_type',
                        ]
                    ],
                ],
                'hint' => 'Select what triggers',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'targeting2'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'table_name',
                'label' => 'Table',
                'tab' => 'Rule Setup',
                'type' => 'af_select_from_array',
                'options' => AfHelper::gettables(),
                'hint' => 'Leave empty to ignore',
                'onchange' => [
                    [
                        'function' => 'afUpdateField',
                        'parameters' => [
                            'table_name',
                            '/af-data/columns',
                            'field_name'
                        ]
                    ],
                    [
                        'function' => 'afUpdateField',
                        'parameters' => [
                            'table_name',
                            '/af-data/targeting',
                            'targeting'
                        ]
                    ],
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_table_name'
                ],
            ]
        );

        //$this->crud->addField(['name' => 'separator-1','type' => 'af_separator','label' => 'Rule Setup']);


        $this->crud->addField(
            [
                // select_from_array
                'name' => 'rule_script',
                'tab' => 'Rule Setup',
                'label' => 'Custom Script',
                'type' => 'af_select_from_array',
                'options' => AfHelper::getRuleScripts(),
                'hint' => 'Select a custom rule script (usually cron jobs)',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_rule_script'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'field_name',
                'tab' => 'Rule Setup',
                'label' => 'Column',
                'type' => 'af_select_from_array',
                'options' => ['-- Any --'],
                'hint' => 'Select the database column',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_field_name'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'rule_operator',
                'tab' => 'Rule Setup',
                'label' => 'Operator',
                'type' => 'select_from_array',
                'options' => AfHelper::getRuleOperators(),
                'hint' => 'Select the database column',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_rule_operator'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'rule_value',
                'tab' => 'Rule Setup',
                'label' => 'Value',
                'type' => 'text',
                'hint' => 'Value when using an operator / custom script',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_rule_value'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

    }

    private function tabTargeting()
    {
        $this->crud->field('to_admins')->type('checkbox')->label('Add to admins')->hint('Whether this notification is shown to admins.')->tab('Targeting');

        $this->crud->addField(
            [   // radio
                'name' => 'channels', // the name of the db column
                'tab' => 'Targeting',
                'label' => 'Channels', // the input label
                'type' => 'af_select_multiple_json',
                //'selected' => $this->crud->model->channels,
                'allow_multiple' => true,
                'options' => AfHelper::getChannels(),
                // optional
                'inline' => false, // show the radios all on the same line?
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'targeting',
                'tab' => 'Targeting',
                'label' => 'Targeting',
                'type' => 'af_select_from_array',
                'options' => ['-- Select the table first --'],
                'hint' => 'Select the database column',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_targeting'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );


    }

    private function tabInfo()
    {

        $this->crud->field('name')->tab('Info');
        $this->crud->field('enabled')->type('checkbox')->label('Enabled')->tab('Info');
        $this->crud->field('description')->type('textarea')->tab('Info');
        $this->crud->field('digestible')->type('checkbox')->label('Digestible')->hint('Email will be included in the digest as opposed to sending right away.')->tab('Info');
        $this->crud->field('digest_delay')->type('text')->label('Digest delay')->hint('Value in seconds before trying to combine same event types.')->tab('Info');
        $this->crud->field('background_job')->type('checkbox')->label('Background Job')->hint('This rule is based on analysing database rather than reactive to table changes. For example: all users that have been inactive for more than a week.')->tab('Info');

        $this->crud->addField(
            [
                'name' => 'id_template',
                'label' => 'Template',
                'tab' => 'Info',
                'type' => 'select_from_array',
                'options' => AfTemplate::all()->pluck('name', 'id')->toArray()
            ]
        );

        $this->crud->addField(
            [
                'name' => 'id_user_owner',
                'tab' => 'Info',
                'label' => 'Default owner',
                'hint' => 'If this is selected the "from" of any message will always be from this user.',
                'type' => 'select_from_array',
                'options' => \App\ActivityFeed\AfUsersModel::where('admin','=',1)->pluck('name', 'id')->toArray(),
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_owner'
                ],
            ]
        );


        $this->crud->addField(
            [
                'name' => 'id_parent',
                'tab' => 'Info',
                'label' => 'Parent template (optional)',
                'hint' => 'You can define master template to "decorate" the actual template',
                'type' => 'select_from_array',
                'options' => AfTemplate::where('master_template', '=', 1)->pluck('name', 'id')->toArray()
            ]
        );


        $this->crud->addField(
            [
                'name' => 'id_category',
                'label' => 'Category',
                'tab' => 'Info',
                'type' => 'select_from_array',
                'options' => AfCategory::all()->pluck('name', 'id')->toArray()
            ]
        );


    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
