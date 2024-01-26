<?php

namespace App\Http\Requests;

class RegistrationRequest extends ApiFormRequest
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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|regex:(^.*(?=.{3,})(?=.*[a-z])(?=.*[A-Z])(?=.*\d).*$)|min:3',
            'first_name' => 'required',
            'last_name' => 'required'
        ];
    }
}
