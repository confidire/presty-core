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

namespace presty\urlParser;
use startphp\Facade\Router;
use startphp\Facade\Route;
use startphp\RouterInterface;

class Start extends Router implements RouterInterface
{
    public function parse ($url): array
    {
        global $route;
        $returnInfo = ["app" => "", "path" => DS, "controller" => "", "function" => "", "vars" => []];
        if(config("use_system_index_route",true) && $url == "/index" ||  preg_match('/\/\?.*/', $url, $matches))
            $returnInfo = ["app" => DEFAULT_APP_NAME, "path" => DS, "controller" => "Index", "function" => "index", "vars" => []];
        Route::parse($url,$route,
            function($tempValue,$combine){
                header("Location: ".$tempValue.$combine);
            },
            function ($tempValue,$combine){
                header("Location: http://".$_SERVER['HTTP_HOST'].$tempValue.$combine);
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
            $returnInfo["app"] = ucfirst ($info[0]);
            $returnInfo["controller"] = ucfirst ($info[1]);
            $returnInfo['path'] = ParseUrl . phpimplode (DS, array_slice ($info, 2, count ($info) - 3, true));
            $returnInfo["function"] = $info[count ($info) - 1];
        }
        return $returnInfo;
    }
}