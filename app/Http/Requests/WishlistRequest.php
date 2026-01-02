<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only authenticated users can modify wishlists
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product selection is required.',
            'product_id.exists'   => 'Selected product does not exist.',
        ];
    }
}
