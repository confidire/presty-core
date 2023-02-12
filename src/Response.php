<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Tomanday All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace presty;

use presty\Facade\Request;

class Response
{
    protected $type = "html";

    protected $code = 200;

    protected $content = "";

    protected $charset = "utf-8";

    protected $fileType = "text/html";

    protected $header = [];

    protected $request;

    protected $vars = [];

    public function init ($content = "", $code = 200){
        $this->content = $content;
        $this->code = $code;
        $this->request = new Request;
        if (!function_exists('getallheaders'))
        {
            function getallheaders()
            {
                $headers = array ();
                foreach ($_SERVER as $name => $value)
                {
                    if (substr($name, 0, 5) == 'HTTP_')
                    {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
                return $headers;
            }
        }
        $this->header = getallheaders();
    }

    public function create ($data = '', $type = 'html', $code = 200,$args = [])
    {
        $className = false !== strpos($type, '\\') ? $type : '\\presty\\Response\\Driver\\' . ucfirst(strtolower($type));
        $args = array_merge ($args,[$data, $code]);
        return Container::getInstance()->invokeClass($className, $args);
    }

    public function send (): void
    {
        if (!headers_sent() && !empty($this->header)) {
            http_response_code($this->code);
            foreach ($this->header as $key => $value) {
                header($key . (!is_null($value) ? ':' . $value : ''));
            }
        }
        echo $this->content;
    }

    public function handle ($content = "")
    {
        if(empty($content)) $content = $this->content;
        $this->content = $content;
        return $this;
    }

    public function header ($header)
    {
        if (is_array ($header)) {
            $this->header = array_merge ($this->header,$header);
        }
    }

    public function vars($vars)
    {
        $this->vars = array_merge ($this->vars,$vars);
        return $this;
    }

    public function setContent ($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent ()
    {
        return $this->content;
    }
}