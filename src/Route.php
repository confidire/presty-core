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
class Route {
    public function set ($alias,$origin = "",$method = "*") {
        global $route;
        if(is_array ($method)){
            foreach ($method as $k => $v) {
                $method[$k] = strtoupper ($k);
            }
        }elseif(is_string ($method))
        {
            $method = strtoupper ($method);
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($alias) && !empty($origin)){
            $this->newRule (
                is_array ($alias) ? $alias : [$alias],
                is_array ($origin) ? $origin : [$origin],
                is_array ($method) ? $method : [$method]
            )->parse ($url,$route,
                function($tempValue,$combine){
                    header("Location: ".$tempValue.$combine);
                },
                function ($tempValue,$combine){
                    header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                });
            return $route;
        }
    }

    public function get ($alias,$origin = "") {
        global $route;
        $method = [];
        is_string ($alias) ? $data = [$alias] : $data = $origin;
        for ($i = 0;$i< count($data);$i++){
            $method[] = 'GET';
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($alias) && !empty($origin)){
            $this->newRule (
                is_array ($alias) ? $alias : [$alias],
                is_array ($origin) ? $origin : [$origin],
                $method
            )->parse ($url,$route,
                function($tempValue,$combine){
                    header("Location: ".$tempValue.$combine);
                },
                function ($tempValue,$combine){
                    header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                });
            return $route;
        }
    }

    public function post ($alias,$origin = "") {
        global $route;
        $method = [];
        is_string ($alias) ? $data = [$alias] : $data = $origin;
        for ($i = 0;$i< count($data);$i++){
            $method[] = 'POST';
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($alias) && !empty($origin)){
            $this->newRule (
                is_array ($alias) ? $alias : [$alias],
                is_array ($origin) ? $origin : [$origin],
                $method
            )->parse ($url,$route,
                function($tempValue,$combine){
                    header("Location: ".$tempValue.$combine);
                },
                function ($tempValue,$combine){
                    header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                });
            return $route;
        }
    }

    public function put ($alias,$origin = "") {
        global $route;
        $method = [];
        is_string ($alias) ? $data = [$alias] : $data = $origin;
        for ($i = 0;$i< count($data);$i++){
            $method[] = 'PUT';
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($alias) && !empty($origin)){
            $this->newRule (
                is_array ($alias) ? $alias : [$alias],
                is_array ($origin) ? $origin : [$origin],
                $method
            )->parse ($url,$route,
                function($tempValue,$combine){
                    header("Location: ".$tempValue.$combine);
                },
                function ($tempValue,$combine){
                    header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                });
            return $route;
        }
    }

    public function delete ($alias,$origin = "") {
        global $route;
        $method = [];
        is_string ($alias) ? $data = [$alias] : $data = $origin;
        for ($i = 0;$i< count($data);$i++){
            $method[] = 'DELETE';
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($alias) && !empty($origin)){
            $this->newRule (
                is_array ($alias) ? $alias : [$alias],
                is_array ($origin) ? $origin : [$origin],
                $method
            )->parse ($url,$route,
                function($tempValue,$combine){
                    header("Location: ".$tempValue.$combine);
                },
                function ($tempValue,$combine){
                    header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                });
            return $route;
        }
    }

    public function patch ($alias,$origin = "") {
        global $route;
        $method = [];
        is_string ($alias) ? $data = [$alias] : $data = $origin;
        for ($i = 0;$i< count($data);$i++){
            $method[] = 'PATCH';
        }
        $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        if(!empty($alias) && !empty($origin)){
            $this->newRule (
                is_array ($alias) ? $alias : [$alias],
                is_array ($origin) ? $origin : [$origin],
                $method
            )->parse ($url,$route,
                function($tempValue,$combine){
                    header("Location: ".$tempValue.$combine);
                },
                function ($tempValue,$combine){
                    header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                });
            return $route;
        }
    }

    public function newRule ($alias = [],$origin = [],$method = ['*'])
    {
        global $route;
        $backup = $data = array_combine ($alias,$origin);
        $i = 0;
        $file = file_get_contents (ROUTE . "route.php") or \ThrowError::throw(__FILE__, __LINE__, "EC100008");
        $pos = stripos ($file,"[");
        $head = substr ($file,0,$pos +2);
        $allowToWrite = false;
        foreach ($data as $k => $v) {
            if(!isset($route[$k])) {
                $allowToWrite = true;
                $route = array_merge ($route,[$k=>[$method[$i],$v]]);
                $content = "\"$k\" => [\"$method[$i]\",\"$v\"],\r\n";
                $data = $head.$content;
                $file = str_replace ($head,$data,$file);
            }
        }
        if($allowToWrite){
            $fileResource = fopen (ROUTE."route.php",'r+') or \ThrowError::throw(__FILE__, __LINE__, "EC100008");
            fwrite ($fileResource,$file);
        }
        $i++;
        return $this;
    }

    public function parse ($url,$route,$urlMatchFunction = "",$urlMismatchFunction = "")
    {
        if($url == "/index") $tempUrl = "/";
        else $tempUrl = $url;
        $path = parse_url ($tempUrl ?? $url);
        $info = array_filter (explode ("/", substr ($path['path'], 1)));
        foreach ($route as $key => $value) {
            $requestMethod = $value[0];
            if(stripos ($requestMethod,"|")) $requestMethod = explode ("|",$requestMethod);
            else $requestMethod = [$requestMethod];
            $tempValue = $value[1];
            $array = array_filter (explode ("/", $key));
            if(empty($array)) $array[] = "/";
            $combineKey = array_diff ($array, $info);
            $continue = true;
            $combine = "";
            if($combineKey[0] == $path['path']) $url = $tempValue;
            foreach ($combineKey as $k => $v) {
                if (is_bool (strpos ($v, "`"))) $continue = false;
                else $combineKey[$k] = substr ($v, 1);
            }
            if ($continue) {
                $combineKey = array_values($combineKey);
                $diff = array_values (array_diff ($info, $array));
                $combine = [];
                foreach ($combineKey as $k => $v) {
                    $combine[] = "$v=$diff[$k]";
                }
                $combine = "?".implode ("&",$combine);
            }
            if($combineKey[0] == $path['path']) {
                if (in_array ($_SERVER['REQUEST_METHOD'], $requestMethod) || in_array ("*", $requestMethod)) {
                    if (filter_var ($tempValue, FILTER_VALIDATE_URL) !== false) {
                        $urlMatchFunction($tempValue, $combine);
                    } else {
                        $urlMismatchFunction($tempValue, $combine);
                    }
                }
            }
        }
        return $this;
    }
}