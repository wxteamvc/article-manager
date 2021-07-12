<?php

use Encore\ArticleManager\Http\Controllers\ArticleManagerController;

Route::get('article-manager', ArticleManagerController::class.'@index');
