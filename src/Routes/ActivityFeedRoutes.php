<?php

use Illuminate\Support\Facades\Route;

Route::get('/af-data/tables', 'East\LaravelActivityfeed\Http\Api\AfData@tables');
Route::get('/af-data/columns', 'East\LaravelActivityfeed\Http\Api\AfData@columns');
Route::get('/af-data/relationships', 'East\LaravelActivityfeed\Http\Api\AfData@relationships');
Route::get('/af-data/tableInfo', 'East\LaravelActivityfeed\Http\Api\AfData@tableInfo');
Route::get('/af-data/targeting', 'East\LaravelActivityfeed\Http\Api\AfData@targeting');
Route::get('/af-data/var-replacer', 'East\LaravelActivityfeed\Http\Api\AfData@varReplacer');
//Route::get('/fetch/template', 'East\LaravelActivityfeed\Http\Api\AfData@targeting');

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web','admin'],
    'namespace' => 'East\LaravelActivityfeed\Http\Backpack',
], function () { // custom admin routes
    Route::crud('af-categories', 'AfCategoriesCrudController');
    Route::crud('af-events', 'AfEventsCrudController');
    Route::crud('af-rules', 'AfRulesCrudController');
    Route::crud('af-templates', 'AfTemplatesCrudController');
   // Route::crud('af-templates/fetch', 'AfTemplatesCrudController');
}); // this should be the absolute last line of this file