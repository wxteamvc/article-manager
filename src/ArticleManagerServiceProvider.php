<?php

namespace Encore\ArticleManager;

use Encore\ArticleManager\Commands\InstallCommand;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ArticleManagerServiceProvider extends ServiceProvider
{
    protected $commands = [InstallCommand::class];
    /**
     * {@inheritdoc}
     */
    public function boot(ArticleManager $extension)
    {
        if (! ArticleManager::boot()) {
            return ;
        }

        // =============增加一个微信文章验证规则=================
        Validator::extend('wx_article', function($attribute, $value, $parameters, $validator){
            $parse = parse_url($value);
            if (!isset($parse['scheme']) || !in_array($parse['scheme'], ['http', 'https'])){
                return false;
            }
            if (!isset($parse['host']) || $parse['host'] != "mp.weixin.qq.com"){
                return false;
            }
            return true;
        });
        // ==================================================

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'article-manager');
        }

//        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
//            $this->publishes(
//                [$assets => public_path('vendor/fengwuyan/article-manager')],
//                'article-manager'
//            );
//        }

        if ($this->app->runningInConsole()){
            $this->publishes([__DIR__.'/../config/article_manager.php' => config_path('article_manager.php')], 'article-manager-config');
        }

        $this->app->booted(function () {
            ArticleManager::routes(__DIR__.'/../routes/web.php');
        });
    }

    public function register()
    {
        $this->commands($this->commands);
    }
}
