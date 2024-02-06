<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccessRightsRequest;
use App\Http\Requests\AddFilesRequest;
use App\Http\Requests\DeleteFileRequest;
use App\Http\Requests\GetFileRequest;
use App\Http\Requests\RenameFileRequest;
use App\Http\Service\Helper\FileHelper;
use App\Models\AccessRight;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function addFiles(AddFilesRequest $request): JsonResponse
    {
        /** @var UploadedFile[] $files */
        $files = $request->files->get('files');
        /** @var User $user */
        $user = auth()->user();
        $response = [];
        foreach ($files as $uploadedFile) {
            $file = File::new();
            $file->user_id = $user->id;
            $file->filename = FileHelper::getUniqueClientFilenameFromUploadedFile($uploadedFile);
            if (Storage::put($file->filename, $uploadedFile->getContent())) {
                $response[] = [
                    'success' => true,
                    'message' => 'Success',
                    'name' => $file->filename,
                    'url' => env('APP_URL') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $file->id,
                    'file_id' => $file->id
                ];
                $file->save();
            } else {
                $response[] = [
                    'success' => false,
                    'message' => 'File not loaded',
                    'name' => $uploadedFile->getClientOriginalName()
                ];
            }
        }
        return response()->json($response);
    }

    public function renameFile(RenameFileRequest $request): JsonResponse
    {
        /** @var File $file */
        $file = File::where(['id' => $request->route('id')])->first();

        Storage::move($file->filename, $request->name);

        $file->filename = $request->name;
        $file->save();

        return response()->json([
            'success' => true,
            'message' => 'Renamed'
        ]);
    }

    public function deleteFile(DeleteFileRequest $request): JsonResponse
    {
        /** @var File $file */
        $file = File::where(['id' => $request->route('id')])->first();

        Storage::delete($file->filename);

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File already deleted'
        ]);
    }

    public function getFile(GetFileRequest $request): StreamedResponse
    {
        $file = File::where(['id' => $request->route('id')])->first();

        return Storage::download($file->filename);
    }

    public function addAccessRights(AccessRightsRequest $request): JsonResponse
    {
        $accessRight = new AccessRight();
        $accessRight->file_id = $request->route('id');

        $user = User::where(['email' => $request->email])->first();
        $accessRight->user_id = $user->id;

        $accessRight->save();

        $allAccessRightToFile = AccessRight::where(['file_id' => $request->route('id')])->get();
        $response = [];

        foreach ($allAccessRightToFile as $accessRight) {
            $user = User::find($accessRight->user_id);

            $response[] = [
                'fullname' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'type' => AccessRight::RIGHT_NAME
            ];
        }
        /** @var User $user */
        $user = auth()->user();

        $response[] = [
            'fullname' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'type' => 'author'
        ];

        return response()->json($response);
    }

    public function deleteAccessRights(AccessRightsRequest $request): JsonResponse
    {
        $userForDeleteRights = User::where(['email' => $request->email])->first();
        AccessRight::where(['user_id' => $userForDeleteRights->id, 'file_id' => $request->route('id')])->delete();

        $allAccessRightToFile = AccessRight::where(['file_id' => $request->route('id')])->get();
        $response = [];

        foreach ($allAccessRightToFile as $accessRight) {
            $user = User::find($accessRight->user_id);

            $response[] = [
                'fullname' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'type' => AccessRight::RIGHT_NAME
            ];
        }
        /** @var User $user */
        $user = auth()->user();

        $response[] = [
            'fullname' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'type' => 'author'
        ];

        return response()->json($response);
    }

    public function getUserFiles(): JsonResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $files = File::where(['user_id' => $user->id])->get();

        $response = [];

        foreach ($files as $i => $file) {
            $response[$i] = [
                'file_id' => $file->id,
                'name' => $file->name,
                'url' => env('APP_URL') . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $file->id,
                'accesses' => [
                ]
            ];

            $accesses = AccessRight::where(['file_id' => $file->id])->get();
            foreach ($accesses as $access){
                $accessRightUser = User::find($access->user_id);

                $response[$i]['accesses'][] = [
                    'fullname' => $accessRightUser->first_name . ' ' . $accessRightUser->last_name,
                    'email' => $accessRightUser->email,
                    'type' => AccessRight::RIGHT_NAME
                ];
            }
            $response[$i]['accesses'][] = [
                'fullname' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'type' => 'author'
            ];
        }

        return response()->json($response);
    }
}
