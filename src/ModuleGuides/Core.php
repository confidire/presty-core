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

use \presty\Facade\MiddleWare;

class Core
{
    public function register (\presty\Core $app)
    {

        unset($GLOBALS["baseDir"],$GLOBALS["vendorDir"]);
        $baseDir = $vendorDir = null;

        $configClass = $app->config->init();
        $app->instance ("config",$configClass);

        if(file_exists (ROOT.".env")){
            $app->env->loadFile();
        }


        ($langClass = $app->lang($app))->load();
        $app->instance ("lang",$langClass);

        //监听App_Init
        $hook = (new \presty\MiddleWare($app))->getClassName('appInit',false,$app->has("middleWare","",true))->listening ();
        $hook = (new \presty\MiddleWare($app))->getClassName('appInit',false,$app->has("middleWare","",true))->listening ();
        MiddleWare::getClassName('appInit',false,$app->has("middleWare","",true))->listening ();

        //注册助手函数
        require_once DIR . "Helper.php";

        //注册调试模式日志输出机制
        $app->instance ("debugMode",$app->debugMode);

        //注册系统错误捕获机制
        \presty\Error::init ();
    }
}