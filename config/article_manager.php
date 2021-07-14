<?php


return [
    /*
     * 文章图片是否压缩
     */
    'is_resize' => true,

    /*
     * 图片压缩长宽,is_resize设置成true才会生效
     * 填写 0 默认会忽略掉
     * 暂时只有go-fastdfs(dropbox)文件存储中可以使用
     */
    'resize_w_h' => [
        'width' => 480,
        'height' => 0
    ],

    /*
     * 配置基于is_resize
     * 图片小于设置宽度,不进行压缩
     */
    'not_resize_width' => 200
];