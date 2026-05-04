<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $review = $this->input('review');

        if (is_string($review) && trim($review) === '') {
            $this->merge(['review' => null]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => [
                'nullable',
                'string',
                'max:2000',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! is_string($value)) {
                        return;
                    }

                    $text = trim($value);
                    if ($text === '') {
                        return;
                    }

                    if (mb_strlen($text) < 5) {
                        $fail('Review text must be at least 5 characters.');
                    }

                    if (preg_match('/https?:\/\//i', $text)) {
                        $fail('Links are not allowed in review text.');
                    }

                    if (preg_match('/<[^>]*>/', $text)) {
                        $fail('HTML tags are not allowed in review text.');
                    }
                },
            ],
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
