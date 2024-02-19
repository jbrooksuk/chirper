<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChirpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:255'],
        ];
    }

    public function bodyParameters()
    {
        return [
            'message' => [
                'description' => 'The message of the Chirp',
                'example' => 'Hello, PHP UK!',
            ],
        ];
    }
}
