<?php

namespace App\Http\Controllers;

use App\Helpers\Media;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RecordController extends Controller
{
    public function uploadRecord(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'media' => 'required|file|mimetypes:video/*|max:20480',
                'thumbnail' => 'file|mimetypes:image/*|max:20480'
            ]);
            if ($validate->fails()) {
             return response()->json(['message' => $validate->errors()->all(),], 404);
            }
            $name = Media::store($request);
            $thumbnail = Media::storeThumbnail($request);
            $records = Record::create([
                'name' => $name,
                'url' => config('app.url') . '/api/' . $name,
                'thumbnail' => $thumbnail
            ]);
            return response()->json([
                'message' => "Record uploaded successfully.",
                'data' => $records
            ], 201);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 503);
        }
    }


    public function getRecord(Request $request)
    {
        try {
            $record = Record::where('name', $request->name)->first();
            if (!$record) {
                return response()->json(['message' => 'Media not found.'], 404);
            }
            return response()->json([
                'message' => "Media uploaded successfully.",
                'data' => $record
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }


    public function getAllRecord()
    {
        try {
            $records = Record::all();
            return response()->json([
                'message' => "Records Retrieved successfully.",
                'data' => $records
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }

}
