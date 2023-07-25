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

class Jsonp extends Response
{
    protected $vars = [
        'json_handler' => JSON_UNESCAPED_UNICODE,
        'var_jsonp_handler'     => 'callback',
        'default_jsonp_handler' => 'jsonpReturn',
    ];

    public function __construct ($content = "",$code = 200) {
        $this->init($content,$code);
    }

    public function handle ($content = "")
    {
        $jsonpHandler = $this->vars['var_jsonp_handler'];
        $handler = !empty($jsonpHandler) ? $jsonpHandler : $this->vars['default_jsonp_handler'];
        $data = json_encode($content, $this->vars['json_handler']);
        $this->content = $handler . '(' . $data . ');';
        return $this;
    }
}