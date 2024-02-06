<?php

namespace App\Http\Requests;

class AddFilesRequest extends ApiFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'files.*' => 'required|file|mimes:doc,pdf,docx,zip,jpeg,png|max:2048',
            'files' => [
                'required',
                'array'
            ]
        ];
    }
}
