<?php

namespace East\LaravelActivityfeed\Http\Backpack;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\Pro\Http\Controllers\Operations\CloneOperation;
use East\LaravelActivityfeed\Models\ActiveModels\AfCategory;
use East\LaravelActivityfeed\Models\ActiveModels\AfTemplate;
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

    /*    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;*/

    use CloneOperation;

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
        CRUD::column('enabled')->type('check');
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
        //CRUD::setValidation(AfTemplatesRequest::class);
        $this->crud->setCreateView('af_feed::backpack.views.template-create-form');

        CRUD::field('name');
        CRUD::field('description')->type('textarea');

        $this->crud->field('enabled')->type('checkbox')->label('Enabled');

        $this->crud->addField(
            [
                'name' => 'master_template',
                'label' => 'Master template',
                'type' => 'af_checkbox',
                'hint' => 'Other templates can include master template as a base',
                'onclick' => [
                    [
                        'function' => 'afMasterTemplateToggle',
                    ],
                ]
            ]
        );


        $this->crud->addField(
            [
                'name' => 'id_parent',
                'label' => 'Parent template (optional)',
                'hint' => 'If you want to "decorate" this template, you can use a parent template to do it.',
                'type' => 'select_from_array',
                'options' => AfTemplate::where('master_template', '=', 1)->pluck('name', 'id')->toArray(),
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_parent_template'
                ],
            ]
        );


        $this->crud->addField(
            [
                'name' => 'id_category',
                'label' => 'Category',
                'type' => 'select_from_array',
                'options' => AfCategory::all()->pluck('name', 'id')->toArray()
            ]
        );


        $this->crud->addField(
            [
                'name' => 'url_template',
                'label' => 'URL format',
                'hint' => 'Define the format for URL that is accessible with {{$url}}. Mark ID location with {{$id}}. This is not always needed if you write the url directly into the template.',
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_url_format'
                ],
            ]
        );

        CRUD::field('slug')->label('Slug')->hint('This is used to identify the template, needs to be unique.');

        CRUD::field('notification_template')->type('textarea')->label('Notification template');
        CRUD::field('admin_template')->type('textarea')->label('Notification template for admins');
        CRUD::field('digest_template')->type('textarea')->label('Notification template for digest');


        $this->crud->addField(
            [
                'name' => 'email_subject',
                'label' => 'Email subject',
                'type' => 'text',
                'wrapper' => [
                    'class' => 'form-group col-md-12',
                    'id' => 'w_email_subject'
                ],
            ]
        );

        CRUD::field('email_template')->type('textarea')->label('Email template');

    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->crud->setEditView('af_feed::backpack.views.template-edit-form');
        $this->setupCreateOperation();
    }
}
