<script type="text/javascript">

</script>

<style>
    .recipient {
        background-color: #E3E3E3;
        border-radius: 8px;
        margin-right: 8px;
        padding: 2px;
        padding-left: 7px;
        margin-bottom: 6px;
        display: inline-block;
    }

    span.text-capitalize {
        font-size: 32px;
    }

    textarea.form-control {
        height: 120px !important;
    }

    small {
        display: none !important;
    }

    .af-code {
        padding: 15px;
        background-color: #DCE2E5;
        font-family: "Courier New", Courier, monospace;
        margin-bottom: 20px;
        font-size: 15px;
    }

</style>

@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add').' '.$crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
                                class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }}
                        <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')

    <div class="row">
        <div class="col-7">

            <!-- Default box -->

            @include('crud::inc.grouped_errors')

            <form method="post"
                  action="{{ url($crud->route) }}"
                  @if ($crud->hasUploadFields('create'))
                      enctype="multipart/form-data"
                    @endif
            >
                {!! csrf_field() !!}
                <!-- load the view from the application if it exists, otherwise load the one in the package -->
                @if(view()->exists('vendor.backpack.crud.form_content'))
                    @include('vendor.backpack.crud.form_content', [ 'fields' => $crud->fields(), 'action' => 'create' ])
                @else
                    @include('crud::form_content', [ 'fields' => $crud->fields(), 'action' => 'create' ])
                @endif
                <!-- This makes sure that all field assets are loaded. -->
                <div class="d-none" id="parentLoadedAssets">{{ json_encode(Assets::loaded()) }}</div>
                @include('crud::inc.form_save_buttons')
            </form>
        </div>
        <div class="col-5">
            @include('af_feed::backpack.af-views.template-info')
        </div>
    </div>
    </div>

@endsection

