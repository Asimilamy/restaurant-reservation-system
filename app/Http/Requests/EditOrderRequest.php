<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'table_id' => 'required|integer',
            'total' => 'nullable',
            'payment' => 'nullable',
            'details.*.menu_id' => 'required|integer',
            'details.*.qty' => 'required|numeric',
        ];
    }
}
