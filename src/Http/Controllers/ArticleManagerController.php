<?php
namespace Encore\ArticleManager\Http\Controllers;

use Encore\ArticleManager\Http\Models\WxArticle;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\ArticleManager\Http\Tools\Scraper;

class ArticleManagerController extends Controller
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
            ->header(trans('admin.index'))
            ->description(trans('admin.description'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header(trans('admin.detail'))
            ->description(trans('admin.description'))
            ->body($this->detail($id));
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
            ->body($this->formEdit()->edit($id));
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
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WxArticle);

        $grid->id('ID');
        $grid->column('url', '原文链接')->link();
        $grid->column('title', '文章标题');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WxArticle::findOrFail($id));

        $show->id('ID');
        $show->url('url');
        $show->title('title');
        $show->content('content');
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WxArticle);

        $form->text('url', '文章地址');

        return $form;
    }

    protected function formEdit()
    {
        $form = new Form(new WxArticle);
        $form->text('url', '原文链接')->disable();
        $form->text('title', '文章标题');
//        $form->editor('content', '文章内容');
        return $form;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        $data = request()->all();
        $url = $data['url'];
        $scraper = new Scraper();
        $result = $scraper->scrape($url);
        if ($result !== false){
            $wx_article = new WxArticle();
            $wx_article->title = $result['data']['title'];
            $wx_article->content = $result['data']['content'];
            $wx_article->url = $url;
            $wx_article->save();
        }
        return redirect(route('articlemanager.index'));

    }

}
