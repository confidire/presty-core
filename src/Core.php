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

class Core extends Container
{
    //是否已经初始化过
    protected $inited = false;

    //系统开始运行时间
    protected $startTime = 0;

    //系统初始内存
    protected $startMemory = 0;

    //系统默认语言环境
    protected $language = "";

    //系统根目录
    protected $rootPath = "";

    //框架核心代码目录
    protected $systemPath = "";

    //应用目录
    protected $appPath = "";

    //配置文件目录
    protected $configPath = "";

    //模块引导文件目录
    protected $moduleGuidesPath = "";

    //静态资源目录
    protected $publicPath = "";

    //系统目录分隔符
    protected $ds = DIRECTORY_SEPARATOR;

    protected $logo = [
        'app'             => Core::class,
        'cookie'          => Cookie::class,
        'database'        => Database::class,
        'env'             => Env::class,
        'http'            => Http::class,
        'hook'            => Hook::class,
        'lang'            => Language::class,
        'log'             => Log::class,
        'model'           => Model::class,
        'module'          => Module::class,
        'redirect'        => Redirect::class,
        'request'         => Request::class,
        'response'        => Response::class,
        'route'           => Route::class,
        'router'          => Router::class,
        'session'         => Session::class,
        'template'        => Template::class,
        'throwError'      => ThrowError::class,
        'view'            => View::class,
        'viewQueue'       => ViewQueue::class,
    ];

    function __construct ()
    {
        //将自身注入到容器中，用于依赖注入
        static::setInstance($this);
        $this->instance('app', $this);
        $this->instance('container', $this);

        //记录所有输出，以便在错误捕获后清空页面所有已渲染元素
        ob_start ();
    }

    public function init ()
    {

        $this->systemPath = dirname (__DIR__);
        $this->rootPath = $this->rootPath ?? dirname ($this->systemPath,4).$this->ds;
        $this->appPath = $this->rootPath."app".$this->ds;
        $this->configPath = $this->rootPath."config".$this->ds;
        $this->publicPath = $this->rootPath."pulic".$this->ds;
        $this->moduleGuidesPath = $this->rootPath."moduleGuides".$this->ds;

        //记录系统初始化状态
        if ($this->inited) return false;
        else $this->inited = true;

        //记录系统启动信息
        $this->startTime = SYSTEM_START_TIME;
        $this->startMemory = SYSTEM_START_MEMORY;

        //引入系统全局配置
        require_once $this->configPath . 'Config.php';

        //注册系统基本模块
        $module = $this->instance ("module",new Module());
        $this->newInstance ("module")->init ($this)->guide ();

        unset($GLOBALS["baseDir"]);
        $baseDir = null;

        return $this;
    }

    public function runMain ()
    {
        return $this->newInstance ("module")->callFunction("http","init",[$this],0);
    }

    public function getRootPath ()
    {
        return $this->rootPath;
    }

    public function getSystemPath ()
    {
        return $this->systemPath;
    }

    public function getAppPath ()
    {
        return $this->appPath;
    }

    public function getConfigPath ()
    {
        return $this->configPath;
    }

    public function getmoduleGuidesPath ()
    {
        return $this->moduleGuidesPath;
    }

    public function getPublicPath ()
    {
        return $this->PublicPath;
    }

    public function end ()
    {
        global $memoryAnalysis;
        //系统启动完成
        $hasBeenRun['end'] = " - System_Init_End";
        hook_getClassName ("appDestroy")->transfer ([$memoryAnalysis]);
        if (env ('system.debug.mode', false) || config('print_system_status',false)) {
            $memoryAnalysis->output ();
        }
    }
}