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

if (!function_exists ("app")) {
    function app ()
    {
        return \presty\Container::getInstance ();
    }
}

if (!function_exists ("getClass")) {
    function getClass ($name)
    {
        return \presty\Container::getInstance ()->getClass($name);
    }
}

if (!function_exists ("container_instance")) {
    function container_instance ($name,$instance)
    {
        return \presty\Container::getInstance ()->instance($name,$instance);
    }
}

if (!function_exists ("container_make")) {
    function container_make ($name,$autoSave = false)
    {
        return \presty\Container::getInstance ()->make($name,$autoSave);
    }
}

if (!function_exists ("app_make")) {
    function app_make ($key)
    {
        return \presty\Container::getInstance ()->make($key);
    }
}

if (!function_exists ("session_get")) {
    function session_get ($sessionName,$sessionValue = "")
    {
        return \presty\Container::getInstance ()->make("session")->get($sessionName,$sessionValue);
    }
}

if (!function_exists ("session_set")) {
    function session_set ($sessionName,$sessionValue)
    {
        return \presty\Container::getInstance ()->make("session")->set($sessionName,$sessionValue);
    }
}

if (!function_exists ("session_unset")) {
    function session_unset ($sessionName)
    {
        return \presty\Container::getInstance ()->make("session")->unset($sessionName);
    }
}

if (!function_exists ("session_des")) {
    function session_des ($sessionName)
    {
        return \presty\Container::getInstance ()->make("session")->des($sessionName);
    }
}

if (!function_exists ("session_isset")) {
    function session_isset ($sessionName,$sessionValue = "")
    {
        return \presty\Container::getInstance ()->make("session")->isset($sessionName,$sessionValue);
    }
}

if (!function_exists ("session_empty")) {
    function session_empty ($sessionName)
    {
        return \presty\Container::getInstance ()->make("session")->empty($sessionName);
    }
}

if (!function_exists ("cookie_get")) {
    function cookie_get ($sessionName,$sessionValue = "")
    {
        return \presty\Container::getInstance ()->make("cookie")->get($sessionName,$sessionValue);
    }
}

if (!function_exists ("cookie_set")) {
    function cookie_set ($cookieName, $cookieValue, $expire = 0, $path = "", $domain = "",$secure = false,$httponly = false)
    {
        return \presty\Container::getInstance ()->make("cookie")->set($cookieName,$cookieValue,$expire,$path,$domain,$secure,$httponly);
    }
}

if (!function_exists ("cookie_des")) {
    function cookie_des ($cookieName)
    {
        return \presty\Container::getInstance ()->make("cookie")->des($cookieName);
    }
}

if (!function_exists ("cookie_isset")) {
    function cookie_isset ($cookieName,$cookieValue = "")
    {
        return \presty\Container::getInstance ()->make("cookie")->isset($cookieName,$cookieValue);
    }
}

if (!function_exists ("cookie_empty")) {
    function cookie_empty ($cookieName)
    {
        return \presty\Container::getInstance ()->make("cookie")->empty($cookieName);
    }
}

if (!function_exists ("redirect")) {
    function redirect ($url = "", $remember = false)
    {
        return \presty\Container::getInstance ()->make("redirect")->redirect ($url, $remember);
    }
}

if (!function_exists ("redirect_backoff")) {
    function redirect_backoff ($remember = false)
    {
        return \presty\Container::getInstance ()->make("redirect")->backoff ($remember);
    }
}

if (!function_exists ("container_make")) {
    function container_make ($key)
    {
        \presty\Container::getInstance ()->make ($key);
    }
}

if (!function_exists ("container_bind")) {
    function container_bind ($key, $value)
    {
        \presty\Container::getInstance ()->bind ($key, $value);
    }
}

if (!function_exists ("container_get")) {
    function container_get ($make)
    {
        \presty\Container::getInstance ()->get ($make);
    }
}

if (!function_exists ("middleWare_listening")) {
    function middleWare_listening ($className = "", $functionName = "", $args = "")
    {
        return \presty\Container::getInstance ()->makeAndSave("middleWare")->listening ($className, $functionName, $args);
    }
}

if (!function_exists ("middleWare_getClassName")) {
    function middleWare_getClassName ($hookName, $returnString = false)
    {
        require_once DIR."MiddleWare.php";
        $hook = new presty\MiddleWare();
        return \presty\Container::getInstance ()->makeAndSave("middleWare")->getClassName ($hookName, $returnString);
    }
}

if (!function_exists ("middleWare_bind")) {
    function middleWare_bind ($hookName, $className)
    {
        \presty\Container::getInstance ()->middleWare->bind ($hookName, $className);
    }
}

if (!function_exists ("getMainView")) {
    function getMainView ()
    {
        \presty\Container::getInstance ()->makeAndSave("middleWare")->getMainView();
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
        return \presty\Container::getInstance ()->makeAndSave("config");
    }
}

if (!function_exists ("get_config")) {
    function get_config ($name, $default = "")
    {
        return \presty\Container::getInstance ()->makeAndSave("config")->get($name,$default);
    }
}

if (!function_exists ("get_all_config")) {
    function get_all_config ()
    {
        return \presty\Container::getInstance ()->makeAndSave("config")->getAll();
    }
}

if (!function_exists ("getConfigFile")) {
    function getConfigFile ($filePath)
    {
        return \presty\Container::getInstance ()->makeAndSave("config")->getFile($filePath);
    }
}

if (!function_exists ("env")) {
    function env ($name,$default = "")
    {
        return \presty\Env::get($name,$default);
    }
}

if (!function_exists ("getModelClass")) {
    function getModelClass ($modelClass,$returnClaName = false)
    {
        return \presty\Container::getInstance ()->make("model")->getModelClass($modelClass,$returnClaName);
    }
}

if (!function_exists ("appPath")) {
    function appPath ()
    {
        return APP.\presty\Container::getInstance ()->makeAndSave("request")->controllerApp().DS;
    }
}

if (!function_exists ("appClass")) {
    function appClass ()
    {
        return "app\\".\presty\Container::getInstance ()->makeAndSave("request")->controllerApp();
    }
}

if (!function_exists ("parseGlobalUrl")) {
    function parseGlobalUrl (\presty\Request $request)
    {
        $parser = \presty\Container::getInstance ()->makeAndSave("config")->get('env.url_parser', 'presty\urlParser\Presty');
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
        $parser = \presty\Container::getInstance ()->makeAndSave("config")->get('env.url_parser', 'presty\urlParser\Presty');
        $parser = new $parser;
        $url = $parser->parse($url);
        return $url;
    }
}

if (!function_exists ("dumpArray")) {
    function dumpArray ($array,$formatOutput = true)
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
            $a = $dir  . DS . $v;
            if (is_dir ($a)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if($deepScan) scanFiles ($a,$deepScan,$function);
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
        if(empty($index)) return \presty\Container::getInstance ()->makeAndSave("lang")->lang();
        else return \presty\Container::getInstance ()->makeAndSave("lang")->lang()[$index];
    }
}

if (!function_exists ("response")) {
    function response ($data,$args = [],$code = 200)
    {
        $type = "View";
        return \presty\Container::getInstance ()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("json")) {
    function json ($data,$args = [],$code = 200)
    {
        $type = "json";
        return \presty\Container::getInstance ()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("jsonp")) {
    function jsonp ($data,$args = [],$code = 200)
    {
        $type = "jsonp";
        return \presty\Container::getInstance ()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("html")) {
    function html ($data,$args = [],$code = 200)
    {
        $type = "html";
        return \presty\Container::getInstance ()->make("Response")->create($data,$type,$code,$args);
    }
}

if (!function_exists ("getRootPath")) {
    function getRootPath ()
    {
        return \presty\Container::getInstance ()->getrootPath();
    }
}

if (!function_exists ("getSystemPath")) {
    function getSystemPath ()
    {
        return \presty\Container::getInstance ()->systemPath();
    }
}

if (!function_exists ("getAppPath")) {
    function getAppPath ()
    {
        return \presty\Container::getInstance ()->appPath();
    }
}

if (!function_exists ("getConfigPath")) {
    function getConfigPath ()
    {
        return \presty\Container::getInstance ()->configPath();
    }
}

if (!function_exists ("getModuleGuidesPath")) {
    function getModuleGuidesPath ()
    {
        return \presty\Container::getInstance ()->moduleGuidesPath();
    }
}

if (!function_exists ("getPublicPath")) {
    function getPublicPath ()
    {
        return \presty\Container::getInstance ()->PublicPath();
    }
}

if (!function_exists ("getPageCacheStatus")) {
    function getPageCacheStatus ()
    {
        $request = \presty\Container::getInstance ()->makeAndSave("request");
        $pageName = $request->requestPage();
        $pagePath = $request->requestPagePath();
        if(empty($pagePath)) return 0;
        if(file_exists (CACHE . "viewCache" . DS  . $pageName . "-" . md5_file ($pagePath) . \presty\Container::getInstance ()->makeAndSave("config")->get ("env.template_suffix"))){
            return 0;
        }
        elseif(file_exists (CACHE . "viewCache" . DS  . $pageName . "-" . md5_file ($pagePath) . \presty\Container::getInstance ()->makeAndSave("config")->get ("env.template_suffix")."-cache")) {
            return 1;
        }
        else return 2;
    }
}

if(!function_exists("checkArgs")){
    function checkArgs($args){
        if(is_array($args)){
            foreach ($args as $arg){
                checkArgs($arg);
            }
        }else{
            if(!array_key_exists($args,\presty\Container::getInstance ()->makeAndSave("request")->controllerArgs())) die(json_encode(["code" => -101, "msg" => "Insufficient parameters", "data" => []],JSON_UNESCAPED_UNICODE));
        }
    }
}

if(!function_exists("createDs")){
    function createDs($salt){
        $now = time();
        $prefix = "r=".$now."q=".md5($salt).$salt;
        $secret = md5(md5($prefix."s".md5(md5($prefix).$salt)).$salt);
        return $now.".".rand(100000,999999).".".$secret;
    }
}


if(!function_exists("curl_send")){
    function curl_send($url,$method = "GET",$data = array())
    {
        $ds = createDs(\presty\Container::getInstance ()->makeAndSave("config")->get("verify.ds.salt"));
        header("DS:".$ds);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if(!function_exists("curl_get")){
    function curl_get($url,$data = array())
    {
        $ds = createDs(\presty\Container::getInstance ()->makeAndSave("config")->get("verify.ds.salt"));
        header("DS:".$ds);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if(!function_exists("curl_post")){
    function curl_post($url,$data = array())
    {
        $ds = createDs(\presty\Container::getInstance ()->makeAndSave("config")->get("verify.ds.salt"));
        header("DS:".$ds);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}

if(!function_exists("AESencrypt")){
    function AESencrypt($data)
    {
        if ($data== null || empty($data)) {
            return $data;
        }
        $key = "71d9003912ad4a91";//秘钥必须为：8/16/32位
        $iv = "a719bbe6c0c4f7f9";
        $base64_str = base64_encode(json_encode($data));
        $encrypted = openssl_encrypt($base64_str, "aes-128-cbc", $key, OPENSSL_ZERO_PADDING, $iv);
        return base64_encode($encrypted);
    }
}

if(!function_exists("AESdecrypt")){
    function AESdecrypt($data)
    {
        if ($data== null || empty($data)) {
            return $data;
        }
        $encrypted = base64_decode($data);
        $key = "71d9003912ad4a91";//秘钥必须为：8/16/32位
        $iv = "a719bbe6c0c4f7f9";
        $decrypted = openssl_decrypt($encrypted, 'aes-128-cbc', $key, OPENSSL_ZERO_PADDING, $iv);
        return json_decode(base64_decode($decrypted), true);
    }
}

if(!function_exists("debug")){
    function debug($print)
    {
        var_dump($print);
        die();
    }
}