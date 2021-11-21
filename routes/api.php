<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/', function () {
    return ['message' => 'it works'];
});

Route::any('/items', function () {
    $path = resource_path('items.json');
    if (file_exists($path)) {
        return response()->file($path);
    }
    return 'File not exists.';
});

Route::any('/effects', function () {
    $path = resource_path('effects.json');
    if (file_exists($path)) {
        return response()->file($path);
    }
    return 'File not exists.';
});

Route::any('/outfit', function () {
    $generator = new \App\Services\OutfitGeneratorService();
    if (!$params = $_GET['params'] ?? null) {
        return 'Wrong outfit params';
    }

    $outfit = $generator->generate($params);
    header('Content-Type: image/png');
    imagepng($outfit);
    imagedestroy($outfit);
});
