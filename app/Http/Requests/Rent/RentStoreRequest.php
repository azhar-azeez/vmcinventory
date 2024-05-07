<?php

namespace App\Http\Requests\Rent;

use Illuminate\Support\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Http\FormRequest;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rules\Enum;

class RentStoreRequest extends FormRequest
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
            'rent_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:rent_date'
        ];
    }

}
