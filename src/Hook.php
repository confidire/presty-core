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

class Hook
{

    protected $className = "";

    protected $functionName = "";

    public function __construct () {
        global $hook;
        if(empty($hook)){
            $hook = require_once CONFIG . "Hook.php";
        }
    }

    public function transfer ($args = "", $className = "", $functionName = "")
    {
        global $hook;
        if (empty($className) && empty($functionName)) {
            $functionName = $this->functionName;
            $className = $this->className;
        }
        if (empty($className) && empty($functionName)) \ThrowError::throw(__FILE__, __LINE__, "EC100007");
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

    public function getClassName ($hookName, $returnString = false)
    {
        global $hook;
        if ($returnString) return $hook[$hookName];
        else {
            $this->functionName = $hookName;
            $this->className = $hook[$hookName];
            return $this;
        }
    }

    public function bind ($hookName, $className)
    {
        global $hook;
        array_push ($hook[$hookName], $className);
    }

    protected static function setFacade ()
    {
        return '\Hook';
    }
}