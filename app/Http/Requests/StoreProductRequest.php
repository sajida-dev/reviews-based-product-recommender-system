<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Product::class);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],

            'name' => ['required', 'string', 'max:255'],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'slug'),
            ],

            'description' => ['nullable', 'string'],

            'price' => ['required', 'numeric', 'min:0'],

            'discount_price' => [
                'nullable',
                'numeric',
                'min:0',
                'lt:price',
            ],

            'stock' => ['required', 'integer', 'min:0'],

            'attributes' => ['nullable', 'array'],

            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'primary_image' => ['required', 'integer', 'min:0'],

        ];
    }

    public function messages(): array
    {
        return [
            'discount_price.lt' => 'Discount price must be less than regular price.',
        ];
    }
}
