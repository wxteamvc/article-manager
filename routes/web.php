<?php

use Encore\ArticleManager\Http\Controllers\ArticleManagerController;
use Encore\ArticleManager\Http\Controllers\ExtendConfigController;
use Encore\ArticleManager\Http\Controllers\ArticleCategoryController;

Route::get('article_manager/create_article', ArticleManagerController::class . "@createArticle")->name('article_manager.create_article');
Route::resource('article_manager', ArticleManagerController::class);

Route::resource('article_category', ArticleCategoryController::class);

// 获取分类列表
Route::get('article_manager_category_list', ArticleCategoryController::class . "@list");
// 获取文章列表
Route::get('article_manager_list', ArticleManagerController::class . "@list");
// 获取文章详情
Route::get('article_manager_info', ArticleManagerController::class . "@info");

Route::resource('extend_config', ExtendConfigController::class);
Route::post('extend_config/setting/{group}', ExtendConfigController::class . "@saveConfig")->name('extendConfig.save');

Route::get('extend_config/setting/{group}', ExtendConfigController::class . "@setting")->name('extendConfig.setting');




