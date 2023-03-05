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

use presty\Facade\Request;

class Controller
{

    protected $view, $template,$request, $viewQueue,$middlewareArgs = [];

    function __construct ()
    {
        $this->viewQueue = app()->make("viewQueue");
        $this->view = $this->viewQueue->getMainView();
        $this->request = app()->make("request");
    }

    public function global ($vars)
    {
        return $GLOBALS[$vars];
    }

    public function getFileContent ($path, $returnContent = false)
    {
        return $this->view->getFileContent ($path, $returnContent);
    }

    public function assign ($key, $value = "")
    {
        $this->view->assign ($key, $value);
        return $this;
    }

    public function render ($content = "")
    {
        $this->view->render ($content);
        return $this;
    }

    public function toggle ($cleanCache = true)
    {
        $this->view->toggle ($cleanCache);
        return $this;
    }

    public function filter ($content = "")
    {
        $this->view->filter ($content);
        return $this;
    }

    public function save ()
    {
        $this->view->save ();
        return $this;
    }

    public function setContent ($content)
    {
        $this->view->setContent ($content);
        return $this;
    }

    public function getMiddlewareArg ($name)
    {
        return $this->middlewareArgs[$name];
    }

    public function setMiddlewareArg ($args)
    {
        return $this->middlewareArgs = $args;
    }

}