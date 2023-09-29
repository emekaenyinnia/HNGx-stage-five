<?php

namespace App\Http\Controllers;

use App\Helpers\Media;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecordController extends Controller
{
    public function uploadRecord(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'record' => 'required|file|mimetypes:video/*|max:20480',
                'thumbnail' => 'file|mimetypes:image/*|max:20480'
            ]);
            if ($validate->fails()) {
             return response()->json(['message' => $validate->errors()->all(),], 404);
            }
            DB::beginTransaction();
            $data = Media::store($request);
            $thumbnail = Media::storeThumbnail($request);
            $records = Record::create([
                'name' => $data['filename'],
                'url' => 'https://hngx-stage-five.onrender.com' . '/api/' . $data['filename'],
                'size' =>  $data['size'],
                'extension' =>  $data['extension'],
                'duration' => $data['duration'],
                'thumbnail' => $thumbnail
            ]);
            DB::commit();
            return response()->json([
                'message' => "record uploaded successfully.",
                'data' => $records
            ], 201);
          
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 503);
        }
    }


    public function getRecord($name)
    {
        try {
          
            $record = Record::where('name', $name)->first();
            if (!$record) {
                return response()->json(['message' => 'record not found.'], 404);
            }
            $path = storage_path('app/public/records'. $name);
            // $path = Storage::disk('public')->path("records/{$name}");

            if (file_exists($path)) {
                return response()->file($path);
            }else{
                return response()->json(['message' => 'cant retrieve record.'], 404);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }


    public function getAllRecord()
    {
        try {
            $records = Record::all();
            return response()->json([
                'message' => "records retrieved successfully.",
                'data' => $records
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }

    public function deleteRecord(Request $request)
    {
        try {
            $record = Record::where('name', $request->name)->first();
            if (!$record) {
                return response()->json(['message' => 'record not found.'], 404);
            }
            DB::beginTransaction();
           $deteled = $record->delete();
           if ($deteled) {
               Media::deleteRecord($request);
           }
            DB::commit();
            return response()->json([
                'message' => "record delete successfully.",
            ], 202);
          
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(),], 503);
        }
    }

}
