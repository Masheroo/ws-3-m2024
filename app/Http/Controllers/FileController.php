<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFilesRequest;
use App\Http\Requests\DeleteFileRequest;
use App\Http\Requests\RenameFileRequest;
use App\Http\Service\Helper\FileHelper;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
}
