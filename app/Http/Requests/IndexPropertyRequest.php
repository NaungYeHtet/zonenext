<?php

namespace App\Http\Requests;

use App\Enums\Filters\FilterListType;
use App\Enums\PropertyType;
use App\Models\Township;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator;

class IndexPropertyRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

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
            'search' => ['string'],
            'list_type' => ['required', 'string', new Enum(FilterListType::class)],
            'state' => ['string', 'exists:states,slug'],
            'township' => ['string', 'exists:townships,slug'],
            'type' => ['string', new Enum(PropertyType::class)],
            'price_from' => ['integer'],
            'price_to' => ['integer', function ($attribute, $value, $fail) {
                if ($this->price_from && $value < $this->price_from) {
                    $fail(__('validation.gt.numeric', ['attribute' => __('Price to'), 'value' => $this->price_from]));
                }
            }],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $state = $validator->safe()->state;
                $township = $validator->safe()->township;
                if ($state && $township && Township::findBySlug($township)->state->slug != $state) {
                    $validator->errors()->add(
                        'township',
                        __('validation.exists', [
                            'attribute' => 'township',
                        ])
                    );
                }
            },
        ];
    }
}
