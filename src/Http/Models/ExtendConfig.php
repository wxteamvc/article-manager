<?php
namespace Encore\ArticleManager\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ExtendConfig extends Model
{
    protected $table = 'admin_extend_config';

    // 缓存前缀
    const cache_prefix = 'c_config_';

    public static $field_options = [
        'text' => 'Text文本框',
        'textarea' => 'Textarea文本域',
        'radio' => 'Radio选择',
        'checkbox' => 'Checkbox选择',
        'select' => 'Seclet单选',
        'multipleSelect' => 'Select多选',
        'switch' => 'Switch开关'
    ];

    public function config_options()
    {
        return $this->hasMany(ExtendConfigOptions::class, 'config_id', 'id');
    }

    public function getOptions()
    {
        $options = $this->config_options()
            ->pluck('options_value', 'options_key')
            ->toArray();

        return $options;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = json_encode($value);
    }

    public function getValueAttribute($value)
    {
        return json_decode($value);
    }


    /**
     * 统一保存配置方法
     * @param $data
     * @param false $group
     * @return bool
     */
    public static function saveConfig($data, $group = false)
    {
        // $group没有传递则尝试从$data中获取
        if (!$group){
            $group = isset($data['group']) ? $data['group'] : false;
        }
        // 如果分组名不存在返回false
        if ($group == false) return false;

        $fields = self::where('group', $group)->get()->toArray();
        $cache_data = [];
        foreach ($fields as $field) {
            $field_key = $field['key'];
            $value = isset($data[$field_key]) ? $data[$field_key]: '';
            if (is_array($value)) $value = array_filter($value);
            // 给开关组件bug,添加判断
            if ($field['field_type'] == 'switch'){
                $value = $value == 'on' ? 1 : 0;
            }
            // 为了让他走修改器,必须使用模型操作数据
            $edit_field = self::where('group', $group)->where('key', $field_key)->first();
            $edit_field->value = $value;
            $edit_field->save();
            $cache_data[] = [$field_key => $value];
        }
        // 每次修改数据都重新生成缓存
        Cache::put(self::cache_prefix . $group, $cache_data, 60 * 24);
        return true;
    }

    /**
     * 动态加载数据库中的配置
     * @param string $group_name 配置分组名
     */
    public static function loadConfigByGroup(string $group_name)
    {
        if (Cache::has(self::cache_prefix . $group_name)){
            $group_config = Cache::get(self::cache_prefix . $group_name);
            config([$group_name => $group_config]);
        }else{
            try {
                $group_config = self::where('group', $group_name)->pluck('value', 'key');
                if ($group_config->isNotEmpty()){
                    $group_config = $group_config->toArray();
                    config([$group_name => $group_config]);
                    Cache::put(self::cache_prefix . $group_name, $group_config);
                }
            }catch(\Exception $exception){
                Log::error($exception->getMessage());
            }
        }


    }
}