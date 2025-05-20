<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/blog/{slug}-{id}', function (string $slug, string $id) {
    $test = 'test';
    return [
        'slug' => $slug,
        'id' => $id,
        'test' => $test
    ];
});

require __DIR__ . '/auth.php';
