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

namespace presty\Router\Driver;
use presty\exception\UrlSyntaxException;
use presty\Facade\Router;
use presty\Facade\Route;
use presty\RouterInterface;

class Presty extends Router implements RouterInterface
{
    public function parse ($url): array
    {
        $returnInfo = ["app" => "", "path" => DS, "controller" => "", "function" => "", "vars" => []];
        $tempUrl = explode ("/",$url);
        $tempVars = [];
        foreach ($tempUrl as $key => $item) {
            if(is_numeric ($item)){
                $tempVars[$tempUrl[$key-1]] = $item;
                unset($tempUrl[$key],$tempUrl[$key-1]);
                if(count($tempUrl) < 3 && count($tempUrl) != 1 && $tempUrl[0] != "") new UrlSyntaxException("参数名和参数值的数量不匹配",__FILE__,__LINE__);
                elseif(count($tempUrl) == 1 && $tempUrl[0] == "") $url = "/index";
            }
        }
        if(get_config("env.use_system_index_route",true) && $url == "/index" ||  preg_match('/\/\?.*/', $url, $matches)) {
            return ["app" => DEFAULT_APP_NAME, "path" => DS, "controller" => "Index", "function" => "index", "vars" => $tempVars];
        }
        Route::parse($url,app()->has("route","",true),
            function($tempValue,$combine){
                header("Location: ".$tempValue.$combine);
            },
            function ($tempValue,$combine){
                header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
                die;
            });
        $path = parse_url ($url);
        $info = array_filter (explode ("/", substr ($path['path'], 1)));
        isset($path['query']) ? $query = $path['query'] : $query = [];
        if (is_string ($query)) {
            $queryArray = array_filter (explode ("&", $query));
            $query = [];
            foreach ($queryArray as $key => $value) {
                $array = array_filter (explode ("=", $value));
                $query[$array[0]] = $array[1];
            }
        }
        $returnInfo["vars"] = array_merge ($returnInfo["vars"], $query);
        if (count ($info) == 3) {
            $returnInfo["app"] = ucfirst ($info[0]);
            $returnInfo["controller"] = ucfirst ($info[1]);
            $returnInfo["function"] = $info[2];
        } elseif (count ($info) > 3) {
            foreach ($info as $key => $item) {
                if (is_numeric ($item)){
                    $returnInfo["vars"][$info[$key-1]] = $item;
                    unset($info[$key],$info[$key-1]);
                    if(count($info) < 3) new UrlSyntaxException("参数名和参数值的数量不匹配",__FILE__,__LINE__);
                }
            }
            $returnInfo["app"] = ucfirst ($info[0]);
            $returnInfo["controller"] = ucfirst ($info[1]);
            $returnInfo['path'] = implode (DS, array_slice ($info, 2, count ($info) - 3, true));
            $returnInfo["function"] = $info[count ($info) - 1];
            if(empty($returnInfo["path"])) $returnInfo["path"] = DS;
        }
        return $returnInfo;
    }
}