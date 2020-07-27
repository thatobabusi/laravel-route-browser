<?php

use ThatoBabusi\RouteBrowser\AssetController;
use ThatoBabusi\RouteBrowser\EnabledMiddleware;
use ThatoBabusi\RouteBrowser\RouteListController;

Route
    ::prefix(config('route-browser.path', 'routes'))
    ->middleware(EnabledMiddleware::class)
    ->group(static function () {

        Route::get('/', RouteListController::class)
            ->name('route-browser.list');

        Route::get('assets/{path}', AssetController::class)
            ->where('path', '.*')
            ->name('route-browser.asset');

    });
