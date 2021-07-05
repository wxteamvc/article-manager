<?php

namespace Encore\\ArticleManager;

use Illuminate\Support\ServiceProvider;

class ArticleManagerServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot(ArticleManager $extension)
    {
        if (! ArticleManager::boot()) {
            return ;
        }

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'article-manager');
        }

        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/fengwuyan/article-manager')],
                'article-manager'
            );
        }

        $this->app->booted(function () {
            ArticleManager::routes(__DIR__.'/../routes/web.php');
        });
    }
}