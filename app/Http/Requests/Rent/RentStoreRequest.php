<?php

namespace App\Http\Requests\Rent;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

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
            'return_date' => 'required|date|after_or_equal:rent_date',
            'rent_type' => 'required|in:Monthly,Daily',
        ];
    }

    // Override the failedValidation method
    protected function failedValidation(Validator $validator)
    {
        $response = redirect()
            ->route('rents.create')
            ->withErrors($validator)
            ->withInput();

        throw new ValidationException($validator, $response);
    }
}
