<?php

namespace East\LaravelActivityfeed\Http\Backpack;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategoriesModel;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvent;
use East\LaravelActivityfeed\Models\ActiveModels\AfEvents;
use East\LaravelActivityfeed\Models\ActiveModels\AfEventsModel;
use East\LaravelActivityfeed\Models\ActiveModels\AfRulesModel;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplatesModel;
use East\LaravelActivityfeed\Requests\AfCategoriesRequest;
use East\LaravelActivityfeed\Requests\AfEventsRequest;
use East\LaravelActivityfeed\Requests\AfRulesRequest;
use East\LaravelActivityfeed\Requests\AfTemplatesRequest;

/**
 * Class AfRulesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AfEventsCrudController extends CrudController
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
        CRUD::setModel(AfEvent::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/af-events');
        CRUD::setEntityNameStrings('Event', 'Events');

        $this->crud->denyAccess(['create','edit']);
        $this->crud->removeButton('update');
        $this->crud->removeButton('create');
        $this->crud->removeButton('add');
        $this->crud->removeButton('edit');


    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->removeButton('update');
        $this->crud->removeButton('delete');

        CRUD::column('created_at');

        $this->crud->addColumn(
            [
                // any type of relationship
                'label' => 'Creator',
                'type' => 'relationship',
                'name' => 'creator', // name of relationship method in the model
                'attribute' => 'email', // foreign key attribute that is shown to user
            ]
            );

        $this->crud->addColumn(
            [
                // any type of relationship
                'label' => 'Rule',
                'type' => 'relationship',
                'name' => 'afRule', // name of relationship method in the model
                'attribute' => 'name', // foreign key attribute that is shown to user
            ]
            );


/*        CRUD::column('digestible');
        CRUD::column('digested');*/
        CRUD::column('processed');
        CRUD::column('dbtable');
        CRUD::column('operation');
        CRUD::column('dbkey');

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
        CRUD::setValidation(AfEventsRequest::class);

        CRUD::field('id');
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
        CRUD::field('enabled');

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
