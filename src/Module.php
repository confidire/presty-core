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

class Module
{

    protected $modules = [];

    protected $app = null;

    public function init (Core $app): Module
    {
        $this->modules = require_once CONFIG."Module.php";
        $this->app = $app;
        return $this;
    }

    public function guide ($modules = null): bool
    {
        $modules = $modules ?: $this->modules;
        if(empty($modules)) return false; //Exception
        foreach ($modules as $key => $value) {
            if(count($value) == 1){
                if(is_array ($value[0])){
                    $className = array_shift ($value[0]);
                    $args = array_merge ([$this->app],$value[0]);
                }else{
                    $className = $value[0];
                    $args = [$this->app];
                }
                $class = Container::getInstance ()->invokeClass($className);
                $class->register(...$args);
            }
            else{
                foreach ($value as $v) {
                    if(is_array ($v)){
                        $className = array_shift ($v);
                        $args = array_merge ([$this->app],$v);
                    }else{
                        $className = $v;
                        $args = [$this->app];
                    }
                    $class = Container::getInstance ()->invokeClass($className);
                    $class->register(...$args);
                }
            }
        }
        return true;
    }

    public function callFunction ($moduleName,$functionName,$args = [],$classIndex = 0)
    {
        $class = new $this->modules[$moduleName][$classIndex];
        if(method_exists ($class,$functionName))
        return call_user_func_array ([$class,$functionName],$args);
        else return false; //Exception
    }
}