<?php

namespace App\Http\Controllers;


use App\Helpers\Media;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;


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
            $transcribed = null;
            $transcribed = Media::convertRecordToText($data['filename']);
            if ($transcribed === null) {
                return response()->json(['error' => 'Failed to convert video to text'], 500);
            }

            $records = Record::create([
                'name' => $data['filename'],
                'url' => $data['url'],
                'size' =>  $data['size'],
                'extension' =>  $data['extension'],
                'duration' => $data['duration'],
                'thumbnail' =>  $data['thumbnail'],
                'transcribe_text' =>  $transcribed['transcription'],
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

            return response()->json([
                'message' => "record retrieved successfully.",
                'data' =>  $record
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
