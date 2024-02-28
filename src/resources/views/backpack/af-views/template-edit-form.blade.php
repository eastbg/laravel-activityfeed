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

    .af-error {
        margin-top: 10px;
        margin-bottom: 10px;
        border-color: #C32215;
        border-width: 2px;
        border-radius: 4px;
        padding: 10px;
    }

    .af-error b {
        color: #C32215;
    }


    textarea.form-control {
        min-height: 120px !important;
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
      trans('backpack::crud.admin') => backpack_url('dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.edit') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.edit').' '.$crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
                                class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }}
                        <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection



@section('content')
    <!-- Default box -->
    <div class="row">

        <div class="col-7">

            @if(isset($error_message) AND $error_message)
                <div class="af-error">
                    <b>IMPORTANT: please check your template! While trying to create a notification, <br>
                        the following error was encountered:</b><br>
                    {{$error_message}}
                </div>
            @endif


            @include('crud::inc.grouped_errors')

            <form method="post"
                  action="{{ url($crud->route.'/'.$entry->getKey()) }}"
                  @if ($crud->hasUploadFields('update', $entry->getKey()))
                      enctype="multipart/form-data"
                    @endif
            >
                {!! csrf_field() !!}
                {!! method_field('PUT') !!}

                @if ($crud->model->translationEnabled())
                    <div class="mb-2 text-right">
                        <!-- Single button -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                {{trans('backpack::crud.language')}}
                                : {{ $crud->model->getAvailableLocales()[request()->input('_locale')?request()->input('_locale'):App::getLocale()] }}
                                &nbsp; <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($crud->model->getAvailableLocales() as $key => $locale)
                                    <a class="dropdown-item"
                                       href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}?_locale={{ $key }}">{{ $locale }}</a>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <!-- load the view from the application if it exists, otherwise load the one in the package -->
                @if(view()->exists('vendor.backpack.crud.form_content'))
                    @include('vendor.backpack.crud.form_content', ['fields' => $crud->fields(), 'action' => 'edit'])
                @else
                    @include('crud::form_content', ['fields' => $crud->fields(), 'action' => 'edit'])
                @endif
                <!-- This makes sure that all field assets are loaded. -->
                <!--<div class="d-none" id="parentLoadedAssets">{>{ json_encode(Assets::loaded()) }}</div>-->
                @include('crud::inc.form_save_buttons')
            </form>
        </div>
        <div class="col-5">
            @include('af_feed::backpack.af-views.template-info')
        </div>
    </div>

@endsection
