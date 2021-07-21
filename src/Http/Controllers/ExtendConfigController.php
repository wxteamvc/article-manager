<?php
namespace Encore\ArticleManager\Http\Controllers;


use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;
use Encore\ArticleManager\Http\Models\ExtendConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class ExtendConfigController extends Controller
{

    use HasResourceActions;

    //定义导航
    protected $name = '配置扩展管理';


    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('扩展配置')
            ->description('扩展配置详情')
            ->body($this->grid());
    }


    public function setting(Content $content, $group)
    {
        return $content
            ->header('配置设置')
            ->description($group)
            ->breadcrumb(
                ['text' => '文章库配置', 'url' => '#']
            )
            ->body($this->settingForm($group));
    }



    public function create(Content $content)
    {
        return $content
            ->header('扩展配置')
            ->description('添加配置')
            ->body($this->Form());
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
            ->header('扩展配置')
            ->description('编辑配置')
            ->body($this->Form()->edit($id));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ExtendConfig());

        $grid->disableExport();
        $grid->actions(function(Grid\Displayers\Actions $actions){
            $actions->disableView();
        });
        $grid->id('ID');
        $grid->column('group', '分组');
        $grid->column('title', '配置标题');
        $grid->column('field_type', '字段类型');
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        return $grid;
    }

    protected function Form()
    {
        $form = new Form(new ExtendConfig());
        $groups = ExtendConfig::groupBy('group')->pluck('group')->toArray();

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();

        if ($groups){
            $groups = implode(',', $groups);
            $group_help = "已存在分组{$groups}";
        }else{
            $group_help = "还未存在分组";
        }
        $form->text('group', '配置分组')->required()->help($group_help);
        $form->text('title', '配置名称')->required();
        $form->text('key', '配置键值')->required();
        $form->textarea('help', '字段提示信息');
        $form->select('field_type', '字段类型')->options(ExtendConfig::$field_options)->required();
        $form->hasMany('config_options', '字段配置', function(Form\NestedForm $form){
            $form->text('options_key', '选项键');
            $form->text('options_value', '选项值');
        });

        return $form;
    }

    public function store(Request $request)
    {
        $params = $request->all();
        DB::beginTransaction();
        try{
            $extend_config = new ExtendConfig();
            $extend_config->group = $params['group'];
            $extend_config->title = $params['title'];
            $extend_config->key = $params['key'];
            $extend_config->field_type = $params['field_type'];
            $extend_config->admin_id = Admin::user()->id;
            $extend_config->save();

            if (in_array($extend_config->field_type, ['radio', 'checkbox', 'select', 'multipleSelect'])){
                $filter_options = [];
                $options = array_filter(array_values($params['config_options']), function($array) use(&$filter_options){
                    $filter_options[] = [
                        'options_key' => $array['options_key'],
                        'options_value' => $array['options_value'],
                    ];
                    return true;
                });
                $extend_config->config_options()->createMany($filter_options);
            }
            DB::commit();
            $success = new MessageBag([
                'title' => '创建配置成功'
            ]);
            return redirect(route('extend_config.index'));
        }catch(\Exception $exception){
            return redirect(route('extend_config.index'))->withErrors($exception->getMessage());
        }

    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function settingForm($group)
    {
        $form = new Form(new ExtendConfig());

        // 关闭无用功能
        $form->disableEditingCheck();
        $form->disableViewCheck();
        $form->tools(function(Form\Tools $tools){
            // 去掉`列表`按钮
            $tools->disableList();
        });

        $form_fields = ExtendConfig::where('group', $group)->get();

        foreach ($form_fields as $field){

            $field_type =  $field->field_type;
            if ($field_type == 'switch'){
                $form_field = $form->{$field_type}($field->key, $field->title)->states([
                    'on' => ['value' => 1, 'text' => '开启', 'color' => 'success'],
                    'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger']
                ]);
            } elseif (in_array($field_type, ['radio', 'checkbox', 'select', 'multipleSelect'])){
                $form_field =$form->{$field_type}($field->key, $field->title)->options($field->getOptions());
            } else {
                $form_field = $form->{$field_type}($field->key, $field->title);
            }
            $form_field = $form_field->default($field->value);
            if ($field->help){
                $form_field->help($field->help);
            }
        }

        $form->setAction(route('extendConfig.save', ['group' => $group]));
        return $form;
    }


    public function saveConfig(Request $request, $group)
    {

        $result = ExtendConfig::saveConfig($request->all(),$group);
        if ($result == true){
            $success = new MessageBag([
                'title'   => '保存成功',
            ]);
            return back()->with(compact('success'));
        }else{
            $error = new MessageBag([
                'title'   => '保存失败',
            ]);
            return back()->with(compact('error'));
        }


    }

}