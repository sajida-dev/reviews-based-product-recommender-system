<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating'     => ['required', 'integer', 'between:1,5'],
            'review'     => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.between' => 'Rating must be between 1 and 5 stars.',
            'product_id.exists' => 'Selected product does not exist.',
        ];
    }
}
