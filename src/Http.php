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
use presty\exception\RunTimeException;
use Symfony\Component\Console\Application;

class Http
{
    protected $app;

    protected $request;

    public function init (Core $app): Http
    {
        $this->app = $app;

        return $this;
    }

    public function operate (Request $request)
    {
        $this->setRequest ($request);

        $runMode = php_sapi_name ();

        if(php_sapi_name () == "cli" || php_sapi_name() == 'phpdbg') $this->runWithoutRouter ();
        else return $this->runWithRouter ($request);
    }

    public function runWithRouter (Request $request)
    {
        $router = $this->app->newInstance("router",true);
        $url = $router->init();
        $url = $router->setEngine()->getEngine()->parse($url);
        $request->setUrl ($url);
        return $router->set($request);
    }

    public function runWithoutRouter ()
    {
        $app = new Application();
        $pathPrefix = DIR."Console".DS."App".DS."Commands";
        scanFiles (DIR."Console/App/Commands",true,function($a, $v) use ($pathPrefix,$app){
            $a = str_replace ("/",DS,$a);
            $a = "presty\Console\App\Commands\\".str_replace (DS,"\\",str_replace (".php","",str_replace ($pathPrefix,"",$a)));
            $app->add (new $a());
        });
        try {
            $app->run ();
        } catch (\Exception $e) {
            new RunTimeException($e->getMessage (),$e->getFile (),$e->getLine ());
        }
    }

    public function setRequest ($request): Http
    {
        $this->request = $request;
        $this->app->instance("request",$request);
        return $this;
    }
}