<?php

namespace East\LaravelActivityfeed\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AfTemplatesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }


    /**
     * Get the validation rules that apply to the request.
     * todo: make sure that master templates have variable {!! $content !!} set
     *
     * @return array
     */
    public function rules()
    {
        return [
             'name' => 'required|min:5|max:255',
             'slug' => 'unique:af_templates|min:4|max:255',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
