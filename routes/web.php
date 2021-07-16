<?php

use Encore\ArticleManager\Http\Controllers\ArticleManagerController;
use Encore\ArticleManager\Http\Controllers\ExtendConfigController;

Route::resource('article_manager', ArticleManagerController::class);
Route::get('article_manager', ArticleManagerController::class . "@index")->name('articlemanager.index');

Route::get('article_manager_list', ArticleManagerController::class . "@list");
Route::get('article_manager_info', ArticleManagerController::class . "@info");

Route::resource('extend_config', ExtendConfigController::class);
Route::post('extend_config/setting/{group}', ExtendConfigController::class . "@saveConfig")->name('extendConfig.save');
Route::get('extend_config', ExtendConfigController::class . "@index")->name('extendConfig.index');


Route::get('extend_config/setting/{group}', ExtendConfigController::class . "@setting")->name('extendConfig.setting');

//Route::post('extend_config/{group}', ExtendConfigController::class . "@store")->name('extendConfig.store');



