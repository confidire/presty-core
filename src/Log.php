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

    protected function logOut ($content, $saveFile = "")
    {
        hook_getClassName ('beforeLogWrite')->transfer ([$content, $saveFile]);
        $hasBeenRun['end'] = " - Log_Write";
        $this->runtime = date ("Ym");
        $this->save_path .= $this->runtime;
        if (!is_dir ($this->save_path)) {
            mkdir ($this->save_path, 0777, true);
        }
        if (empty($saveFile)) $saveFile = time () . ".log";
        $this->save_file = $this->save_path . "/" . $saveFile;
        $file = fopen ($this->save_file, "a+");
        file_put_contents ($this->save_file, $content);
    }

    protected function errorOut ($content, $saveFile = CACHE . "Error.log")
    {
        hook_getClassName ('beforeLogWrite')->transfer ([$content, $saveFile]);
        $hasBeenRun['end'] = " - Log_Write";
        $this->runtime = date ("Ym");
        $this->save_path .= $this->runtime;
        if (!is_dir ($this->save_path)) {
            mkdir ($this->save_path);
        }
        if (!file_exists ($saveFile)) {
            file_put_contents ($saveFile, "");
        }
        error_log ($content, 0);
        error_log ($content . "\r\n" . "\r\n", 3, $saveFile);
    }

    protected static function setFacade ()
    {
        return '\Cache';
    }


}