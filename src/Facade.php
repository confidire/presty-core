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


use presty\Container;

class Facade{
    public static function __callStatic($name,$args) {
        $class = $name ?: static::class;
        $facadeClass = static::setFacade();
        if ($facadeClass) {
            $class = $facadeClass;
        }
        $class = Container::getInstance ()->invokeClass($class,$args);
        return call_user_func_array ([$class,$name],$args);
    }

    private static function setFacade () {}
}