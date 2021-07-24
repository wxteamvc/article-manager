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

class ArticleManagerController extends Controller
{
    use HasResourceActions;

    protected $category_options;

    public function __construct()
    {

        // 初始化分类选择参数
        $this->category_options = ArticleCategory::pluck('title', 'id')->toArray();
    }

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
     * 手动创建文章
     */
    public function createArticle(Content $content)
    {
        return $content
            ->header(trans('admin.edit'))
            ->description(trans('admin.description'))
            ->breadcrumb(
                ['text' => '文章列表', 'url' => '/article_manager'],
                ['text' => '手动创建文章', 'url' => '#']
            )
            ->body($this->formCreate());
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
            ->body($this->formEdit($id)->edit($id));
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
        $grid->model()->with(['category'])->orderBy('id', 'desc');

        $grid->disableExport();
        $grid->actions(function(Grid\Displayers\Actions $actions){
            $actions->disableView();
        });

        // 过滤器
        $grid->filter(function($filter){
            $filter->equal('category_id', '文章分类')->select($this->category_options);
        });

        // 重写创建按钮
        $grid->disableCreateButton();
        $grid->tools(function($tools){
            $pull_route = route('article_manager.create');
            $html = <<<html
<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="{$pull_route}" class="btn btn-sm btn-success" title="新增">
        <i class="fa fa-cloud-download"></i><span class="hidden-xs">&nbsp;&nbsp;拉取微信公众号文章</span>
    </a>
</div>
html;
            $tools->append($html);

//            $create_route = route('article_manager.create_article');
            $create_route = '/admin/article_manager/create_article';
            $html2 = <<<html
<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="{$create_route}" class="btn btn-sm btn-success" title="新增">
        <i class="fa fa-hand-stop-o"></i><span class="hidden-xs">&nbsp;&nbsp;手动创建文章</span>
    </a>
</div>
html;
            $tools->append($html2);
            
        });
        $grid->id('ID');
        $grid->column('category', '所属分类')->display(function($value){
            return $value['title'];
        });
        $grid->column('title', '文章标题')->display(function($value){
            return str_limit($value, 50);
        });
        $grid->column('type', '文章类型')->display(function($value){
            if ($value == 'wx'){
                return "<span class='label label-info'>公众号文章</span> [ <a href='{$this->url}' target='_blank'><i class='fa fa-link'></i></a> ]";
            }else{
                return '<span class="label label-success">原创文章</span>';
            }

        });
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

        $form->select('category_id', '所属分类')->options($this->category_options)->required();
        $form->text('url', '文章地址')->rules('required|wx_article', [
            'required' => 'url必须填写',
            'wx_article' => 'url格式不正确'
        ])->help('暂时只支持 mp.weixin.qq.com 域名下的文章');

        $form->saving(function(Form $form){
            $url = $form->url;
            if (!$form->model()->id && $url){

                $scraper = new Scraper();
                $result = $scraper->scrape($url);
                if ($result['status'] === true){
                    $form->model()->title = $result['data']['title'];
                    $form->model()->content = $result['data']['content'];
                    $form->model()->type = 'wx';
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
                $form->model()->type = $form->model()->type ?: 'origin';
            }
            $form->model()->admin_id = Admin::user()->id;
        });

        return $form;
    }


    /**
     * 手动创建文章
     */
    protected function formCreate()
    {
        $form = new Form(new ArticleMedia);

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
        });
        $form->select('category_id', '所属分类')->options($this->category_options)->required();
        $form->text('title', '文章标题')->required();
        $form->editor('content', '文章内容');
        return $form;
    }

    protected function formEdit($id = null)
    {
        $form = new Form(new ArticleMedia);

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->tools(function(Form\Tools $tools){
            $tools->disableView();
        });

        $form->select('category_id', '所属分类')->options($this->category_options)->required();
        if ($id && ArticleMedia::where('id', $id)->value('type') == 'wx'){
            $form->text('url', '原文链接')->disable();
        }
        $form->text('title', '文章标题')->required();
        $form->editor('content', '文章内容');
        return $form;
    }


    /**
     * 富文本框获取文章列表接口
     * search_name 要查询的文章标题
     * category_id 要查询的文章分类id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function list(Request $request)
    {
        $search_name = $request->input('search_name', false);
        $category_id = $request->input('category_id', false);
        $article_manager = ArticleMedia::query()->select('id', 'category_id', 'title', 'url', 'created_at');
        if ($search_name){
            $article_manager->where('title', 'like', "%{$search_name}%");
        }
        if ($category_id){
            $article_manager->where('category_id', $category_id);
        }
        $paginator = $article_manager->orderBy('id', 'desc')->Paginate(10);
        return response()->json([
            'code' => 1,
            'data' => $paginator->items(),
            'current' => $paginator->currentPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
            'lastPage'  => $paginator->lastPage()
        ]);
    }

    /**
     * 获取文章详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
