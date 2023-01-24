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

class View
{

    protected $data = [];

    protected $url = "";

    protected $firstRender = true;

    protected $content = "";

    protected $engine = "";

    protected $protectInfo = false;

    public function __construct ()
    {
        hook_getClassName ('viewInit')->transfer ();
        if(getClass("response") == false){
            $content = "";
            $this->response = new Response;
            $this->response = $this->response->create ($content, config ('response_type', 'view'), 200, [$this]);
            $this->response->setContent($content);
            app()->instance("response",$this->response);
        }
    }

    public function engine ($engine = null)
    {
        if (empty($this->engine)) {
            if (is_string ($engine) && !empty($engine)) {
                $engine = new $engine;
            } elseif (is_null ($engine)) {
                if (class_exists ($class = config ('template_engine', '\startphp\view\Start')))
                    $engine = new $class;
                else \ThrowError::throw (__FILE__, __LINE__, "EC100019");
            }
            return $engine;
        } else return $this->engine;
    }

    public function assign ($key, $value = ""): View
    {
        if (is_array ($key)) {
            $this->data = array_merge ($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function render ($content = "",$response = null)
    {
        if ($this->protectInfo) $content = "";
        if(is_null ($response)) $response = getClass ("response");
        if (config('render_auto_clean',false) && $this->firstRender) ob_clean ();
        if (empty($content) || $content == "%self%") $content = $this->content;
        if (config('auto_xss_protect',false)) {
            $antiXss = new \startphp\format\AntiXSS\AntiXSS();
            $content = $antiXss->antiXss ($content);
        }
        if ($this->firstRender) $this->firstRender = false;
        if (!is_bool ($name = array_search ($this, container_newInstance ("viewQueue")->getQueue ()))) container_newInstance ("viewQueue")->set ($name, $this);
        hook_getClassName ('beforeRender')->transfer ();
        ob_start ();
        $this->output ($response,$content, true);
        $this->protectInfo = false;
        return $response;
    }

    public function toggle ($cleanCache = true): View
    {
        ob_clean ();
        ob_start ();
        echo $this->content;
        return $this;
    }

    public function filter ($content = "", $returnContent = false)
    {
        if ($this->protectInfo) {
            $content = "";
            $returnContent = false;
        }
        if (empty($content)) $content = $this->content;
        $this->content = $this->engine ()->parse ($content, $this->data);
        return $returnContent ? $this->content : $this;
    }

    public function getFileContent ($path, $returnContent = false)
    {
        if ($this->protectInfo) return $this;
        global $config;
        $allowToLoad = false;
        $content = "";
        $app = array_filter (explode ("/", $path))[0];
        if (file_exists (APP . $app . ".php")) {
            $allowToLoad = empty($result = require_once (APP . $app . ".php")) || $result;
        } else {
            $allowToLoad = true;
        }
        $path = APP . $path . config ('file_suffix', '.html');
        if ($allowToLoad) $content = file_get_contents ($path);
        else $content = file_get_contents (TEMPLATES . config('access_denied_page') . config('template_suffix'));
        $response = app()->make("response")->create ("", config ('response_type', 'view'), 200, [$this]);
        $response->setContent($content);
        return $content;
    }

    public function save (): View
    {
        $this->content = ob_get_clean ();
        return $this;
    }

    public function setContent ($content): View
    {
        $this->content = $content;
        return $this;
    }

    public function getContent ()
    {
        return $this->content;
    }

    public function output ($response, $content = "", $printNull = false)
    {
        if (!$printNull) {
            if (empty($content)) $content = $this->content;
        }
        $response->setContent($content);
        return $response;
    }

    public function antiXss ($content = "")
    {
        if (empty($content)) $content = $this->content;
        $antiXss = new \startphp\AntiXSS\AntiXSS();
        $this->content = $antiXss->antiXss ($content);
    }

    public function setProtect ($opinion = true): View
    {
        $this->protectInfo = $opinion;
        return $this;
    }

    public function getResponse ()
    {
        return $this->response;
    }
}