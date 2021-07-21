<?php
namespace Encore\ArticleManager\Http\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\ArticleManager\Http\Models\ArticleCategory;
use Encore\ArticleManager\Http\Models\ArticleMedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\ArticleManager\Http\Tools\Scraper;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class ArticleCategoryController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('文章分类管理')
            ->description('分类列表')
            ->breadcrumb(
                ['text' => '分类列表', 'url' => '#']
            )
            ->body($this->grid());
    }


    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->breadcrumb(
                ['text' => '文章列表', 'url' => '/article_manager'],
                ['text' => '文章编辑', 'url' => '#']
            )
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header(trans('admin.create'))
            ->description(trans('admin.description'))
            ->breadcrumb(
                ['text' => '分类列表', 'url' => '/article_category'],
                ['text' => '分类创建', 'url' => '#']
            )
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ArticleCategory());
        $grid->disableExport();
        $grid->actions(function(Grid\Displayers\Actions $actions){
            $actions->disableView();
        });


        $grid->id('ID');
        $grid->column('title', '分类标题');
        $grid->column('remark', '分类备注');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ArticleCategory());

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();

        $form->text('title', '分类标题')->required();
        $form->textarea('remark','分类备注');

        $form->saving(function(Form $form){
            $form->model()->admin_id = Admin::user()->id;
        });

        return $form;
    }

    /**
     * 获取文章分类列表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $article_category = ArticleCategory::select('id', 'title')->get()->toArray();
        return response()->json([
           'code' => 1,
           'data' =>  $article_category
        ]);
    }


}

