<?php
namespace Encore\ArticleManager\Http\Tools;

use GuzzleHttp\Client;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;

final class Scraper
{

    protected $client;

    /**
     * 公众号文章图片可能的后缀
     * @var string[]
     */
    protected $allow_ext = [
        'jpg',
        'jpeg',
        'png',
        'gif'
    ];

    /**
     * 公众号图片搜索指定字符之后的内容为后缀
     * @var string
     */
    protected $search_ext = 'wx_fmt=';

    /**
     * 下载的图片保存的目录
     * @var string
     */
    protected $save_path = '/wx_article_images';

    /**
     * 采集规则
     * 数组里第一个元素为css过滤规则,第二个元素为html或者text
     * @var array
     */
    protected $rules = [
        'content' => [
            '#js_content' , 'html'
        ],
    ];

    public function __construct(array $rules = [])
    {
        $this->client = new Client();
        $rules && $this->rules = array_merge($this->rules, $rules);
    }

    public function scrape($urls)
    {
        if (is_string($urls)){
            try {
                $content = $this->client->get($urls)->getBody()->getContents();
                $data = $this->processResponse($content);
                return [
                    'status' => true,
                    'message' => 'success',
                    'data' => $data
                ];
            }catch(\Exception $exception){
                return [
                    'status' => false,
                    'message' => $exception->getMessage()
                ];
            }

        }
        return ['status' => false, 'message' => '采集网址格式错误'];
    }

    private function processResponse(string $html)
    {
        $crawler = new Crawler($html);
        // ====================采集文章标题=================================
        $article_title = $crawler->filter('#activity-name')->text();
        $data['title'] = $article_title;
        // ===============================================================
        $content = $crawler->filter('#img-content')->html();
        $crawler2 = new Crawler();
        $crawler2->addHtmlContent($content,"utf-8");

        /**
        // ====================提取style中的背景图替换===========================
        // 获取section标签里面background-image标签里的url
        $background_images = [];
        $pattern = "|background-image: url\((.*)\);|U";
        preg_match_all($pattern, $content, $background_images_data);

        // 过滤掉空数组
        $background_images_data = array_filter($background_images_data);

        // 如果不为空,说明有背景图需要替代
        if (!empty($background_images_data)){
            foreach ($background_images_data[1] as $src){
                // 首尾去掉 "和'
                $src = trim($src,  '"');
                $src = trim($src, "'");
                $download = $this->downloadImages($src, false);
                $download && $background_images[] = $download;
            }
        }

        // 替换背景图
        foreach ($background_images as $background_image){
            $content = str_replace($background_image['old_src'], $background_image['replace_src'], $content);
        }
        // =========================背景图替换结束=============================
        */

        // =========================替换img标签里的data-src属性并替换成src========================
        // 获取img标签内容并替代
        $images = [];
        $crawler2->filter('#js_content img')->each(function(Crawler $el, $i) use(&$images) {
            $image_attr = $el->extract(['data-src', 'data-type']);
            $src = $image_attr[0][0];
            $ext = $image_attr[0][1];
            if ($src){
                $download = $this->downloadImages($src,true, $ext);
                $download && $images[] = $download;
            }
        });
        // 所有要替代的图片路径都在$images变量里
        // 循环替代文章中的图片
        foreach ($images as $image){
            $content = str_replace('data-src="'. $image['old_src'] . '"', 'src="' . $image['replace_src'] . '"', $content);
        }
        // ==========================替换image标签结束======================================

        // ==================过滤掉style===============================
        // 正则去除style属性
        $regex = '%style="[^"]+"%i';
        $content = preg_replace($regex, "", $content);
        $regex = "%style='[^']+'%i";
        $content = preg_replace($regex, "", $content);
        // ==================过滤style结束===============================
        $data['content'] = $content;


        return $data;
    }

    /**
     * @param string $src   图片路径
     * @param bool $is_resize 是否压缩
     * @param false $ext 有没有后缀名
     * @return array|false
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function downloadImages(string $src,bool $is_resize, $ext = false)
    {
        $response = $this->client->request('get',$src);
        if ($response->getStatusCode() == 200){
            // 有后缀就直接取img标签里的后缀
            $ext ?: $ext = $this->getFileExt($src);
            $file_name = md5(time() . rand(100000, 999999)) . '.' . $ext;
            $filepath = $this->save_path . '/' . $file_name;
            $saved = Storage::disk('admin')->put($filepath, $response->getBody());
            if ($saved){
                // 替代路径的时候判断要不要压缩,默认压缩返回的是imagecache组件处理过的图片.
                // 后期考虑重写imagecache扩展的路由.使文件路径和fastdfs的风格保持一致
                $replace_src = $is_resize ?
                    route('imagecache', ['template' => 'w480', 'filename' => $file_name])
                    : Storage::disk('admin')->url($filepath);
                return [
                    'old_src' => $src,
                    'replace_src' => $replace_src
                ];
            }
        }
        return false;
    }


    /**
     * 获取公众号文章内图片的后缀
     * laravel 6.* 有坑,不能直接使用助手函数?
     * @param $src
     * @return string
     */
    protected function getFileExt($src)
    {
        $ext = Str::after($src, $this->search_ext);
        if (!in_array($ext, $this->allow_ext)){
            return 'tmp';
        }
        return $ext;
    }

}

