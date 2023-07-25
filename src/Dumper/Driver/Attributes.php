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

namespace presty\Dumper\Driver;

class Attributes
{
    public function parse ($content,$typeFilter = "")
    {
        $parsed = [];
        $content = array_values (array_filter (explode ("*",str_replace (" ","",trim(str_replace ("*/","",str_replace ("/**","",$content)))))));
        foreach ($content as $key => $item) {
            $isMatched = preg_match('/@([^(]*)\(([^=]*=[^)]*)\)/', $item, $matches);
            if(!is_bool ($isMatched)){
                if(!empty($typeFilter) && $matches[1] != $typeFilter) continue;
                $args = explode (",",$matches[2]);
                foreach ($args as $arg) {
                    $arg = explode ("=",$arg);
                    $parsed[$key]["name"] = $matches[1];
                    $parsed[$key]["args"][$arg[0]] = $arg[1];
                }
            }
        }
        return $parsed;
    }
}