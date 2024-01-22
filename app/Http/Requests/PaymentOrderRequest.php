<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class PaymentOrderRequest extends FormRequest
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
            'order_id' => 'required|integer',
            'payment' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $order = Order::find(request('order_id'));
                    if ($value < $order->total) {
                        $fail(ucfirst($attribute) . ': \'' . $value . '\' is less than Total: \'' . $order->total . '\'');
                    }
                    if ($order->payment > 0 && $order->payment == $order->total) {
                        $fail('Order already has payment!');
                    }
                }
            ]
        ];
    }
}
