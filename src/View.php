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

namespace presty;

use presty\Exception\NotFoundException;

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
        \presty\Container::getInstance ()->make("middleWare")->getClassName ('viewInit')->listening ();
        \presty\Container::getInstance ()->set ("hasBeenRun", "view", " - [".(new \DateTime())->format("Y-m-d H:i:s:u")."] => View_Init");
        if(\presty\Container::getInstance ()->getClass("Response") == false){
            $content = "";
            $this->response = new Response;
            $this->response = $this->response->create ($content, \presty\Container::getInstance ()->make("config")->get ('view.response_type', 'View'), 200, [$this]);
            $this->response->setContent($content);
            \presty\Container::getInstance ()->instance("Response",$this->response);
        }
    }

    public function engine ($engine = null)
    {
        if (empty($this->engine)) {
            if (is_string ($engine) && !empty($engine)) {
                $engine = new $engine;
            } elseif (is_null ($engine)) {
                if (class_exists ($class = \presty\Container::getInstance ()->make("config")->get ('env.template_engine', 'presty\View\Driver\View')))
                    $engine = new $class;
                else new NotFoundException($class,__FILE__,__LINE__,"EC100009");
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
        if(is_null ($response)) $response = \presty\Container::getInstance ()->getClass ("Response");
        if (\presty\Container::getInstance ()->make("config")->get('env.render_auto_clean',false) && $this->firstRender) ob_clean ();
        if (empty($content) || $content == "%self%") $content = $this->content;
        if (\presty\Container::getInstance ()->make("config")->get('env.auto_xss_protect',false)) {
            $antiXss = new \presty\Response\Driver\AntiXSS();
            $content = $antiXss->antiXss ($content);
        }
        if ($this->firstRender) $this->firstRender = false;
        if (!is_bool ($name = array_search ($this, Container::getInstance ()->makeAndSave ("viewQueue")->getQueue ()))) Container::getInstance ()->makeAndSave ("viewQueue")->set ($name, $this);
        \presty\Container::getInstance ()->make("middleWare")->getClassName ('beforeRender')->listening ();
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
        $allowToLoad = false;
        $content = "";
        $app = array_filter (explode ("/", $path))[0];
        $path = str_replace ("/",DS,$path);
        if (file_exists (APP . $app . ".php")) {
            $allowToLoad = empty($result = require_once (APP . $app . ".php")) || $result;
        } else {
            $allowToLoad = true;
        }
        $fullPath = APP . $path . \presty\Container::getInstance ()->make("config")->get ('view.file_suffix', '.html');
        \presty\Container::getInstance ()->make("request")->setRequestPage($path,$fullPath);
        if ($allowToLoad) {
            if(!\presty\Env::get('system_debug_mode')) {
                if (file_exists (CACHE . "viewCache" . DS . $path . "-" . md5_file ($fullPath) . \presty\Container::getInstance ()->make("config")->get ("view.template_suffix"))) {
                    $content = file_get_contents (CACHE . "viewCache" . DS  . $path . "-" . md5_file ($fullPath) . \presty\Container::getInstance ()->make("config")->get ("view.template_suffix"));
                } else {
                    $content = file_get_contents ($fullPath);
                    if(!is_dir (PUBLICDIR)){
                        new NotFoundException($class,__FILE__,__LINE__,"EC100034","/public目录不存在");
                    }
                    if(!is_dir (CACHE)){
                        mkdir (CACHE);
                    }
                    if(!is_dir (CACHE . "viewCache")){
                        mkdir (CACHE . "viewCache");
                    }
                    if(!is_dir (CACHE . "viewCache" . DS  . dirname ($path))){
                        mkdir (CACHE . "viewCache" . DS  . dirname ($path));
                    }
                    $cacheFile = fopen(CACHE . "viewCache" . DS  . $path . "-" . md5_file ($fullPath) . \presty\Container::getInstance ()->make("config")->get ("view.template_suffix")."-cache","w");
                    fwrite ($cacheFile,$content);
                    fclose ($cacheFile);
                }
            } else $content = file_get_contents ($fullPath);
        }
        else $content = file_get_contents (TEMPLATES . \presty\Container::getInstance ()->make("config")->get('view.access_denied_page') . \presty\Container::getInstance ()->make("config")->get('template_suffix'));
        $response = \presty\Container::getInstance ()->make("Response")->create ("", \presty\Container::getInstance ()->make("config")->get ('view.response_type', 'View'), 200, [$this]);
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
        $antiXss = new \presty\AntiXSS\AntiXSS();
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