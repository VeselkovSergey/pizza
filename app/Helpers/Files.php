<?php


namespace App\Helpers;

use Illuminate\Http\FileHelpers;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use App\Models\Files as FilesDB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\Exception;

class Files
{
    public static function SaveFile(UploadedFile $file, string $path = 'files', string $disk = 'local')
    {
        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->extension();
        $type = $file->getMimeType();
        $hashFileName = pathinfo($file->hashName(), PATHINFO_FILENAME);
        $hashFileNameWithExtension = $hashFileName . '.' . $extension;
        $file->storeAs($path, $hashFileNameWithExtension, $disk);

        return FilesDB::create([
            'hash_name' => $hashFileName,
            'original_name' => $originalFileName,
            'extension' => $extension,
            'type' => $type,
            'disk' => $disk,
            'path' => $path,
        ]);
    }

    public static function MakeFile($data, $nameFile, $ext = 'txt', string $path = 'files', string $disk = 'local')
    {
        $originalFileName = $nameFile;
        $extension = $ext;
        $type = $ext;
        $hashFileName = pathinfo(Str::random(40), PATHINFO_FILENAME);
        $data = is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE);
        $fullPath = $path . '/' . $hashFileName. '.' .$ext;
        $saveFile = Storage::disk($disk)->put($fullPath, $data);

        $fileDB = FilesDB::create([
            'hash_name' => $hashFileName,
            'original_name' => $originalFileName,
            'extension' => $extension,
            'type' => $type,
            'disk' => $disk,
            'path' => $path,
        ]);

        return (object)[
            'modelFile' => $fileDB,
            'contentFile' => Storage::disk($disk)->get($fullPath),
        ];
    }

    public static function DeleteFiles($filesId)
    {
        if (is_array($filesId)) {
            $filesDb = FilesDB::whereIn('id', $filesId)->get();
            foreach ($filesDb as $file) {
                Storage::disk($file->disk)->delete($file->path . '/' . $file->hash_name . '.' . $file->extension);
                $file->delete();
            }
            return true;
        } else {
            $file = FilesDB::find($filesId);
            if ($file) {
                $file->delete();
                return Storage::disk($file->disk)->delete($file->path . '/' . $file->hash_name . '.' . $file->extension);
            }
        }
        return false;
    }

    public static function GetFileHTTP(Request $request)
    {
        $file = FilesDB::find($request->file_id);
        if ($file) {
            $filePath = Storage::disk($file->disk)->get($file->path . '/' . $file->hash_name. '.' . $file->extension);
            return response($filePath)->header('Content-type',$file->type);
        }
        return abort(404);
    }

    public static function GetFile($fileIdOrName)
    {
        if (is_int($fileIdOrName)) {
            $columnName = 'id';
        } else {
            $columnName = 'original_name';
        }

        $file = FilesDB::where($columnName, $fileIdOrName)->first();

        if ($file) {
            return (object)[
                'modelFile' => $file,
                'contentFile' => Storage::disk($file->disk)->get($file->path . '/' . $file->hash_name . '.' . $file->extension),
            ];
        }
        return false;
    }
}
