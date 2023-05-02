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

if (!function_exists ("app")) {
    function app ()
    {
        return \presty\Container::getInstance ()->getClass('app');
    }
}

if (!function_exists ("getClass")) {
    function getClass ($name)
    {
        return app()->getClass($name);
    }
}

if (!function_exists ("container_instance")) {
    function container_instance ($name,$instance)
    {
        return app()->instance($name,$instance);
    }
}

if (!function_exists ("container_newInstance")) {
    function container_newInstance ($name,$autoSave = false)
    {
        return app()->newInstance($name,$autoSave);
    }
}

if (!function_exists ("container_make")) {
    function container_make ($key)
    {
        return app()->make($key);
    }
}

if (!function_exists ("session_get")) {
    function session_get ($sessionName,$sessionValue = "")
    {
        return Session::get($sessionName,$sessionValue);
    }
}

if (!function_exists ("session_set")) {
    function session_set ($sessionName,$sessionValue)
    {
        return Session::set($sessionName,$sessionValue);
    }
}

if (!function_exists ("session_unset")) {
    function session_unset ($sessionName)
    {
        return Session::unset($sessionName);
    }
}

if (!function_exists ("session_des")) {
    function session_des ($sessionName)
    {
        return Session::des($sessionName);
    }
}

if (!function_exists ("session_isset")) {
    function session_isset ($sessionName,$sessionValue = "")
    {
        return Session::isset($sessionName,$sessionValue);
    }
}

if (!function_exists ("session_empty")) {
    function session_empty ($sessionName)
    {
        return Session::empty($sessionName);
    }
}

if (!function_exists ("cookie_get")) {
    function cookie_get ($sessionName,$sessionValue = "")
    {
        return Cookie::get($sessionName,$sessionValue);
    }
}

if (!function_exists ("cookie_set")) {
    function cookie_set ($cookieName, $cookieValue, $expire = 0, $path = "", $domain = "",$secure = false,$httponly = false)
    {
        return Cookie::set($cookieName,$cookieValue,$expire,$path,$domain,$secure,$httponly);
    }
}

if (!function_exists ("cookie_des")) {
    function cookie_des ($cookieName)
    {
        return Cookie::des($cookieName);
    }
}

if (!function_exists ("cookie_isset")) {
    function cookie_isset ($cookieName,$cookieValue = "")
    {
        return Cookie::isset($cookieName,$cookieValue);
    }
}

if (!function_exists ("cookie_empty")) {
    function cookie_empty ($cookieName)
    {
        return Cookie::empty($cookieName);
    }
}

if (!function_exists ("redirect")) {
    function redirect ($url = "", $remember = false)
    {
        return Redirect::redirect ($url, $remember);
    }
}

if (!function_exists ("redirect_backoff")) {
    function redirect_backoff ($remember = false)
    {
        return Redirect::backoff ($remember);
    }
}

if (!function_exists ("container_get")) {
    function container_get ($key)
    {
        app()->get ($key);
    }
}

if (!function_exists ("container_bind")) {
    function container_bind ($key, $value)
    {
        app()->bind ($key, $value);
    }
}

if (!function_exists ("container_has")) {
    function container_has ($value)
    {
        app()->isValueSet ($value);
    }
}

if (!function_exists ("middleWare_listening")) {
    function middleWare_listening ($className = "", $functionName = "", $args = "")
    {
        return \presty\Facade\MiddleWare::listening ($className, $functionName, $args);
    }
}

if (!function_exists ("middleWare_getClassName")) {
    function middleWare_getClassName ($hookName, $returnString = false)
    {
        require_once DIR."MiddleWare.php";
        $hook = new presty\MiddleWare();
        return \presty\Facade\MiddleWare::getClassName ($hookName, $returnString);
    }
}

if (!function_exists ("middleWare_bind")) {
    function middleWare_bind ($hookName, $className)
    {
        app()->middleWare->bind ($hookName, $className);
    }
}

if (!function_exists ("getMainView")) {
    function getMainView ()
    {
        app()->make("middleWare")->getMainView();
    }
}

if (!function_exists ("globals")) {
    function globals ($vars)
    {
        if (isset($GLOBALS[$vars])) return $GLOBALS[$vars];
        else {
            $backtrace = debug_backtrace();
            $backtrace = array_shift($backtrace);
            new \ThrowError($backtrace['file'],$backtrace['line'],"EC100018");
        }

    }
}

if (!function_exists ("config")) {
    function config ()
    {
        return app()->newInstance("config");
    }
}

if (!function_exists ("get_config")) {
    function get_config ($name, $default = "")
    {
        return app()->newInstance("config")->get($name,$default);
    }
}

if (!function_exists ("get_all_config")) {
    function get_all_config ()
    {
        return app()->newInstance("config")->getAll();
    }
}

if (!function_exists ("getConfigFile")) {
    function getConfigFile ($filePath)
    {
        return app()->newInstance("config")->getFile($filePath);
    }
}

if (!function_exists ("env")) {
    function env ($name,$default = "")
    {
        return \presty\Env::get($name,$default);
    }
}

if (!function_exists ("model")) {
    function model ($modelClass)
    {
        return \Model::model($modelClass);
    }
}

if (!function_exists ("appPath")) {
    function appPath ()
    {
        return APP.app()->make("request")->controllerApp().DS;
    }
}

if (!function_exists ("appClass")) {
    function appClass ()
    {
        return "app\\".app()->make("request")->controllerApp();
    }
}

if (!function_exists ("parseGlobalUrl")) {
    function parseGlobalUrl (\presty\Request $request)
    {
        $parser = get_config('env.url_parser', 'presty\urlParser\Presty');
        $parser = new $parser;
        $url = $parser->init();
        $url = $parser->parse($url);
        $request->setUrl ($url);
        $parser->set($request);
        return is_array ($url);
    }
}

if (!function_exists ("parseUrl")) {
    function parseUrl ($url)
    {
        $parser = get_config('env.url_parser', 'presty\urlParser\Presty');
        $parser = new $parser;
        $url = $parser->parse($url);
        return $url;
    }
}

if (!function_exists ("varDumpArray")) {
    function varDumpArray ($array,$formatOutput = true)
    {
        if($formatOutput){
            echo "<pre>";
            var_dump ($array);
            echo "</pre>";
        }
        else var_dump ($array);
    }
}

if (!function_exists ("scanFiles")) {
    function scanFiles ($dir,$deepScan,$function)
    {
        $temp = scandir ($dir);
        foreach ($temp as $v) {
            $a = $dir . $v;
            if (is_dir ($a)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if($deepScan)scanDir ($a,$deepScan,$function);
                else continue;
            } else {
                $fileFullPath = $a;
                $fileName = $v;
                $function($fileFullPath,$fileName);

            }
        }
    }
}

if (!function_exists ("lang")) {
    function lang ($index = "")
    {
        if(empty($index)) return app()->newInstance("lang")->lang();
        else return app()->newInstance("lang")->lang()[$index];
    }
}

if (!function_exists ("response")) {
    function response ($data,$args = [],$code = 200)
    {
        $type = "View";
        return app()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("json")) {
    function json ($data,$args = [],$code = 200)
    {
        $type = "json";
        return app()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("jsonp")) {
    function jsonp ($data,$args = [],$code = 200)
    {
        $type = "jsonp";
        return app()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("html")) {
    function html ($data,$args = [],$code = 200)
    {
        $type = "html";
        return app()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("getRootPath")) {
    function getRootPath ()
    {
        return app()->getrootPath();
    }
}

if (!function_exists ("getSystemPath")) {
    function getSystemPath ()
    {
        return app()->systemPath();
    }
}

if (!function_exists ("getAppPath")) {
    function getAppPath ()
    {
        return app()->appPath();
    }
}

if (!function_exists ("getConfigPath")) {
    function getConfigPath ()
    {
        return app()->configPath();
    }
}

if (!function_exists ("getModuleGuidesPath")) {
    function getModuleGuidesPath ()
    {
        return app()->moduleGuidesPath();
    }
}

if (!function_exists ("getPublicPath")) {
    function getPublicPath ()
    {
        return app()->PublicPath();
    }
}

if (!function_exists ("getPageCacheStatus")) {
    function getPageCacheStatus ()
    {
        $request = app()->make("request");
        $pageName = $request->requestPage();
        $pagePath = $request->requestPagePath();
        if(file_exists (CACHE . "viewCache" . DS  . $pageName . "-" . md5_file ($pagePath) . get_config ("env.template_suffix"))){
            return 0;
        }
        elseif(file_exists (CACHE . "viewCache" . DS  . $pageName . "-" . md5_file ($pagePath) . get_config ("env.template_suffix")."-cache")) {
            return 1;
        }
        else return 2;
    }
}