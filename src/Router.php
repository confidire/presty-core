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

use presty\Exception\InvalidReturnException;
use presty\Exception\NotFoundException;
use presty\Facade\Template;

class Router
{

    protected $engine = null;

    protected $response = null;

    public function setEngine ($engine = null): Router
    {
        $this->engine = $engine ?? Container::getInstance ()->invokeClass(\presty\Container::getInstance ()->makeAndSave("config")->get('env.url_parser', 'presty\Router\Driver\Presty'));

        return $this;
    }

    public function getEngine ()
    {
        return $this->engine ?? false;
    }

    public function init ()
    {
        $query_file = $_SERVER["REQUEST_URI"];
        if ($query_file == "/") $query_file = "/index";
        if (is_file (PUBLICDIR . substr ($query_file, 1))) {
            $fInfo = finfo_open (FILEINFO_MIME_TYPE);
            $type = finfo_file ($fInfo, PUBLICDIR . substr ($query_file, 1));
            echo file_get_contents (PUBLICDIR . substr ($query_file, 1));
            header ("Content-type: $type");
            header ("Content-Disposition: attachment; filename= " . basename (PUBLICDIR . substr ($query_file, 1)));
            set_time_limit (10);
            return false;
        }
        \presty\Container::getInstance()->set("hasBeenRun","rInit"," - [".(new \DateTime())->format("Y-m-d H:i:s:u")."] => Router_Init");
        \presty\Container::getInstance ()->makeAndSave("middleWare")->getClassName ('routerInit')->listening ([$query_file]);
        return $query_file;
    }

    public function set (Request $request)
    {
        $url = $request->url();
        $query_file = $_SERVER["REQUEST_URI"];
        $pageContent = "";
        if ($query_file == "/") $query_file = "/index";
        if(file_exists(APP.ucfirst($url['app']).".php")){
            require_once(APP.ucfirst($url['app']).".php");
            $class = "app\\"."Entrance\\".ucfirst($url['app']);
            $opinion = (new $class)->entrance($url) ?? true;
            if(is_file(APP.ucfirst($url['app']).".php") && !$opinion){
                $this->response = \presty\Container::getInstance()->make("Response")->create (Template::getTemplateContent(\presty\Container::getInstance ()->makeAndSave("config")->get("env.access_denied_page","AccessDenied")), \presty\Container::getInstance ()->makeAndSave("config")->get ('view.response_type', 'View'), 404, [app ()->make("viewQueue")->getMainView ()]);
                return $this->response->handle();
            }
        }
        if (is_dir (APP . $url['app'] . DS . "config" . DS)) $this->scanFiles (APP . $url['app'] . DS . "config" . DS, true, function ($a, $v) use($url) {
            config()->overwrite(array_merge (get_all_config (), require_once (APP . $url['app'] . DS . "config" . DS . pathinfo ($v, PATHINFO_FILENAME) . ".php")));
        });
        $request->setUrl($url);
        if(file_exists (APP . $url['app'] . DS . "controller" . $url['path'] . $url['controller'] . ".php"))
            require_once (APP . $url['app'] . DS . "controller" . $url['path'] . $url['controller'] . ".php");
        else new NotFoundException(APP . $url['app'] . DS . "controller" . $url['path'] . $url['controller'] . ".php",__FILE__,__LINE__,"EC100024");
        $className = "\\" . "app" . "\\" . $url["app"] . "\\" . "controller" . "\\" . $url["controller"];
        $class = new $className;
        if (method_exists ($class, $url['function'])) {
            $mdwClass = \presty\Container::getInstance()->makeAndSave("middleWare");
            $middlewares = $mdwClass->parseFunctionAttributesMiddleWare($class,$url['function']);
            if(!empty($middlewares)){
                foreach ($middlewares as $middleware) {
                    if($middleware["args"]["call_type"] == "before" || $middleware["args"]["call_type"] == "class") {
                        $args = $middleware["args"];
                        unset ($args["call_type"],$args["name"]);
                        $result = $mdwClass->call ($class,$middleware["args"]["name"],$args);
                        if (!$result) exit();
                    }
                }
            }
            $pageContent = call_user_func_array ([$class, $url['function']], [$request]);
            if(!empty($middlewares)){
                foreach ($middlewares as $middleware) {
                    if($middleware["args"]["call_type"] == "after") {
                        $args = $middleware["args"];
                        unset ($args["call_type"],$args["name"]);
                        $result = $mdwClass->call ($class,$middleware["args"]["name"],$args);
                        if (!$result) exit();
                    }
                }
            }
        } elseif (method_exists ($class, "__call")) {
            $pageContent = call_user_func_array ([$class, '__call'], [$url['function'], [$request]]);
        } else {
            if(empty(\presty\Container::getInstance ()->makeAndSave("config")->get ('env.404_template',''))) new NotFoundException($className . "->" . $url['function']."()",__FILE__,__LINE__,"EC100010");
            else {
                $this->response = \presty\Container::getInstance()->make("Response")->create (Template::getTemplateContent(\presty\Container::getInstance ()->makeAndSave("config")->get('env.404_template','')), \presty\Container::getInstance ()->makeAndSave("config")->get ('view.response_type', 'View'), 404, [app ()->make("viewQueue")->getMainView ()]);
                return $this->response->handle();
            }
        }
        if (gettype ($pageContent) == "NULL") {
            new InvalidReturnException(lang()["controller_empty_return"],__FILE__,__LINE__);
        }
        elseif(is_string ($pageContent)) $this->response = \presty\Container::getInstance()->make("Response")->create ($pageContent, \presty\Container::getInstance ()->makeAndSave("config")->get ('view.response_type', 'View'), 200, [\presty\Container::getInstance()->make("View")]);
        else $this->response = $pageContent;
        \presty\Container::getInstance ()->makeAndSave("middleWare")->getClassName ('afterRouter')->listening ([$query_file, $url]);
        if(\presty\Env::get('system_debug_mode')) {
            return $this->response->handle ();
        }else{
            if(getPageCacheStatus () == 0) return $this->response->defaultHandle ();
            else return $this->response->handle ();
        }
    }
    function scanFiles ($dir,$deepScan,$function)
    {
        $temp = scandir ($dir);
        foreach ($temp as $v) {
            $a = $dir  . DS . $v;
            if (is_dir ($a)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if($deepScan) $this->scanFiles ($a,$deepScan,$function);
                else continue;
            } else {
                $fileFullPath = $a;
                $fileName = $v;
                $function($fileFullPath,$fileName);

            }
        }
    }
}

interface RouterInterface {
    public function parse ($url);
}