<?php
namespace Encore\ArticleManager;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ArticleMangerSeeder extends Seeder
{

    public function run()
    {
        try {
            $datetime = date('Y-m-d H:i:s');
            // 这边把资源管理路由菜单加到数据表
            DB::table('admin_menu')->insert([
                'id' => 50,
                'module_name' => 'admin',
                'parent_id' => 1,
                'order' => 30,
                'title' => '文章库',
                'icon' => 'fa-file',
                'uri' => 'article_manager',
                'status' => 2,
                'is_blank' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime
            ]);
            DB::table('admin_menu')->insert([
                'id' => 51,
                'module_name' => 'admin',
                'parent_id' => 50,
                'order' => 1,
                'title' => '文章库管理',
                'icon' => 'fa-file',
                'uri' => 'article_manager',
                'status' => 2,
                'is_blank' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime
            ]);
            DB::table('admin_menu')->insert([
                'id' => 52,
                'module_name' => 'admin',
                'parent_id' => 50,
                'order' => 1,
                'title' => '文章库配置',
                'icon' => 'fa-file',
                'uri' => 'extend_config/setting/article_manager',
                'status' => 2,
                'is_blank' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime
            ]);
        }catch (\Exception $e){
            dump('菜单表添加记录失败,可能记录已经存在');
        }

        try{
            // 这边把资源管理理由权限加到数据表
            DB::table('admin_permissions')->insert([
                [
                    'id' => 50,
                    'module_name' => 'admin',
                    'name' => '文章库',
                    'slug' => 'article_manager.all',
                    'http_method' => '',
                    'http_path' => '',
                    'parent_id' => 1,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 51,
                    'module_name' => 'admin',
                    'name' => '文章库管理',
                    'slug' => 'article_manager.index',
                    'http_method' => 'GET,POST,PUT,DELETE',
                    'http_path' => '/article_manager*',
                    'parent_id' => 50,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 52,
                    'module_name' => 'admin',
                    'name' => '文章库配置管理',
                    'slug' => 'article_manager.config',
                    'http_method' => 'GET,POST,PUT,DELETE',
                    'http_path' => '/extend_config*',
                    'parent_id' => 50,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ]
            ]);
        }catch(\Exception $exception){
            dump('权限表添加记录失败,可能记录已经存在');
        }

        try {
            // 这边把资源管理理由权限加到数据表
            DB::table('admin_permissions')->insert([
                [
                    'id' => 53,
                    'module_name' => 'admin',
                    'name' => '获取文章列表',
                    'slug' => 'article_manager.list',
                    'http_method' => 'GET',
                    'http_path' => '/article_manager_list',
                    'parent_id' => 50,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 54,
                    'module_name' => 'admin',
                    'name' => '资源组件上传',
                    'slug' => 'article_manager.info',
                    'http_method' => 'GET',
                    'http_path' => '/article_manager_info',
                    'parent_id' => 50,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ]
            ]);
        }catch(\Exception $exception){
            dump('资源组件权限添加失败,可能记录已经存在');
        }

    }

    public function uninstall()
    {
        try{

        }catch(\Exception $exception){

        }
    }


}
