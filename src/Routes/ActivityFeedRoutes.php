<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web','admin'],
    'namespace' => 'East\LaravelActivityfeed\Http\Backpack',
], function () { // custom admin routes
    Route::crud('af-categories', 'AfCategoriesCrudController');
    Route::crud('af-events', 'AfEventsCrudController');
    Route::crud('af-rules', 'AfRulesCrudController');
    Route::crud('af-templates', 'AfTemplatesCrudController');
}); // this should be the absolute last line of this file