<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['title'] = 'sometimes|string|max:255';
            $rules['body'] = 'sometimes|string';
        }

        return $rules;
    }
}
