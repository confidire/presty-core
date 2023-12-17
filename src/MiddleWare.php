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

use presty\Dumper\Driver\Attributes;
use presty\Exception\InvalidArgumentException;
use presty\Exception\RunTimeException;

class MiddleWare
{

    protected $className = "";

    protected $functionName = "";

    public function __construct ($app = "") {
        if(is_object ($app)) if(!$app->get("middleWare")) $app->set("middleWare",require CONFIG . "MiddleWare.php");
        \presty\Container::getInstance()->set ("hasBeenRun", "middleware", " - [".(new \DateTime())->format("Y-m-d H:i:s:u")."] => MiddleWare_Init");
    }

    //监听并实例化指定中间件
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
        if(empty($middleWare)) $middleWare = Container::getInstance ()->get("middleWare",true);
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

    public function parseFunctionAttributesMiddleWare ($class,$functionName)
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            new RunTimeException("类反射失败-".$e->getMessage (),__FILE__,__LINE__);
        }
        $classDoc = $reflection->getDocComment();
        if(!is_bool ($classDoc)) $classDoc = (new Attributes())->parse ($classDoc,"middleware");
        else $classDoc = [];
        try {
            $functionDoc = $reflection->getMethod ($functionName)->getDocComment ();
        } catch (\ReflectionException $e) {
            new RunTimeException("类成员函数获取失败-".$e->getMessage (),__FILE__,__LINE__);
        }
        if(!is_bool ($functionDoc)) $functionDoc = (new Attributes())->parse ($functionDoc,"middleware");
        else $functionDoc = [];

        return array_merge ($classDoc,$functionDoc);
    }

    //直接调用已实例化的中间件模块
    public function call ($class,$functionName,$args = [])
    {
        $class->setMiddlewareArg($args);
        return call_user_func_array ([$class, $functionName], []);
    }

    public function bind ($middlewareName, $className)
    {
        \presty\Container::getInstance ()->set("middleware",$middlewareName,$className);
    }
}