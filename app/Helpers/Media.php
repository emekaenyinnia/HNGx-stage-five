<?php

namespace App\Helpers;

use getID3;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


set_time_limit(300);
class Media
{

    public static function store($request)
    {
        $file = $request->file('record');
        $extension = $file->getClientOriginalExtension();
        $filename = 'record_' . random_int(000000, 999999) . '_' . $file->getClientOriginalName();
        $file->storeAs('public/records', $filename);
        $fileSize = $file->getSize();

        $data['filename'] = $filename;
        $data['size'] = self::formatSizeUnits($fileSize);
        $data['duration'] = self::formatVideoDuration($file);
        $data['extension'] = $extension;
        $data['thumbnail'] = self::storeThumbnail($request);
        $data['url'] = config('app.url') . '/storage/records/' . $data['filename'];
        return $data;
    }


    public static function storeThumbnail($request)
    {
        $thumbnail = null;
        $file = $request->file('thumbnail');
        if (!empty($file)) {
            $extension = $file->getClientOriginalExtension();
            $filename = 'thumbnail_' . random_int(000000, 999999) . '_' . $file->getClientOriginalName();
            $file->storeAs('public/thumbnails', $filename);

            $thumbnail = config('app.url') . '/storage/thumbnails/' . $filename;
        }
        return $thumbnail;
    }

    public static function deleteRecord($record)
    {
        $path = Storage::disk('public')->path("records/{$record->name}");
        if (file_exists($path)) {
            Storage::disk('public')->delete("records/{$record->name}");
            self::deleteThumbnail($record);
        }
    }

    public static function deleteThumbnail($record)
    {
        $path = Storage::disk('public')->path("thumbnails/{$record->name}");
        if (file_exists($path)) {
            return  Storage::disk('public')->delete("thumbnails/{$record->name}");
        }
    }

    public static function convertRecordToText($path_name)
    {

        $transcirbe = null;
        $path = Storage::disk('public')->path("records/{$path_name}");
        $file = fopen($path, 'r');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('app.whisper_api'),
        ])->attach('file', $file)->post(
            'https://transcribe.whisperapi.com',
            [
                'diarization' => 'false',
                'fileType' => 'mp4',
                'task' => 'transcribe',
                "language" => "en",
            ]
        );
        $data = $response->json();
        if ($response->successful()) {
            $transcription = $data['text'];
            $transcirbe = [
                'transcription' => $transcription,
                'transcription_url' => 'not yet implemented',
            ];
        }
        return  $transcirbe;
    }

    // public static function storeTranscribeText($transcription) 
    // {
    //  $transcirbe = null;
    //     $fileName = random_int(000000, 999999)  . '_transcription.txt';
    //     $storagePath = storage_path('app/public/transcribe/' . $fileName);
    //     $filePath = 'app/public/transcribe/' . $fileName;
    //     if (File::exists($storagePath)) {
    //         File::put($storagePath, $transcription);
    //         $transcirbe = [
    //             'transcription' => $transcription,
    //             'transcription_url' => 'https://hngx-stage-five.onrender.com' . '/storage/records/' . $fileName,
    //         ];
    //     }; 
    //     return $transcirbe;
    // }
    protected static function formatVideoDuration($video_path)
    {
        $getID3 = new getID3;
        $file = $getID3->analyze($video_path);
        $duration = date('H:i:s.v', $file['playtime_seconds']);
        return $duration;
    }
    protected static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    // Add more helper methods as needed
}
