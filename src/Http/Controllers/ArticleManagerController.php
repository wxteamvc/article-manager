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
use Illuminate\Http\Request;
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
            ->header('文章库管理')
            ->description('文章列表')
            ->breadcrumb(
                ['text' => '文章列表', 'url' => '#']
            )
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
            ->breadcrumb(
                ['text' => '文章列表', 'url' => '/article_manager'],
                ['text' => '文章编辑', 'url' => '#']
            )
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
            ->breadcrumb(
                ['text' => '文章列表', 'url' => '/article_manager'],
                ['text' => '文章创建', 'url' => '#']
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
        $grid = new Grid(new ArticleMedia);
        $grid->disableExport();
        $grid->actions(function(Grid\Displayers\Actions $actions){
            $actions->disableView();
        });
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

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();

        $form->text('url', '文章地址')->rules('required|wx_article', [
            'required' => 'url必须填写',
            'wx_article' => 'url格式不正确'
        ])->help('暂时只支持 mp.weixin.qq.com 域名下的文章');

        $form->saving(function(Form $form){
            if (!$form->model()->id){
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
            }else{
                $form->model()->title = $form->title;
                $form->model()->content = $form->content;
            }

        });

        return $form;
    }

    protected function formEdit()
    {
        $form = new Form(new ArticleMedia);

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
        });

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


    public function list(Request $request)
    {
        $search_name = $request->input('search_name');
        $article_manager = ArticleMedia::query()->select('id','title', 'url', 'created_at');
        if ($search_name){
            $article_manager->where('title', 'like', "%{$search_name}%");
        }
        $paginator = $article_manager->Paginate(10);
        return response()->json([
            'code' => 1,
            'data' => $paginator->items(),
            'current' => $paginator->currentPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
            'lastPage'  => $paginator->lastPage()
        ]);
    }

    public function info(Request $request)
    {
        $id = $request->input('id', 0);
        $article = ArticleMedia::find($id);
        if (!$article){
            return response()->json([
                'code' => 0,
                'message' => '文章未找到',
                'data' => []
            ]);
        }
        return response()->json([
            'code' => 1,
            'message' => 'sucess',
            'data' => $article
        ]);
    }

}
