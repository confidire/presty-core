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

use presty\Exception\InvalidArgumentException;
use presty\Exception\RunTimeException;

class ViewQueue
{
    protected $queue = [];
    protected $mainView;
    protected $viewName;

    function init (\presty\View $view, $name): ViewQueue
    {
        $this->queue[$name] = $view;
        $this->mainView = $view;
        container_instance ("View",$this->mainView);
        return $this;
    }

    public function get ($name)
    {
        return $this->queue[$name];
    }

    public function getQueue (): array
    {
        return $this->queue;
    }

    public function getMainView (): View
    {
        return $this->mainView;
    }

    public function getViewName ($view, $returnSelf = false)
    {
        if ($returnSelf) {
            $this->viewName = array_search ($view, $this->queue);
            return $this;
        }
        return array_search ($view, $this->queue);
    }

    public function getView ($name)
    {
        return $this->queue[$name];
    }

    public function create ($name)
    {
        $this->queue[$name] = new View();
    }

    public function set ($name, $view): bool
    {
        $this->queue[$name] = $view;
        return isset($this->queue[$name]);
    }

    public function setMainView ($view): bool
    {
        container_instance ("View",$this->mainView);
        if(is_object ($view)) {
            $this->mainView = $view;
        }
        else {
            $this->mainView = !is_bool ($result = array_search ($view, $this->queue)) ? $result : new RunTimeException("视图对象不存在",__FILE__,__LINE__,"EC100007");
        }
        return true;
    }

    public function toggle ($targetView)
    {
        $this->getMainView ()->save ();
        $this->mainView = $targetView;
        $targetView->toggle ();
    }

    public function delete ($name = "")
    {
        if (empty($name)) $name = $this->viewName;
        if (empty($name)) new InvalidArgumentException("name",__FILE__,__LINE__,"EC100032");
        unset($this->queue[$name]);
    }
}