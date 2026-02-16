<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'payment_method' => ['required', 'string', 'in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery'],
        ];

        // Credit/Debit card specific validation
        if (in_array($this->payment_method, ['credit_card', 'debit_card'])) {
            $rules['card_number'] = ['required', 'string', 'regex:/^[0-9]{13,19}$/'];
            $rules['card_holder_name'] = ['required', 'string', 'max:255'];
            $rules['expiry_month'] = ['required', 'integer', 'min:1', 'max:12'];
            $rules['expiry_year'] = ['required', 'integer', 'min:' . date('Y')];
            $rules['cvv'] = ['required', 'string', 'regex:/^[0-9]{3,4}$/'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'order_id.required' => 'Order ID is required.',
            'order_id.exists' => 'Order not found.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'card_number.required' => 'Card number is required.',
            'card_number.regex' => 'Invalid card number format.',
            'card_holder_name.required' => 'Card holder name is required.',
            'expiry_month.required' => 'Expiry month is required.',
            'expiry_year.required' => 'Expiry year is required.',
            'cvv.required' => 'CVV is required.',
            'cvv.regex' => 'Invalid CVV format.',
        ];
    }
}
