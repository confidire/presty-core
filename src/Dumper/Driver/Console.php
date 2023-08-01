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

namespace presty\Dumper\Driver;

use presty\Exception\InvalidArgumentException;
use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    // 不显示信息(静默)
    const VERBOSITY_QUIET        = 0;

    // 正常信息
    const VERBOSITY_NORMAL       = 1;

    // 详细信息
    const VERBOSITY_VERBOSE      = 2;

    // 非常详细的信息
    const VERBOSITY_HIGH_VERBOSE = 3;

    // 调试信息
    const VERBOSITY_DEBUG        = 4;

    //普通错误，不会中断系统运行
    const NORMAL_ERROR           = 0;

    //普通警告，不会中断系统运行
    const WARNING_ERROR          = 1;

    //致命错误，会中断系统运行
    const FATAL_ERROR            = 2;

    private $errorCode = self::NORMAL_ERROR;

    // 输出信息级别
    private $verbosity = self::VERBOSITY_NORMAL;

    //输出样式
    protected $styles = [
        'info' => '\033[36m',
        'question' => '\033[34m',
        'debug' => '\033[35m',
        'highlight' => '\033[33m',
        'warning' => '\033[38;5;59m',
        'error' => '\033[31m',
        'success' => '\033[32m',
        'normal' => '[0m'
    ];

    public function __construct () {
        if(self::env ("system.debug_mode",false)) $this->verbosity = self::VERBOSITY_HIGH_VERBOSE;
    }

    public function render ($content = "") {
        return $this->styles["info"].$content.$this->styles["normal"];
    }

    public function info ($content = "") {
        echo $this->styles["info"].$content.$this->styles["normal"];
    }

    public function question ($content = "") {
        echo $this->styles["question"].$content.$this->styles["normal"];
    }

    public function debug ($content = "") {
        echo $this->styles["debug"].$content.$this->styles["normal"];
    }

    public function highlight ($content = "") {
        echo $this->styles["highlight"].$content.$this->styles["normal"];
    }

    public function warning ($content = "") {
        echo $this->styles["warning"].$content.$this->styles["normal"];
    }

    public function error ($content = "") {
        echo $this->styles["error"].$content.$this->styles["normal"];

    }

    public function success ($content = "") {
        echo $this->styles["success"].$content.$this->styles["normal"];
    }

    public function renderError ($data = [])
    {
        if (empty($data)) {
            if($this->errorCode == self::FATAL_ERROR){
                exit();
            }
        }else{
            $content = $data["message"]." on [".$data["line"]."] ".$data["file"];
            if($this->errorCode == self::WARNING_ERROR) $this->warning ("Warning: ".$content);
            elseif($this->errorCode == self::NORMAL_ERROR) $this->error ("Error: ".$content);
            elseif($this->errorCode == self::FATAL_ERROR){
                $this->error ("Fatal Error: ".$content);
                die();
            }
        }
    }

    public function setVerbosity ($verbosity = self::VERBOSITY_NORMAL): Console
    {
        $this->verbosity = $verbosity;
        return $this;
    }

    public function setErrorCode ($code = self::NORMAL_ERROR): Console
    {
        $this->errorCode = $code;
        return $this;
    }

    private static function env ($name,$default = "")
    {
        return \presty\Env::get($name,$default);
    }
}