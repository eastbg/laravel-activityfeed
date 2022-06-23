
<script type="text/javascript">

    window.addEventListener('load', function () {
        targetingHide();
    })

    const targetingHide = function(){
        document.getElementById('targeting1').style.display = "none";
        document.getElementById('targeting2').style.display = "none";
        document.getElementById('targeting3').style.display = "none";
        document.getElementById('targeting4').style.display = "none";
        document.getElementById('targeting5').style.display = "none";
        document.getElementById('targeting_visible').style.display = "none";
        document.getElementById('targeting_hidden').style.display = "inline";
    }

    const targetingShow = function(){
        document.getElementById('targeting1').style.display = "block";
        document.getElementById('targeting2').style.display = "block";
        document.getElementById('targeting3').style.display = "block";
        document.getElementById('targeting4').style.display = "block";
        document.getElementById('targeting5').style.display = "block";
        document.getElementById('targeting_visible').style.display = "inline";
        document.getElementById('targeting_hidden').style.display = "none";
    }

    const fieldWirePreview = function ($field) {
        let key = $field;
        let params = {};
        params[key] = document.getElementById(key).value;
        Livewire.emit('updatePreview', params);
    }

    const fieldWireRecipients = function ($field) {
        let key = $field;
        let params = {};
        params[key] = document.getElementById(key).value;
        Livewire.emit('updateRecipients', params);
    }

    const fieldWireRecipients2 = function ($field) {
        let key = $field;
        let params = {};
        params[key] = $('#' + key).select2('data');
        Livewire.emit('updateRecipients', params);
    }

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
        height:250px!important;
    }

    small {
        display: none!important;
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
    <br>
    @include('backpack.crud.inc.custom_tabbing')

    <div class="row">

        <div class="col-7">

            {{--
                        <div class="{{ $crud->getCreateContentClass() }}">
            --}}
            <!-- Default box -->

            @include('crud::inc.grouped_errors')

            <form method="post"
                  name="emailForm"
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
            {{--
                       </div>
            --}}
        </div>
        <div class="col-5">
            Templates are in Laravel Blade format. They are fed with var replacement and data replacement. Idea is that you can dump data from your database record and it's relations directly to the template. So you could define it like this:
            <br><div class="af-code">
            @php echo(htmlspecialchars( 'You have a new notification, click <a href="{{$url ?? \'\'}}">here</a> to read it.')); @endphp
            </div>

            So also this would work:
            <br><div class="af-code">
                @php echo( htmlspecialchars('@if(isset($username) AND $username)) Hello {{$username}}! @endif<br>
            You have a new notification, click <a href="{{$url ?? \'\'}}">here</a> to read it.')); @endphp
            </div>

            And this (provided the correct relations exist):
            <br><div class="af-code">
            @php echo(htmlspecialchars('@if(isset($user->profile) AND $user->profile)) Hello {{$user->profile->name}}! @endif')); @endphp <br>
            You have a new notification, click <a href="{{$url ?? ''}}">here</a> to read it.
            </div>
            The variable replacement happens at save time and is "blind" so you should adjust your templates accordingly.
            <br><br>
            Rules define targeting and channels.
            <br><br>

            <a href="https://laravel.com/docs/9.x/blade">Blade Syntax</a>

        </div>
    </div>

@endsection

