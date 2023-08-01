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

use presty\Facade\Log;

/**
 *
 */
class Core extends Container
{
    /**
     * 是否已经初始化过
     * @var bool
     */
    protected $inited = false;

    /**
     * 系统开始运行时间
     * @var int
     */
    protected $startTime = 0;

    /**
     * 系统初始内存
     * @var int
     */
    protected $startMemory = 0;

    /**
     * 系统默认语言环境
     * @var string
     */
    protected $language = "";

    /**
     * 系统根目录
     * @var string
     */
    protected $rootPath = "";

    /**
     * 框架核心代码目录
     * @var string
     */
    protected $systemPath = "";

    /**
     * 应用目录
     * @var string
     */
    protected $appPath = "";

    /**
     * 配置文件目录
     * @var string
     */
    protected $configPath = "";

    /**
     * 内置配置文件目录
     * @var string
     */
    protected $insideConfigPath = "";

    /**
     * 模块引导文件目录
     * @var string
     */
    protected $moduleGuidesPath = "";

    /**
     * 运行模式
     * @var string Web/Cli/Cli-test
     */
    protected $runningMode = "";

    /**
     * 静态资源目录
     * @var string
     */
    protected $publicPath = "";

    /**
     * 系统目录分隔符
     * @var string
     */
    protected $ds = DIRECTORY_SEPARATOR;

    /**
     * 系统预定义类
     * @var string[]
     */
    protected $logo = [
        'app'             => Core::class,
        'config'          => Config::class,
        'cookie'          => Cookie::class,
        'database'        => Database::class,
        'debugMode'       => DebugMode::class,
        'env'             => Env::class,
        'http'            => Http::class,
        'middleWare'      => MiddleWare::class,
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
        'exception'       => Exception::class,
        'view'            => View::class,
        'validate'        => Validate::class,
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

    /**
     * @return $this|false
     */
    public function init ()
    {
        //记录系统初始化状态
        if ($this->inited) return false;
        else $this->inited = true;

        $this->systemPath = __DIR__.$this->ds;
        $this->rootPath = $this->rootPath ?? dirname ($this->systemPath,4).$this->ds;
        $this->appPath = $this->rootPath."app".$this->ds;
        $this->configPath = $this->rootPath."config".$this->ds;
        $this->insideConfigPath = $this->systemPath."Config".$this->ds;
        $this->publicPath = $this->rootPath."public".$this->ds;
        $this->moduleGuidesPath = $this->rootPath."ModuleGuides".$this->ds;

        //记录系统启动信息
        $this->startTime = SYSTEM_START_TIME;
        $this->startMemory = SYSTEM_START_MEMORY;
        $this->setVar ("hasBeenRun",["sInit"=>" - System_Init"]);

        //引入系统全局配置
        require_once $this->insideConfigPath . 'Config.php';

        //注册系统基本模块
        $module = $this->instance ("module",new Module());
        $this->newInstance ("module")->init ($this)->guide ();

        return $this;
    }

    /**
     * @return mixed
     */
    public function runMain ()
    {
        return $this->newInstance ("module")->callFunction("http","init",[$this],0);
    }

    /**
     * @return string
     */
    public function getRootPath (): string
    {
        return $this->rootPath;
    }

    /**
     * @return string
     */
    public function getSystemPath (): string
    {
        return $this->systemPath;
    }

    /**
     * @return string
     */
    public function getAppPath (): string
    {
        return $this->appPath;
    }

    /**
     * @return string
     */
    public function getConfigPath (): string
    {
        return $this->configPath;
    }

    /**
     * @return string
     */
    public function getModuleGuidesPath (): string
    {
        return $this->moduleGuidesPath;
    }

    /**
     * @return string
     */
    public function getPublicPath (): string
    {
        return $this->publicPath;
    }

    /**
     * @return boolean
     */
    public function runningInConsole(): bool
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }

    /**
     * @return string
     */
    public function getRunningMode (): string
    {
        return $this->runningMode;
    }

    /**
     * @param $mode
     * @return Core
     */
    public function setRunningMode ($mode): Core
    {
        $this->runningMode = $mode;

        return $this;
    }

    /**
     * @return void
     */
    public function end ()
    {
        //系统启动完成
        app()->setArrayVar("hasBeenRun","sEnd"," - System_End");
        middleWare_getClassName ("appDestroy")->listening ([app()->make("debugMode")]);
        if (env ('system.debug.mode', false) || get_config('env.print_system_status',false)) {
            app()->make("debugMode")->output ();
        }
        if(get_config("env.save_running_log",false)) {
            $time = date("Y-m-d H:i:s");
            $title = "[".$time." ".app()->make("request")->siteUrl()."]";
            $data = app()->make("debugMode")->print();
            $temp = "";
            foreach ($data as $item){
                $temp .= "\\n".implode ("\\n",$item);
            }
            Log::logOut($title."\r\n".str_replace ("\\n","\r\n",$temp));
        }
    }
}