<?php

use DaveJamesMiller\RouteBrowser\AssetController;
use DaveJamesMiller\RouteBrowser\EnabledMiddleware;
use DaveJamesMiller\RouteBrowser\RouteListController;

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
