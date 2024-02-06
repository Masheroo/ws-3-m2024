<?php

namespace App\Http\Service\Helper;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHelper
{
    public static function getClientFileNameWithoutExtension(UploadedFile $uploadedFile): string
    {
        return pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    }

    public static function getUniqueClientFilenameFromUploadedFile(UploadedFile $uploadedFile, User $user): string
    {
        $filename = self::getClientFileNameWithoutExtension($uploadedFile);
        if (!self::checkFilenameUnique($filename . '.' . $uploadedFile->getClientOriginalExtension(), $user)) {
            for ($i = 1; $i > -1; $i++) {
                $newFilename = $filename . '(' . $i . ')';
                if (self::checkFilenameUnique($newFilename. '.' . $uploadedFile->getClientOriginalExtension(), $user)){
                    $filename = $newFilename;
                    break;
                }
            }
        }
        return $filename . '.' . $uploadedFile->getClientOriginalExtension();
    }

    public static function checkFilenameUnique(string $filename, User $user): bool
    {
        return !Storage::exists('/' . $user->id . '/'.$filename);
    }
}
