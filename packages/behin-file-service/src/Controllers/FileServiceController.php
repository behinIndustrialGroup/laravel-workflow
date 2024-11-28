<?php

namespace FileService\Controllers;

use App\Http\Controllers\Controller;
use FileService\Services\File\FileService;

class FileServiceController extends Controller
{
    public function uploadAndGetFile($request, $file, $directory = 'files') : object{
        $fileService = new FileService;
        $inputs = $request->all();
        if ($request->hasFile('file')) {
            if (!empty($file->file_path)) {
                // $fileService->deleteFile($file->file_path, true);
                $fileService->deleteFile($file->file_path);
            }
            $fileService->setExclusiveDirectory('files' . DIRECTORY_SEPARATOR . $directory);
            $fileService->setFileSize($request->file('file'));
            $fileSize = $fileService->getFileSize();
            $result = $fileService->moveToPublic($request->file('file'));
            // $result = $fileService->moveToStorage($request->file('file'));
            $fileFormat = $fileService->getFileFormat();
        }
        if ($result === false) {
            return response(trans("upload file not ok"), 403);
        }
        $file->file_path = 'public' . DIRECTORY_SEPARATOR . $result;
        $file->file_size = $fileSize;
        $file->file_type = $fileFormat;
        return $file;
    }
}
