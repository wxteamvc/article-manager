<?php

namespace Encore\\ArticleManager;

use Encore\Admin\Extension;

class ArticleManager extends Extension
{
    public $name = 'article-manager';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    public $menu = [
        'title' => 'Articlemanager',
        'path'  => 'article-manager',
        'icon'  => 'fa-gears',
    ];
}