<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreElectionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['string'],
            'credits' => ['required', 'numeric'],
            'motions' => ['required', 'array'],
            'motions.*.content' => ['required', 'string'],
            'motions.*.votes' => ['required', 'numeric'],
            'motions.*.uuid' => ['required', 'string'],
            'motions.*.credits' => ['required', 'numeric'],
            // 'options' => ['required', 'array'],

        ];
    }
}
