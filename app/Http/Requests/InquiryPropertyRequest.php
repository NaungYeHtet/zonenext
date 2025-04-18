<?php

namespace App\Http\Requests;

use App\Enums\Lead\LeadInterest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class InquiryPropertyRequest extends FormRequest
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
            'code' => ['required', 'string', 'exists:properties,code'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required_without:email', 'string', 'max:255'],
            'email' => ['string', 'max:255'],
            'message' => ['string', 'min:0', 'max:5000'],
        ];
    }
}
