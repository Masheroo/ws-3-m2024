<?php

namespace App\Http\Service\Helper;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function Symfony\Component\String\b;

class FileHelper
{
    public static function getClientFileNameWithoutExtension(UploadedFile $uploadedFile): string
    {
        return pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
    }

    public static function getUniqueClientFilenameFromUploadedFile(UploadedFile $uploadedFile): string
    {
        $filename = self::getClientFileNameWithoutExtension($uploadedFile);
        if (!self::checkFilenameUnique($filename . '.' . $uploadedFile->getClientOriginalExtension())) {
            for ($i = 1; $i > -1; $i++) {
                $newFilename = $filename . '(' . $i . ')';
                if (self::checkFilenameUnique($newFilename. '.' . $uploadedFile->getClientOriginalExtension())){
                    $filename = $newFilename;
                    break;
                }
            }
        }
        return $filename . '.' . $uploadedFile->getClientOriginalExtension();
    }

    public static function checkFilenameUnique(string $filename): bool
    {
        $validator = Validator::make(['filename' => $filename], [
            'filename' => 'unique:files,filename'
        ]);

        return !$validator->fails();
    }
}
