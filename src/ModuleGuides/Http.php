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

namespace ModuleGuides;

use presty\Container;

class Http
{
    protected $app;

    public function register (\presty\Core $app)
    {
        $this->app = $app;
        $app->makeAndSave ("http");
        $app->makeAndSave ("request");
        $app->makeAndSave ("Response");
        $app->makeAndSave ("Route");
    }

    public function init ()
    {
        return $this->app->make("http")->init($this->app)->operate($this->app->makeAndSave("request"));
    }
}