<?php

namespace Encore\\ArticleManager\Http\Controllers;

use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class ArticleManagerController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Title')
            ->description('Description')
            ->body(view('article-manager::index'));
    }
}