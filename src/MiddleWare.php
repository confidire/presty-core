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

use presty\exception\InvalidArgumentException;

class MiddleWare
{

    protected $className = "";

    protected $functionName = "";

    public function __construct ($app = "") {
        if(is_object ($app)) if(!$app->has("middleWare")) $app->setVar("middleWare",require CONFIG . "MiddleWare.php");
    }

    public function listening ($args = "", $className = "", $functionName = "")
    {
        if (empty($className) && empty($functionName)) {
            $functionName = $this->functionName;
            $className = $this->className;
        }
        if (empty($className) && empty($functionName)) new InvalidArgumentException("函数transfer()至少需要提供2个非空参数，已提供1个","EC100007");
        if (empty($className) && !empty($functionName)) return false;
        if (count ($className) > 1) {
            foreach ($className as $key => $value) {
                $class = new $value;
                call_user_func_array ([$class, $functionName], $args);
            }
        } else {
            $class = new $className[0];
            return call_user_func_array ([$class, $functionName], (array)$args);
        }
    }

    public function getClassName ($middleWareName, $returnString = false,$middleWare = [])
    {
        if(empty($middleWare)) $middleWare = app()->has("middleWare","",true);
        if (isset($middleWare[$middleWareName])) {
            if ($returnString) return $middleWare[$middleWareName];
            else {
                $this->functionName = $middleWareName;
                $this->className = $middleWare[$middleWareName];
                return $this;
            }
        }
        else return false;
    }

    public function bind ($middlewareName, $className)
    {
        app()->setArrayVar("middleware",$middlewareName,$className);
    }
}