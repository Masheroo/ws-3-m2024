<?php

namespace App\Http\Requests;

use App\Models\AccessRight;
use App\Models\File;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RenameFileRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        $file = File::where(['id' => $this->route('id')])->first() ?? throw new NotFoundHttpException();

        /** @var User $user */
        $user = $this->user();

        if ($user->id == $file->user_id) {
            return true;
        } else {
            $accessRight = AccessRight::where(['file_id' => $file->id, 'user_id' => $user->id])->first();
            if ($accessRight){
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:files,filename'
        ];

    }
}
