<?php

namespace East\LaravelActivityfeed\Http\Backpack;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use East\LaravelActivityfeed\Facades\AfHelper;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategory;
use East\LaravelActivityfeed\Models\ActiveModels\AfRule;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
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
        CRUD::column('enabled');
        CRUD::column('rule_type');
        CRUD::column('rule');
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
        CRUD::setValidation(AfRulesRequest::class);


        $this->crud->field('name');
        $this->crud->field('enabled')->type('checkbox')->label('Enabled');
        $this->crud->field('description')->type('textarea');
        $this->crud->field('background_job')->type('checkbox')->label('Background Job')->hint('This rule is based on analysing database rather than reactive to table changes. For example: all users that have been inactive for more than a week.');
        $this->crud->field('digestible')->type('checkbox')->label('Digestible')->hint('Email will be included in the digest as opposed to sending right away.');

        $this->crud->addField(
            [
                'name'  => 'id_template',
                'label' => 'Template',
                'type'  => 'relationship',
                'inline_create' => true,
                'entity' => 'afTemplates',
                'model' => AfTemplate::class,
                'data_source' => url('fetch/template'),
            ]
        );

        $this->crud->addField(
            [
                'name'  => 'id_category',
                'label' => 'Category',
                'type'  => 'relationship',
                'inline_create' => true,
                'entity' => 'afCategories',
                'model' => AfCategory::class,
                'data_source' => url('/admin/af-categories/fetch/category'),
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'table_name',
                'label' => 'Table',
                'type' => 'select2_from_array2',
                'options' => AfHelper::getTables(),
                'hint' => 'Leave empty to ignore',
                'selected' => [],
                'onchange' => 'updateSelectedTable',
                'wrapper'   => [
                    'class' => 'form-group col-md-12',
                    'id' => 'targeting2'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

        $this->crud->addField(
            [
                // select_from_array
                'name' => 'field_name',
                'label' => 'Field',
                'type' => 'select_from_array2',
                'options' => ['id' => 'id'],
                'hint' => 'Select the database column',
                'wrapper'   => [
                    'class' => 'form-group col-md-12',
                    'id' => 'targeting2'
                ], // change the HTML attributes for the field wrapper - mostly for resizing fields
            ]
        );

        $this->crud->addField(
            [
                'name'  => 'targeting',
                'label' => 'Targeting',
                'type'  => 'repeatable',
                'subfields' => [ // also works as: "fields"
                    [
                        'name'    => 'Table',
                        'type'    => 'text',
                        'label'   => 'Title',
                        'wrapper' => ['class' => 'form-group col-md-6'],
                    ],
                    [
                        'name'    => 'Level',
                        'label'   => 'Level (5 is better)',
                        'type' => 'select_from_array',
                        'options' => [5,4,3,2,1],
                        'wrapper' => ['class' => 'form-group col-md-4'],
                    ],
                ],

                // optional
                'new_item_label'  => 'Add Item', // customize the text of the button
                'init_rows' => 1, // number of empty rows to be initialized, by default 1
                'min_rows' => 0, // minimum rows allowed, when reached the "delete" buttons will be hidden
                'max_rows' => 10, // maximum rows allowed, when reached the "new item" button will be hidden
                // allow reordering?
                'reorder' => true, // hide up&down arrows next to each row (no reordering)
                //'reorder' => true, // show up&down arrows next to each row
                /*                'reorder' => 'order', // show arrows AND add a hidden subfield with that name (value gets updated when rows move)
                                'reorder' => ['name' => 'order', 'type' => 'number', 'attributes' => ['data-reorder-input' => true]], // show arrows AND add a visible number subfield*/
            ]
        );

/*        CRUD::field('id');
        CRUD::field('created_at');
        CRUD::field('updated_at');
        CRUD::field('id_category');
        CRUD::field('id_template');
        CRUD::field('name');
        CRUD::field('description');
        CRUD::field('rule_type');
        CRUD::field('rule');
        CRUD::field('table_name');
        CRUD::field('field_name');
        CRUD::field('rule_operator');
        CRUD::field('rule_value');
        CRUD::field('rule_actions');
        CRUD::field('context');
        CRUD::field('background_job');
        CRUD::field('digestible');
        CRUD::field('enabled');*/

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
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
