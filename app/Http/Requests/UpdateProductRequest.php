<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:categories,id'],

            'name' => ['sometimes', 'string', 'max:255'],

            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('products', 'slug')->ignore(
                    $this->route('product')
                ),
            ],

            'description' => ['nullable', 'string'],

            'price' => ['sometimes', 'numeric', 'min:0'],

            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lt:price',
            ],

            'stock' => ['sometimes', 'integer', 'min:0'],

            'attributes' => ['nullable', 'array'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'primary_image' => ['nullable', 'integer', 'min:0'],

        ];
    }
}
