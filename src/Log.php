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

class Log
{

    //运行时间
    protected $run_time = "";

    //日志输出目录
    protected $save_path = CACHE;

    //日志输出文件
    protected $save_file = "";

    //输出内容
    protected $save_data = "";

    //运行时间
    protected $runtime = 0;

    //完整运行时间
    protected $totalRuntime;

    public function logOut ($content, $saveFile = "")
    {
        middleWare_getClassName ('beforeLogWrite')->listening ([$content, $saveFile]);
        app()->set("hasBeenRun",'end',"[ ".(new \DateTime())->format("Y-m-d H:i:s:u")." ] => - Log_Write");
        $this->runtime = date ("Y-m-d");
        $this->totalRuntime = date ("H-i-s");
        $this->save_path .= $this->runtime;
        if (!is_dir ($this->save_path . DS . "runningLogs")) {
            mkdir ($this->save_path . DS . "runningLogs", 0777, true);
        }
        if (empty($saveFile)) $saveFile = $this->totalRuntime."-".time () . ".log";
        $this->save_file = $this->save_path . DS . "runningLogs" . DS . $saveFile;
        if(!file_exists ($this->save_file)) fopen ($this->save_file, "w");
        file_put_contents ($this->save_file, $content);
    }

    public function errorOut ($content, $saveFile = CACHE . DS . "errorLogs" . DS . "Error.log")
    {
        middleWare_getClassName ('beforeLogWrite')->listening ([$content, $saveFile]);
        app()->set("hasBeenRun",'end',"[ ".(new \DateTime())->format("Y-m-d H:i:s:u")." ] - Log_Write");
        $this->runtime = date ("Ym");
        $this->save_path .= $this->runtime;
        if (!is_dir ($this->save_path . DS . "errorLogs")) {
            mkdir ($this->save_path . DS . "errorLogs");
        }
        fopen ($this->save_file, "a+");
        file_put_contents ($saveFile, "");
        error_log ($content, 0);
        error_log ($content . "\r\n" . "\r\n", 3, $saveFile);
    }

    protected static function setFacade ()
    {
        return '\Cache';
    }


}