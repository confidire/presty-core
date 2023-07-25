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

use presty\exception\InvalidArgumentException;
use presty\exception\NotFoundException;
use presty\exception\RunTimeException;
use ReflectionClass;

class Container
{
    protected static $instance;

    protected $vars = [];

    protected $instances = [];

    protected $closure = [];

    protected $logo = [];

    //获取自身实例对象
    public static function getInstance ()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        if (static::$instance instanceof Closure) {
            return (static::$instance)();
        }

        return static::$instance;
    }

    //手动设置自身实例对象
    public static function setInstance (object $instance)
    {
        static::$instance = $instance;
    }

    public function bind ($key, $value = null): Container
    {
        if (is_array ($key) && $value == null) foreach ($key as $key => $val) $this->bind ($key, $val);
        elseif ($value instanceof Closure) $this->bind[$key] = $value;
        elseif (is_object ($value)) $this->instance ($key, $value);
        else {
            $key = $this->liftAlias ($key);
            if ($key != $value) {
                $this->bind[$key] = $value;
            }
        }
        return $this;
    }

    //解除别名标识，获取绑定的原名
    public function liftAlias ($key): string
    {
        if (isset($this->logo[$key])) {
            $logo = $this->logo[$key];
            if (is_string ($logo)) return $this->liftAlias ($logo);
        }
        return $key;
    }

    //将实例对象绑定到容器中
    public function instance ($key, $instance): Container
    {
        $key = $this->liftAlias ($key);

        $this->instances[$key] = $instance;

        return $this;
    }

    //获取实例容器中的实例对象，没有就从标识中创建
    public function newInstance ($key, $autoSave = false)
    {
        $key = $this->liftAlias ($key);

        if(isset($this->instances[$key])){
            return $this->instances[$key];
        }elseif(isset($this->logo[$key])){
            $class = new $this->logo[$key];
            if($autoSave) $this->instance ($key,$class);
            return $class;
        }elseif(isset($this->vars[$key]) && class_exists ($this->vars[$key])){
            $class = new $this->vars[$key];
            if($autoSave) $this->instance ($key,$class);
            return $class;
        }elseif(class_exists ($key)){
            $class = new $key;
            if($autoSave) $this->instance ($key,$class);
            return $class;
        }
        return false;
    }

    public function make ($key)
    {
        $key = $this->liftAlias ($key);

        if(isset($this->instances[$key])){
            return $this->instances[$key];
        }elseif(isset($this->logo[$key])){
            return new $this->logo[$key];
        }elseif(isset($this->vars[$key]) && class_exists ($this->vars[$key])){
            return new $this->vars[$key];
        }elseif(class_exists ($key)){
            return new $key;
        }
        return false;
    }

    public function get ($name, $args = [])
    {
        $name = $this->liftAlias ($name);
        if (class_exists ($name)) return $this->invokeClass ($name, $args);
        elseif (function_exists ($name)) return $this->invokeFunction ($name, $args);
        else return false;
    }

    public function getClass ($name)
    {
        $name = $this->liftAlias ($name);
        return $this->instances[$name] ?? false;
    }

    public function setVar ($name,$value): Container
    {
        $this->vars[$name] = $value;
        return $this;
    }

    public function setArrayVar ($name,$key,$value): Container
    {
        if(!is_array ($this->vars[$name])) new RunTimeException($name."不是Array类型的变量",__FILE__,__LINE__);
        $this->vars[$name][$key] = $value;
        return $this;
    }

    public function has ($name, $value = "", $returnValue = false)
    {
        if (is_bool ($name) && $name && !empty($value)) {
            if ($returnValue) return !is_bool (array_search ($value, $this->vars)) ? array_search ($value, $this->vars) : false;
            else return !is_bool (array_search ($value, $this->vars));
        } elseif (!empty($name) && !is_bool ($name) && empty($value)) {
            if ($returnValue) return $this->vars[$name] ?? false;
            else return isset($this->vars[$name]);
        } elseif (!empty($name) && !is_bool ($name) && !empty($value)) {
            if ($returnValue) return isset($this->vars[$name]) && $this->vars[$name] == $value ? $this->vars[$name] : false;
            else return isset($this->vars[$name]) && $this->vars[$name] == $value;
        } else {
            new InvalidArgumentException ("Name: " . $name . " Value: " . $value . " returnValue: " . $returnValue,__FILE__, __LINE__);
        }
    }

    public function invokeFunction ($function, array $vars = [])
    {
        try {
            $reflect = new ReflectionFunction($function);
        } catch (ReflectionException $e) {
            new NotFoundException ($function, __FILE__, __LINE__, "EC100010");
        }

        return $function(...$vars);
    }


    public function invokeClass ($class, $args = [])
    {
        try {
            $reflect = new ReflectionClass($class);
        } catch (\ReflectionException $e) {
            new NotFoundException ($class, __FILE__, __LINE__, "EC100009");
        }
        if ($reflect->hasMethod ('__make')) {
            $method = $reflect->getMethod ('__make');
            if ($method->isPublic () && $method->isStatic ()) {
                try {
                    return $method->invokeArgs (null, $args);
                } catch (\ReflectionException $e) {
                    Error::runException ($e);
                }
            }
        }
        $constructor = $reflect->getConstructor ();
        $args = $constructor ? $args : [];
        try {
            return $reflect->newInstanceArgs ($args);
        } catch (\ReflectionException $e) {
            Error::runException ($e);
        }
    }

    public function __get ($name)
    {
        return $this->get($name);
    }

    public function __call ($name,$args)
    {
        return $this->get($name,$args);
    }

}