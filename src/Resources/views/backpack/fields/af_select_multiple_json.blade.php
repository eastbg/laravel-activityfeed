<!-- select2 multiple -->
@php
    //build option keys array to use with Select All in javascript.

    $field['multiple'] = true;
    $field['placeholder'] = $field['placeholder'] ?? trans('backpack::crud.select_entries');
    $field['value'] = old_empty_or_null($field['name'], collect()) ??  $field['value'] ?? $field['default'] ?? collect();

    if(stristr($field['value'],'["')){
        $field['value'] = json_decode($field['value'],true);
    }


    if (isset($field['value']) AND isset($field['model']) AND is_a($field['value'], \Illuminate\Support\Collection::class)) {
        $field['value'] = $field['value']->pluck(app($field['model'])->getKeyName())->toArray();
    }

    if(!$field['value']){
        $field['value'] = [];
    }

@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! $field['label'] !!}</label>
@include('crud::fields.inc.translatable_icon')
{{-- To make sure a value gets submitted even if the "select multiple" is empty, we need a hidden input --}}
<input type="hidden" name="{{ $field['name'] }}" value=""
       @if(in_array('disabled', $field['attributes'] ?? [])) disabled @endif />
<select
        @if(isset($field['onchange']))
            onchange="{{$field['onchange']}}('{{ $field['name'] }}');"
        id="{{ $field['name'] }}"
        @endif
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        data-init-function="bpFieldInitSelect2MultipleElement"
        data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
        data-select-all="{{ var_export($field['select_all'] ?? false)}}"
        data-options-for-js="{{json_encode(array_values($field['options']))}}"
        data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
        data-allows-null="true"
        data-placeholder="{{$field['placeholder']}}"
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_multiple'])
        multiple>


    @foreach ($field['options'] as $option)
        @if(is_array($field['value']) AND in_array($option, $field['value']))
            <option value="{{ $option }}" selected>{{ $option }}</option>
        @else
            <option value="{{ $option }}">{{ $option }}</option>
        @endif
    @endforeach
</select>

@if(isset($field['select_all']) && $field['select_all'])
    {{--
        <a class="btn btn-xs btn-default select_all" style="margin-top: 5px;"><i
                class="la la-check-square-o"></i> {{ trans('backpack::crud.select_all') }}</a>
    --}}
    {{--
        <a class="btn btn-xs btn-default clear" style="margin-top: 5px;"><i
                class="la la-times"></i> {{ trans('backpack::crud.clear') }}</a>
    --}}
@endif

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}

{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
    <!-- include select2 css-->
    @loadOnce('packages/select2/dist/css/select2.min.css')
    @loadOnce('packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css')
@endpush

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    <!-- include select2 js-->
    @loadOnce('packages/select2/dist/js/select2.full.min.js')
    @if (app()->getLocale() !== 'en')
        @loadOnce('packages/select2/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js')
    @endif
    @loadOnce('bpFieldInitSelect2MultipleElement')
    <script>
        function bpFieldInitSelect2MultipleElement(element) {

            var $select_all = element.attr('data-select-all');
            if (!element.hasClass("select2-hidden-accessible")) {
                let $isFieldInline = element.data('field-is-inline');
                let $allowClear = element.data('allows-null');
                let $multiple = element.attr('multiple') ?? false;
                let $placeholder = element.attr('placeholder');

                var $obj = element.select2({
                    theme: "bootstrap",
                    allowClear: $allowClear,
                    multiple: $multiple,
                    placeholder: $placeholder,
                    dropdownParent: $isFieldInline ? $('#inline-create-dialog .modal-content') : document.body
                });

                //get options ids stored in the field.
                var options = JSON.parse(element.attr('data-options-for-js'));

                if ($select_all) {
                    element.parent().find('.clear').on("click", function () {
                        $obj.val([]).trigger("change");
                    });
                    element.parent().find('.select_all').on("click", function () {
                        $obj.val(options).trigger("change");
                    });
                }
            }
        }
    </script>
    @endLoadOnce
@endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
