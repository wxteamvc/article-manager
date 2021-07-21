<?php
namespace Encore\ArticleManager\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
    protected $table = 'admin_article_category';


    /**
     * 文章分类和文章 一对多关联关系
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany(ArticleMedia::class, 'category_id', 'id');
    }
}