<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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

    // Override the failedValidation method
    protected function failedValidation(Validator $validator)
    {
        $response = redirect()
            ->route('orders.create')
            ->withErrors($validator)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
