<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city_id' => ['required', 'exists:cities,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cmi,stripe,paypal,cod'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'Le nom complet est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'phone.required' => 'Le numéro de téléphone est requis.',
            'address.required' => 'L\'adresse de livraison est requise.',
            'city_id.required' => 'Merci de sélectionner une ville.',
            'payment_method.required' => 'Merci de choisir un mode de paiement.',
        ];
    }
}
