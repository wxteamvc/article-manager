<?php

use Encore\ArticleManager\Http\Controllers\ArticleManagerController;


Route::resource('article_manager', ArticleManagerController::class);
Route::get('article_manager', ArticleManagerController::class . "@index")->name('articlemanager.index');
