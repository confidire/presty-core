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
class DevDebug
{

    public function format_bytes ($size, $delimiter = '')
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round ($size, 2) . $delimiter . " " . $units[$i];
    }

    public function output ()
    {
        echo $this->print ();
    }

    public function print ()
    {
        global $lang;
        global $hasBeenRun;
        $data = "";
        $runtime = number_format (microtime (true) - SYSTEM_START_TIME, 10, '.', '') * 1000;
        $reqs = $runtime > 0 ? number_format (1 / $runtime, 2) : '∞';
        $mem = number_format ((memory_get_usage () - SYSTEM_START_MEMORY) / 1024, 2);
        if (isset($_SERVER['HTTP_HOST'])) {
            $uri = $_SERVER['SERVER_PROTOCOL'] . ' ' . $_SERVER['REQUEST_METHOD'] . ' : ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $uri = 'cmd:' . implode (' ', $_SERVER['argv']);
        }
        $data .= "<script>".
            "console.log(\"".
            "      ·" . $lang['system_operation'] . "·      \\n\\n".
            "-----------" . $lang['system'] . "-----------\\n".
            $lang['running_time'] . "：$runtime ms  \\n" . $lang['throughput'] . "：" . $reqs . " req/s\\n".
            $lang['require_info'] . "：" . date ('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri . "\\n".
            $lang['server_info'] . "：" . php_uname ("a") . "\\n".
            $lang['framework_version'] . "：" . VERSION . "\\n".
            $lang['php_version'] . "：" . phpversion () . "\\n".
            $lang['zend_version'] . "：" . zend_version () . "\\n".
            $lang['client_version'] . "：" . $_SERVER['HTTP_USER_AGENT'] . "\\n".
            $lang['interface_type'] . "：" . php_sapi_name () . "\\n".
            $lang['process_id'] . "：" . getmypid () . "\\n".
            $lang['index_node'] . "：" . getmyinode () . "\\n";
        if (session_id ()) {
            $data .= "SessionID：" . session_id () . "\\n\\n";
        } else {
            $data .= "SessionID：NULL\\n\\n";
        }
        $data .= "-----------" . $lang['memory'] . "-----------\\n".
            $lang['initial_memory'] . "：" . $this->format_bytes (SYSTEM_START_MEMORY) . "\\n".
            $lang['current_state'] . "：" . $this->format_bytes (memory_get_usage ()) . "\\n".
            $lang['total_consumption'] . "：" . $this->format_bytes ($mem) . "\\n".
            $lang['peak_occupancy'] . "：" . $this->format_bytes (memory_get_peak_usage ()) . "\\n\\n".
            "------------CPU-----------\\n";
        foreach (getrusage () as $key => $value) {
            $data .= "$lang[$key] $key ：$value\\n";
        }
        $data .= "\\n".
        "--------" . $lang['has_been_run'] . "-------\\n".
        implode ("\\n", $hasBeenRun) . "\\n".
        /** @lang text */ "---------------------------" . "\\n" . "%c Powered By %c " . FRAME_WORK_NAME  . " \"
        ,\"line-height:20px;background-color:#696969;color:white\"
        ,\"line-height:20px;background-color:#1E90FF;color:white\"
        )</script>";
        return $data;
    }
}