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
                'title' => '文章分类管理',
                'icon' => 'fa-file',
                'uri' => 'article_category',
                'status' => 2,
                'is_blank' => 1,
                'created_at' => $datetime,
                'updated_at' => $datetime
            ]);
            DB::table('admin_menu')->insert([
                'id' => 53,
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
//            dump('菜单表添加记录失败,可能记录已经存在');
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
//            dump('权限表添加记录失败,可能记录已经存在');
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
                    'name' => '文章详情',
                    'slug' => 'article_manager.info',
                    'http_method' => 'GET',
                    'http_path' => '/article_manager_info',
                    'parent_id' => 50,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 55,
                    'module_name' => 'admin',
                    'name' => '文章分类列表',
                    'slug' => 'article_manager.category',
                    'http_method' => 'GET',
                    'http_path' => '/article_manager_category_list',
                    'parent_id' => 50,
                    'order' => 0,
                    'is_default' => 2,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
            ]);
        }catch(\Exception $exception){
//            dump('资源组件权限添加失败,可能记录已经存在');
        }

        // 添加配置
        $group = 'article_manager';
        try {
            DB::table('admin_extend_config')->insert([
                [
                    'id' => 1,
                    'group' => 'article_manager',
                    'title' => '是否开启图片压缩',
                    'key' => 'is_resize',
                    'value' => '1',
                    'field_type' => 'switch',
                    'help' => '开启后文章图片会进行压缩',
                    'admin_id' => 1,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 2,
                    'group' => 'article_manager',
                    'title' => '图片压缩最小宽度',
                    'key' => 'not_resize_width',
                    'value' => '"200"',
                    'field_type' => 'text',
                    'help' => '在图片压缩开启的情况下,小于该宽度的图片不压缩',
                    'admin_id' => 1,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 3,
                    'group' => 'article_manager',
                    'title' => '图片压缩宽度',
                    'key' => 'resize_w',
                    'value' => '"480"',
                    'field_type' => 'text',
                    'help' => '图片压缩的宽度,建议只填写宽度或者高度,图片等比例压缩',
                    'admin_id' => 1,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ],
                [
                    'id' => 4,
                    'group' => 'article_manager',
                    'title' => '图片压缩高度',
                    'key' => 'resize_h',
                    'value' => '"0"',
                    'field_type' => 'text',
                    'help' => '',
                    'admin_id' => 1,
                    'created_at' => $datetime,
                    'updated_at' => $datetime
                ]
            ]);
        }catch(\Exception $exception){

        }

    }

    public function uninstall()
    {
        try{

        }catch(\Exception $exception){

        }
    }


}
