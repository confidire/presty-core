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
use presty\Exception\RunTimeException;
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
        if(php_sapi_name () != "cli-server") $this->app->setRunningMode("web");
        else $this->app->setRunningMode("cli-test");
        $router = $this->app->newInstance("router",true);
        $url = $router->init();
        $url = $router->setEngine()->getEngine()->parse($url);
        $request->setUrl ($url);
        return $router->set($request);
    }

    public function runWithoutRouter ()
    {
        $this->app->setRunningMode("cli");
        $app = new Application();
        $pathPrefix = DIR."Console".DS."App".DS."Commands";
        scanFiles (DIR."Console" . DS . "App" . DS . "Commands",true,function($a, $v) use ($pathPrefix,$app){
            if(is_bool (stripos ($v,".php"))) return true;
            $a = str_replace ("/",DS,$a);
            $a = "presty\Console\App\Commands".str_replace (DS,"\\",str_replace (".php","",str_replace ($pathPrefix,"",$a)));
            try {
                $class = new \ReflectionClass($a);
            } catch (\ReflectionException $e) {
                new RunTimeException($e->getMessage (),$e->getFile (),$e->getLine ());
            }
            if(!$class->isAbstract() && $class->isInstantiable()) $app->add ($class->newInstance());
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