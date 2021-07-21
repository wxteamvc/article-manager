<?php
namespace Encore\ArticleManager\Http\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleMedia extends Model
{
    protected $table = 'admin_article_media';

    /**
     * 文章反向关联 文章分类
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id', 'id');
    }
}
