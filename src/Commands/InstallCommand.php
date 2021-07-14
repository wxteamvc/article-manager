<?php
namespace Encore\ArticleManager\Commands;


use Encore\ArticleManager\ArticleManagerServiceProvider;
use Illuminate\Console\Command;


class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article-manager:install';

    /**
     * The console command description.
     *go-fastdfs media
     * @var string
     */
    protected $description = 'article-manager install';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('正在发布资源文件......');

        $this->call('vendor:publish', [
            '--provider' => ArticleManagerServiceProvider::class,
            '--force' => true
        ]);

        $this->info('正在执行迁移文件......');

        $this->call('migrate', ['--path' => './vendor/fengwuyan/article-manager/database/migrations']);


        $this->info('安装完成');
    }

}