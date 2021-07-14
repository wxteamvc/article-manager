<?php
namespace Encore\ArticleManager\Http\Controllers;

use Encore\ArticleManager\Http\Models\ArticleMedia;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\ArticleManager\Http\Tools\Scraper;
use Illuminate\Support\MessageBag;

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
        $grid = new Grid(new ArticleMedia);

        $grid->id('ID');
        $grid->column('title', '文章标题');
        $grid->column('url', '原文链接')->link();
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
        $show = new Show(ArticleMedia::findOrFail($id));

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
        $form = new Form(new ArticleMedia);

        $form->text('url', '文章地址')->rules('required|wx_article', [
            'required' => 'url必须填写',
            'wx_article' => 'url格式不正确'
        ])->help('暂时只支持 mp.weixin.qq.com 域名下的文章');

        $form->saving(function(Form $form){
            $url = $form->url;
            $scraper = new Scraper();
            $result = $scraper->scrape($url);
            if ($result['status'] === true){
                $form->model()->title = $result['data']['title'];
                $form->model()->content = $result['data']['content'];
            }else{
                $error = new MessageBag([
                    'title'   => '抓取文章出错',
                    'message' => $result['message']
                ]);
                return back()->with(compact('error'));
            }
        });

        return $form;
    }

    protected function formEdit()
    {
        $form = new Form(new ArticleMedia);
        $form->text('url', '原文链接')->disable();
        $form->text('title', '文章标题');
        $form->editor('content', '文章内容');
        return $form;
    }

//    /**
//     * Store a newly created resource in storage.
//     *
//     * @return mixed
//     */
//    public function store()
//    {
//        $data = request()->all();
//        $url = $data['url'];
//        $scraper = new Scraper();
//        $result = $scraper->scrape($url);
//        if ($result !== false){
//            $wx_article = new ArticleMedia();
//            $wx_article->title = $result['data']['title'];
//            $wx_article->content = $result['data']['content'];
//            $wx_article->url = $url;
//            $wx_article->save();
//        }
//        return redirect(route('articlemanager.index'));
//
//    }

}
