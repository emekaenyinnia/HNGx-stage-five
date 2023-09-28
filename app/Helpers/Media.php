<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class Media
{

    public static function store($request)
    {
        $file = $request->file('media');
        $filename = 'screen_record_' . random_int(1000000, 9999999);
        $file->storeAs('public/records', $filename);
        return $filename;
    }


    public static function storeThumbnail($request)
    {
        $thumbnail = null;
        $file = $request->file('thumbnail');
        if (!empty($file)) {
            $filename = 'screen_record_thumbnail_' . random_int(1000000, 9999999);
            $file->storeAs('public/thumbnails', $filename);
            $thumbnail = $filename;
        }
        return $thumbnail;
    }

    // Add more helper methods as needed
}
