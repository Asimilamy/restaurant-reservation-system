<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditReservationRequest extends FormRequest
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
            'order_id' => 'nullable',
            'name' => 'required|string|min:2|max:255',
            'start_at' => 'required|date_format:Y-m-d H:i:s',
            'end_at' => 'nullable'
        ];
    }
}
