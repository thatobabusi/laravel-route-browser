<?php

namespace DaveJamesMiller\RouteBrowser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class AssetController
{
    private const MIME_TYPES = [
        'css' => 'text/css',
        'js' => 'application/javascript',
    ];

    public function __invoke($path, Request $request)
    {
        if (strpos($path, '..') !== false) {
            abort(404);
        }

        $file = __DIR__ . '/../build/' . trim($path, '/');
        if (!File::exists($file)) {
            abort(404);
        }

        $headers = [];

        // Not all mime types are guessed automatically
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (isset(static::MIME_TYPES[$extension])) {
            $headers['Content-Type'] = static::MIME_TYPES[$extension];
        }

        return Response::file($file, $headers);
    }
}
