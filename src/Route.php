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
use presty\Exception\RunTimeException;

class Route {

    // public function set ($alias,$origin = "",$method = "*") {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     if(is_array ($method)){
    //         foreach ($method as $k => $v) {
    //             $method[$k] = strtoupper ($k);
    //         }
    //     }elseif(is_string ($method))
    //     {
    //         $method = strtoupper ($method);
    //     }
    //     $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    //     $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     if(!empty($alias) && !empty($origin)){
    //         $this->newRule (
    //             is_array ($alias) ? $alias : [$alias],
    //             is_array ($origin) ? $origin : [$origin],
    //             is_array ($method) ? $method : [$method]
    //         )->parse ($url,$route,
    //             function($tempValue,$combine){
    //                 header("Location: ".$tempValue.$combine);
    //             },
    //             function ($tempValue,$combine){
    //                 header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
    //             });
    //         return $route;
    //     }
    //     return false;
    // }

    // public function get ($alias,$origin = "") {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     $method = [];
    //     is_string ($alias) ? $data = [$alias] : $data = $origin;
    //     for ($i = 0;$i< count($data);$i++){
    //         $method[] = 'GET';
    //     }
    //     $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    //     $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     if(!empty($alias) && !empty($origin)){
    //         $this->newRule (
    //             is_array ($alias) ? $alias : [$alias],
    //             is_array ($origin) ? $origin : [$origin],
    //             $method
    //         )->parse ($url,$route,
    //             function($tempValue,$combine){
    //                 header("Location: ".$tempValue.$combine);
    //             },
    //             function ($tempValue,$combine){
    //                 header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
    //             });
    //         return $route;
    //     }
    //     return false;
    // }

    // public function post ($alias,$origin = "") {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     $method = [];
    //     is_string ($alias) ? $data = [$alias] : $data = $origin;
    //     for ($i = 0;$i< count($data);$i++){
    //         $method[] = 'POST';
    //     }
    //     $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    //     $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     if(!empty($alias) && !empty($origin)){
    //         $this->newRule (
    //             is_array ($alias) ? $alias : [$alias],
    //             is_array ($origin) ? $origin : [$origin],
    //             $method
    //         )->parse ($url,$route,
    //             function($tempValue,$combine){
    //                 header("Location: ".$tempValue.$combine);
    //             },
    //             function ($tempValue,$combine){
    //                 header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
    //             });
    //         return $route;
    //     }
    //     return false;
    // }

    // public function put ($alias,$origin = "") {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     $method = [];
    //     is_string ($alias) ? $data = [$alias] : $data = $origin;
    //     for ($i = 0;$i< count($data);$i++){
    //         $method[] = 'PUT';
    //     }
    //     $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    //     $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     if(!empty($alias) && !empty($origin)){
    //         $this->newRule (
    //             is_array ($alias) ? $alias : [$alias],
    //             is_array ($origin) ? $origin : [$origin],
    //             $method
    //         )->parse ($url,$route,
    //             function($tempValue,$combine){
    //                 header("Location: ".$tempValue.$combine);
    //             },
    //             function ($tempValue,$combine){
    //                 header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
    //             });
    //         return $route;
    //     }
    //     return false;
    // }

    // public function delete ($alias,$origin = "") {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     $method = [];
    //     is_string ($alias) ? $data = [$alias] : $data = $origin;
    //     for ($i = 0;$i< count($data);$i++){
    //         $method[] = 'DELETE';
    //     }
    //     $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    //     $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     if(!empty($alias) && !empty($origin)){
    //         $this->newRule (
    //             is_array ($alias) ? $alias : [$alias],
    //             is_array ($origin) ? $origin : [$origin],
    //             $method
    //         )->parse ($url,$route,
    //             function($tempValue,$combine){
    //                 header("Location: ".$tempValue.$combine);
    //             },
    //             function ($tempValue,$combine){
    //                 header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
    //             });
    //         return $route;
    //     }
    //     return false;
    // }

    // public function patch ($alias,$origin = "") {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     $method = [];
    //     is_string ($alias) ? $data = [$alias] : $data = $origin;
    //     for ($i = 0;$i< count($data);$i++){
    //         $method[] = 'PATCH';
    //     }
    //     $scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    //     $url = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //     if(!empty($alias) && !empty($origin)){
    //         $this->newRule (
    //             is_array ($alias) ? $alias : [$alias],
    //             is_array ($origin) ? $origin : [$origin],
    //             $method
    //         )->parse ($url,$route,
    //             function($tempValue,$combine){
    //                 header("Location: ".$tempValue.$combine);
    //             },
    //             function ($tempValue,$combine){
    //                 header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
    //             });
    //         return $route;
    //     }
    //     return false;
    // }

    // public function newRule ($alias = [],$origin = [],$method = ['*'])
    // {
    //     if(!app()->has("route")) return false;
    //     else $route = app()->has("route");
    //     $backup = $data = array_combine ($alias,$origin);
    //     $i = 0;
    //     $file = file_get_contents (ROUTE . "route.php") or new RunTimeException("file_get_contents函数执行出错",__FILE__,__LINE__);
    //     $pos = stripos ($file,"[");
    //     $head = substr ($file,0,$pos +2);
    //     $allowToWrite = false;
    //     foreach ($data as $k => $v) {
    //         if(!isset($route[$k])) {
    //             $allowToWrite = true;
    //             $route = array_merge ($route,[$k=>[$method[$i],$v]]);
    //             $content = "\"$k\" => [\"$method[$i]\",\"$v\"],\r\n";
    //             $data = $head.$content;
    //             $file = str_replace ($head,$data,$file);
    //         }
    //     }
    //     if($allowToWrite){
    //         $fileResource = fopen (ROUTE."route.php",'r+') or new RunTimeException("fopen函数执行出错",__FILE__,__LINE__);
    //         fwrite ($fileResource,$file);
    //     }
    //     $i++;
    //     return $this;
    // }

    // public function parse ($url,$route,$urlMatchFunction = "",$urlMismatchFunction = "")
    // {
    //     $path = parse_url ($url);
    //     $info = array_filter (explode ("/", substr ($path['path'], 1)));
    //     if(array_key_exists("query",$path)) $args = "?".$path["query"];  
    //     else $args = "";
    //     if(array_key_exists($path['path'],$route)){
    //         $key = $path['path'];
    //         $value = $route[$key];
    //         if(app()->make("request")->method() == $value[0] || $value[0] == "*"){
    //             if (filter_var($path, FILTER_VALIDATE_URL) !== false){
    //                 $urlMatchFunction($value[1],$args);
    //             }else{
    //                 $urlMismatchFunction($value[1],$args);
    //             }
    //         }
    //     }
        
    //     foreach ($route as $key => $value){
    //         $isMatched = preg_match_all('/@([^\/]*)/', $key, $matches);
    //         if($isMatched != 0){
    //             $routeList = array_filter(explode("/",$key));
    //             $valueList = array_filter(explode("/",$value[1]));
    //             $originRoute = array_filter(explode("/",$path['path']));
    //             $originValue = $valueList;
    //             $vars = [];
    //             $i = 0;
    //             if(count($originRoute) != count($routeList)) continue;
    //             foreach ($matches[0] as $match){
    //                 $k = array_search($match,$routeList);
    //                 if(!array_key_exists($k,$routeList) || !array_key_exists($k,$originRoute)) new RunTimeException("路由格式错误",__FILE__,__LINE__);
    //                 if(in_array($match,$valueList)) $originValue[array_search($match,$originValue)] = $originRoute[$k];
    //                 elseif(empty($args)) $args = "?".$matches[1][$i]."=".$originRoute[$k];
    //                 else $args = $args."&".$matches[1][$i]."=".$originRoute[$k];
    //                 $originRoute[$k] = $routeList[$k];
    //                 $i++;
    //             }
    //             $originRoute = "/".implode("/",$originRoute);
    //             $originValue = "/".implode("/",$originValue);
    //             if($originRoute == $key){
    //                 if(app()->make("request")->method() == $value[0] || $value[0] == "*"){
    //                     if (filter_var($path, FILTER_VALIDATE_URL) !== false){
    //                         $urlMatchFunction($originValue,$args);
    //                     }else{
    //                         $urlMismatchFunction($originValue,$args);
    //                     }
    //                 }
    //             }
    //         }
    //     }
    //     return $this;
    // }
}