<?php

namespace App\Helper;

use Carbon\Carbon;

class ImageHelper
{
    public static function personImage($base64Data, $FileName, $isUpdate = false, $OldFileName = '')
    {
        if ($base64Data == '' || $base64Data == null) {
            if ($isUpdate && ($OldFileName != '' && $OldFileName != null)) {
                self::DeleteImage($OldFileName);
            }

            return '';
        } elseif (strpos($base64Data, 'base64') !== false) {
            $pos = strpos($base64Data, ';');
            $type = explode(':', substr($base64Data, 0, $pos))[1];
            $_filename = $FileName.$OldFileName;
            if ($isUpdate) {
                self::DeleteImage($OldFileName);
            }

            // Fixed using storage_path() with a clean relative subdirectory parameter
            $destinationPath = storage_path('app/public/Upload/personimage/');
            $path = $destinationPath.$_filename;

            $data = explode(',', $base64Data);
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            file_put_contents($path, base64_decode($data[1]));

            return 'Upload/personimage/'.$_filename; // Matches the actual subfolder path
        } else {
            return $OldFileName;
        }
    }

    public static function AttendanceImage($base64Data, $FileName, $isUpdate = false, $OldFileName = '')
    {
        if (empty($base64Data)) {

            if ($isUpdate && ! empty($OldFileName)) {
                self::DeleteImage($OldFileName);
            }

            return '';

        } elseif (strpos($base64Data, 'base64') !== false) {

            $pos = strpos($base64Data, ';');
            $type = explode(':', substr($base64Data, 0, $pos))[1];
            $extension = explode('/', $type)[1];

            if (! str_contains($FileName, '.')) {
                $_filename = $FileName.'.'.$extension;
            } else {
                $_filename = $FileName;
            }

            if ($isUpdate && ! empty($OldFileName)) {
                self::DeleteImage($OldFileName);
            }

            $data = explode(',', $base64Data);

            $baseDir = storage_path('app/public/Upload/attendance/');

            if (! file_exists($baseDir)) {
                mkdir($baseDir, 0755, true);
            }

            $currentDate = Carbon::now()->toDateString();

            $dateDir = $baseDir.$currentDate.'/';

            if (! file_exists($dateDir)) {
                mkdir($dateDir, 0755, true);
            }

            $path = $dateDir.$_filename;

            file_put_contents($path, base64_decode($data[1]));

            return 'Upload/attendance/'.$currentDate.'/'.$_filename;

        }

        return $OldFileName;
    }

    public static function SavePdf($base64Data, $FileName, $isUpdate = false, $OldFileName = '')
    {
        if ($base64Data == '' || $base64Data == null) {
            if ($isUpdate && ($OldFileName != '' && $OldFileName != null)) {
                self::DeleteImage($OldFileName);
            }

            return '';
        } elseif (strpos($base64Data, 'base64') !== false) {
            $pos = strpos($base64Data, ';');
            $type = explode(':', substr($base64Data, 0, $pos))[1];
            $_filename = $FileName.'_'.$OldFileName;
            if ($isUpdate) {
                self::DeleteImage($OldFileName);
            }
            $path = storage_path().'//attachment//'.$_filename;
            $data = explode(',', $base64Data);
            if (! file_exists(storage_path().'//attachment//')) {
                mkdir(storage_path().'//attachment//');
            }
            file_put_contents($path, base64_decode($data[1]));

            return $_filename;
        } else {
            return $OldFileName;
        }
    }

    public static function DeleteImage($filePath)
    {
        if (empty($filePath)) {
            return false;
        }

        // Normalize any backslashes to forward slashes for Linux compatibility
        $filePath = str_replace('\\', '/', $filePath);

        // If the path is a full URL, strip the domain/storage prefix to get the relative path
        if (strpos($filePath, 'http') !== false) {
            // Example: http://127.0.0.1:8000/storage/Upload/personimage/file.jpeg
            $parsedUrl = parse_url($filePath, PHP_URL_PATH);
            $filePath = str_replace('/storage/', '', $parsedUrl);
        }

        // Build the absolute path safely using forward slashes
        $fullPath = storage_path('app/public/'.ltrim($filePath, '/'));

        // Fallback if your Upload folder is directly inside storage/ instead of storage/app/public/
        if (! file_exists($fullPath)) {
            $fullPath = storage_path(ltrim($filePath, '/'));
        }

        if (file_exists($fullPath)) {
            unlink($fullPath);

            return true;
        }

        return false;
    }
}
