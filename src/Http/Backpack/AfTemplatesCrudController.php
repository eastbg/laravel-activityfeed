<?php

namespace East\LaravelActivityfeed\Http\Backpack;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategoriesModel;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategory;
use East\LaravelActivityfeed\Models\ActiveModels\AfRulesModel;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplates;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplatesModel;
use East\LaravelActivityfeed\Requests\AfCategoriesRequest;
use East\LaravelActivityfeed\Requests\AfRulesRequest;
use East\LaravelActivityfeed\Requests\AfTemplatesRequest;

/**
 * Class AfRulesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AfTemplatesCrudController extends CrudController
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
        CRUD::setModel(AfTemplate::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/af-templates');
        CRUD::setEntityNameStrings('Template', 'Templates');
    }

    public function fetchTemplate()
    {
        return $this->fetch(AfTemplate::class);
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

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(AfTemplatesRequest::class);
        $this->crud->setEditView('backpack.views.template-create-form');
        $this->crud->setCreateView('backpack.views.template-create-form');


        CRUD::field('name');
        $this->crud->field('enabled')->type('checkbox')->label('Enabled');
        CRUD::field('description')->type('textarea');

        $this->crud->addField(
            [
                'name' => 'id_category',
                'label' => 'Category',
                'type' => 'select_from_array',
                'options' => AfCategory::all()->pluck('name', 'id')->toArray()
            ]
        );

        $fields = ['notification','email','digest','admin'];

        foreach($fields as $field){
            CRUD::field('notification_'.$field)->label(ucfirst($field) .' subject');
            CRUD::field($field.'_template')->type('textarea')->label(ucfirst($field) .' text content');
        }

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
