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

class Http
{
    protected $app;

    protected $request;

    public function init (Core $app)
    {
        $this->app = $app;

        return $this;
    }

    public function operate (Request $request)
    {
        $this->setRequest ($request);

        return $this->runWithRouter ();
    }

    public function runWithRouter ()
    {
        $request = $this->app->newInstance("request");
        $router = $this->app->newInstance("router",true);
        $url = $router->init();
        $url = $router->setEngine()->getEngine()->parse($url);
        $request->setUrl ($url);
        return $router->set($request);
    }

    public function setRequest ($request): Http
    {
        $this->request = $request;
        $this->app->instance("request",$request);
        return $this;
    }
}