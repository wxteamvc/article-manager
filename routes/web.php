<?php

use Encore\ArticleManager\Http\Controllers\ArticleManagerController;


Route::resource('article_manager', ArticleManagerController::class);
Route::get('article_manager', ArticleManagerController::class . "@index")->name('articlemanager.index');
Route::resource('article_manager_config', \Encore\ArticleManager\Http\Controllers\ArticleManagerConfigController::class);

// 获取文章列表
Route::get('article_list', ArticleManagerController::class . "@getList")->name('articlemanager.list');
// 通过id获取指定文章内容
Route::get('article_content', ArticleManagerController::class . "@getContent")->name('articlemanager.content');