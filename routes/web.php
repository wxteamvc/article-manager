<?php

use Encore\ArticleManager\Http\Controllers\ArticleManagerController;


Route::resource('wx_articles', ArticleManagerController::class);
Route::get('wx_articles', ArticleManagerController::class . "@index")->name('articlemanager.index');
