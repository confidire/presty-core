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

if (!function_exists ("scan")) {
    function scan ($dir,$deepScan,$function)
    {
        $temp = scandir ($dir);
        foreach ($temp as $v) {
            $a = $dir . $v;
            if (is_dir ($a)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if($deepScan)scan ($a,$deepScan,$function);
                else continue;
            } else {
                $fileFullPath = $a;
                $fileName = $v;
                $function($fileFullPath,$fileName);

            }
        }
    }
}

spl_autoload_register (function ($className) {
    global $hasBeenRun, $classalias,$route;
    $hasBeenRun['autoload'] = " - Autoload_Init";
    require_once (CONFIG . "Vendormap.php");

    if(empty($classalias)) $classalias = require_once CONFIG. "Classalias.php";

    if (isset($classalias[$className]) && !empty($classalias[$className])) {
        if(class_exists ($classalias[$className])) return class_alias ($classalias[$className], $className, false);
        else $className = $classalias[$className];
    }
    global $vendormap;
    $vendor = substr ($className, 0, strpos ($className, '\\'));
    if (empty($vendor)) \ThrowError::throw(__FILE__, __LINE__, "EC100013", $className);
    $vendor_dir = $vendormap[$vendor] ?? \ThrowError::throw(__FILE__, __LINE__, "EC100014", $vendor);
    $data = explode ("\\",$className);
    $rel_path = implode (DS,array_slice ($data,1,count($data)-2)) ?? "";
    if($rel_path == "/") $rel_path = "";
    $rel_file = basename (str_replace ("\\", "/", substr ($className, strlen ($vendor))));
    if ($vendor == "app") {
        global $url;
        if(in_array ("controller",$data)) {
            if (empty($url)) {
                \ThrowError::throw(/** @lang text */ "Error: Unable to get the app directory to which the controller to resolve belongs.<br>File Name : $className", __FILE__, __LINE__, "EC100001");

            }
                require_once (appPath () . "controller" . DS . $rel_file . ".php");
                $path = explode ("\\", $className);
                $file_path = $vendor_dir . $path[1] . DS . "controller" . DS . $path[count ($path) - 1] . ".php";
                if (is_file ($file_path)) require_once ($file_path);
                else \ThrowError::throw(/** @lang text */ "Error: Unable to get the app directory to which the controller to resolve belongs.<br>File Name : $file_path", __FILE__, __LINE__, "EC100001");
        }
        elseif(in_array ("model",$data)){
            if (empty($url)) {
                \ThrowError::throw(/** @lang text */ "Error: Unable to get the app directory to which the controller to resolve belongs.<br>File Name : $className", __FILE__, __LINE__, "EC100001");
            } else {
                $path = explode ("\\", $className);
                $file_path = $vendor_dir . $path[1] . DS . "model" . DS . $path[count ($path) - 1] . ".php";
                if (is_file ($file_path)) require_once ($file_path);
                else \ThrowError::throw(/** @lang text */ "Error: Unable to get the app directory to which the controller to resolve belongs.<br>File Name : $file_path", __FILE__, __LINE__, "EC100001");
            }
        }
    } else {
        if ($vendor == "startphp") {
            require_once (DIR . $rel_path . DS . $rel_file . ".php");
        } elseif ($vendor == "model") {
            $file = ROOT . "model/" . $rel_file . ".php";
            if (file_exists ($file)) {
                require_once ($file);
            } else {
                \ThrowError::throw("Error: Error:File '$file' containing class '$className' not found", __FILE__, __LINE__, "EC100002");
            }
        }
    }
    if (!defined ('FIRST_TRANSFER_AUTOLOAD')) define ('FIRST_TRANSFER_AUTOLOAD', 0);
    else {
        (new \startphp\Hook())->getClassName ('afterAutoload')->transfer ([$className, $rel_file]);
    }

}, true, true);
register_shutdown_function (function () {
    global $config;
    if ($config['save_running_log']) {
        global $hasBeenRun;
        global $runningResult;
        if (!isset($runningResult)) $runningResult = '成功';
        $Detail = "[ StartPHP " . VERSION . " ] \r\n 执行时间： " . date ('Y-m-d H:i:s') . "\r\n 运行结果： " . $runningResult . " \r\n User-Agent： " . $_SERVER['HTTP_USER_AGENT'];
        \Cache::logOut ($Detail);
        $hasBeenRun['run_end'] = " - System_Run_End";
        hook_getClassName ('appDestroy')->transfer ();
    }
});