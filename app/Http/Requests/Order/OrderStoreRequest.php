<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Illuminate\Support\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Http\FormRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rules\Enum;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required',
            'payment_type' => 'required',
            'pay' => 'required|numeric',
            //'discount' => 'numeric|min:0|max:100'
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = redirect()->route('orders.create');

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
