<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Confidire All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace presty\Response\Driver;

use presty\Response;

class Json extends Response
{
    protected $vars = ['json_handler' => JSON_UNESCAPED_UNICODE];

    public function __construct ($content = "",$code = 200) {
        $this->fileType = "text/html; charset=UTF-8";
        $this->init($content,$code);
    }

    public function handle ($content = "")
    {
        if(empty($content)) $content = $this->content;
        $this->content = json_encode ($content,$this->vars['json_handler']);
        return $this;
    }
}