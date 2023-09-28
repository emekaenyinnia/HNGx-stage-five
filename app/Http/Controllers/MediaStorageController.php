<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class MediaStorageController extends Controller
{
    public function upload(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), ['media' => 'required|file|mimetypes:video/*|max:20480',]);
            if ($validate->fails()) {
                return response()->json(['message' => $validate->errors()->all(),], 400);
            }
            return response()->json([
                'message' => "Media uploaded successfully.",
                'url' => config('app.url').'/api/'.$this->storeMedia($request),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }


    public function downloadMedia($filename)
    {
        try {
            $path = Storage::disk('public')->path("mediaStorage/{$filename}");
            if (file_exists($path)) {
                return response()->file($path);
            }
            return response()->json(['message' => 'Media not found.'], 404);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }


    public function storeMedia($request)
    {
        $file = $request->file('media');
        $filename = 'screen_record_'.random_int(1000000, 9999999);
        $file->storeAs('public/mediaStorage', $filename);
        return $filename;
    }
}
