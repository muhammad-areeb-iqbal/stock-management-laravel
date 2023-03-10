<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreOrderRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'products.*.product_id' => 'required|numeric|min:1',
            'products.*.quantity' => 'required|numeric|min:1',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 1,
            'data' => [
                "headers" => [],
                "original" => [
                    "error" => $validator->errors(),
                ],
                "exception" => null
            ],
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
