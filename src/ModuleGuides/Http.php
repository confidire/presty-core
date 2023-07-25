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
        $app->newInstance ("http",true);
        $app->newInstance ("request",true);
        $app->newInstance ("Response",true);
    }

    public function init (\presty\Core $app)
    {
        return $app->make("http")->init($app)->operate($app->make("request"));
    }
}