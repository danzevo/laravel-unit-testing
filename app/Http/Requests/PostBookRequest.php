<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    public function prepareForValidation() {
        if($this->isbn && is_array($this->isbn))
            $this->isbn = '';

        $this->merge([
            'isbn' => $this->isbn,
        ]);
    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // @TODO implement
        return [
            'isbn' => ['required', 'string', 'unique:books,isbn', 'digits:13'],
            'title' => 'required|string',
            'description' => 'required|string',
            'authors' => 'required|array',
            'authors.*' => 'integer|exists:authors,id',
            'published_year' => 'required|integer|between:1900,2020',
        ];
    }
}
