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

use presty\Exception\NotFoundException;
use presty\Exception\RunTimeException;
use ReflectionClass;
use ReflectionFunction;

class Container
{
    protected static $instance;

    protected $instances = [];

    protected $bind = [];

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

    public function make ($key,$args = [])
    {
        $key = $this->liftAlias ($key);

        if(is_array($key)) {
            foreach ($key as $k => $value) {
                $this->make ($k, $value);
            }
        }

        if(isset($this->instances[$key])){
            return $this->instances[$key];
        }elseif(isset($this->logo[$key])){
            $class = new $this->logo[$key];
            return $class;
        }elseif(isset($this->bind[$key]) && is_string($this->bind[$key])){
                if(class_exists ($this->bind[$key])){
                $class = new $this->bind[$key];
                return $class;
            }
        }elseif(class_exists ($key)){
            return $this->invokeClass($key,$args);
        }
        return false;
    }

    public function isInstanceExists($key) {
        return array_key_exists($key,$this->instances);
    }

    public function isBinded($key) {
        return isset($this->bind[$key]);
    }

    public function makeAndSave ($key,$args = [])
    {

        $key = $this->liftAlias ($key);

        if(is_array($key)) {
            foreach ($key as $k => $value) {
                $this->makeAndSave ($k, $value);
            }
        }

        if(isset($this->instances[$key])){
            return $this->instances[$key];
        }elseif(isset($this->logo[$key])){
            $class = new $this->logo[$key];
            $this->instance ($key,$class);
            return $class;
        }elseif(isset($this->bind[$key]) && class_exists ($this->bind[$key])){
            $class = new $this->bind[$key];
            $this->instance ($key,$class);
            return $class;
        }elseif(class_exists ($key)){
            $class = $this->invokeClass($key,$args);
            $this->instance ($key,$class);
            return $class;
        }
        return false;
    }

    public function get ($name)
    {
        $name = $this->liftAlias ($name);

        if(isset($this->bind[$name])){
            return $this->bind[$name];
        }else {
            return false;
        }
    }

    public function set ($name, $value = "",$arrayValue = "")
    {
        $name = $this->liftAlias ($name);

        if(!empty($arrayValue)){
            if(isset($this->bind[$name]) && is_array($this->bind[$name]))
                $this->bind[$name][$value] = $arrayValue;
            else $this->bind[$name] = [$value => $arrayValue];
        }
        else{
            if(!isset($this->bind[$name])){
                $this->bind[$name] = $value;
                return $this;
            }
            else{
                new RunTimeException('Container::set( '.$name.' , '.$value.' )',__FILE__,__LINE__,'EC100035');
            }
        }
    }

    public function getSelfMethod ($name, $args = [])
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
        // var_dump($class);
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
        return $this->getSelfMethod($name);
    }

    public function __call ($name,$args)
    {
        return $this->getSelfMethod($name,$args);
    }

}