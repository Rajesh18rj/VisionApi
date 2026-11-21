<?php

use Illuminate\Support\Facades\Route;

Route::get('storage/{filename}', function ($filename) {
    $path = storage_path() . '/attachment/' . $filename;
    if (!File::exists($path)) {
        return response()->json(['message' => 'Image not found.'], 404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

Route::get('storage/{date}/{filename}', function ($date, $filename) {
    $path = storage_path('Upload\\' . $date . '\\' . $filename);

    if (!File::exists($path)) {
        return response()->json([
            'message' => 'Image not found.',
            'path' => $path, // Remove after debugging
        ], 404);
    }
    $path = storage_path() . '/Upload/' . $date . '/' . $filename;
    if (!File::exists($path)) {
        return response()->json(['message' => 'Image not found.'], 404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

Route::get('personimage/{filename}', function ($filename) {
    $path = storage_path('app/public/Upload/personimage/' . $filename);

    if (!File::exists($path)) {
        return response()->json([
            'message' => 'Image not found.'
        ], 404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});




Route::get('/', function () {
    return view('welcome');
});

// Route::any('{all}', function () {
//     return \File::get('index.html');
// })->where('all', '.*');
