<?php

namespace App\Http\Requests;

use App\Enums\Lead\LeadContactMethod;
use App\Enums\Lead\LeadContactTime;
use App\Enums\Lead\LeadInterest;
use App\Enums\PropertyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class InquiryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'interest' => ['required', new Enum(LeadInterest::class)],
            'property_type' => ['required', new Enum(PropertyType::class)],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'is_owner' => ['required_if:interest,Renting', 'boolean'],
            'township' => ['string', 'exists:townships,slug'],
            'address' => ['string', 'max:255'],
            'phone' => ['required_without:email', 'string', 'max:255'],
            'email' => ['string', 'max:255'],
            'preferred_contact_method' => ['string', new Enum(LeadContactMethod::class)],
            'preferred_contact_time' => ['string', new Enum(LeadContactTime::class)],
            'send_updates' => ['required', 'boolean'],
            'max_price' => ['integer', 'min:100', 'max:4000000000'],
            'square_feet' => ['integer', 'min:50', 'max:16777215'],
            'bedrooms' => ['integer', 'min:0', 'max:255'],
            'bathrooms' => ['integer', 'min:0', 'max:255'],
            'note' => ['string', 'min:0', 'max:5000'],
        ];
    }
}
