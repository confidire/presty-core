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

use presty\Exception;

class Error
{

    public static function runError ($errno = 2, $errStr = 'Undefined Error', $errFile = 'Undefined Error', $errLine = 0): bool
    {
        $errCode = "";

        if(!is_bool (stripos ($errStr,"Non-static method ")) && !is_bool (stripos ($errStr," should not be called statically"))) {
            $errCode = "EC100028";
            $errStr = str_replace (" should not be called statically","",str_replace ("Non-static method ","",$errStr));
        }
        elseif(!is_bool (stripos ($errStr,"Undefined property: "))) {
            $errCode = "EC100027";
            $errStr = str_replace ("Call to undefined method ","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"Array to string conversion"))) {
            $errCode = "EC100028";
            $errStr = str_replace ("Array to string conversion","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"Undefined variable: "))) {
            $errCode = "EC100030";
            $errStr = "$".str_replace ("Undefined variable: ","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"syntax error, unexpected"))) {
            $errStr = $errFile."第".$errLine."行结尾未添加分号或括号未闭合";
            $errCode = "EC100017";
            $errLine --;
        }

        switch ($errno) {
            case E_ERROR :
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100018", "Fatal Error", $errStr);
                break;
            case E_WARNING:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "System Warning", $errStr);
                break;
            case E_PARSE :
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100017", "Syntax Error", $errStr);
                break;
            case E_NOTICE:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "System Notice", $errStr);
                break;
            case E_USER_ERROR:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "User Report Error", $errStr);
                break;
            case E_USER_WARNING:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "User Report Warning", $errStr);
                break;
            case E_USER_NOTICE:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "User Report Notice", $errStr);
                break;
            case E_DEPRECATED:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "System Deprecated", $errStr);
                break;
            case E_RECOVERABLE_ERROR:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100018", "Catchable Fatal Error", $errStr);
                break;
            case E_USER_DEPRECATED:
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022", "User Report Deprecated", $errStr);
                break;
            default :
                Exception::throw($errFile, $errLine, !empty($errCode) ? $errCode :"EC100022","System Error", $errStr);
            }

        return true;
    }

    public static function runException ($e): bool
    {
        $errStr = $e->getMessage ();
        $errFile = $e->getFile ();
        $errLine = $e->getLine ();
        $errTrace = $e->getTrace ();
        $errCode = $e->getCode();
        switch ($errCode) {
            case E_ERROR :
                $errCode = "EC100018";
                $errType = "Fatal Error";
                break;
            case E_WARNING:
                $errCode = "EC100022";
                $errType = "System Warning";
                break;
            case E_PARSE :
                $errCode = "EC100017";
                $errType = "Syntax Error";
                break;
            case E_NOTICE:
                $errCode = "EC100022";
                $errType = "System Notice";
                break;
            case E_USER_ERROR:
                $errCode = "EC100022";
                $errType = "User Report Error";
                break;
            case E_USER_WARNING:
                $errCode = "EC100022";
                $errType = "User Report Warning";
                break;
            case E_USER_NOTICE:
                $errCode = "EC100022";
                $errType = "User Report Notice";
                break;
            case E_DEPRECATED:
                $errCode = "EC100022";
                $errType = "System Deprecated";
                break;
            case E_RECOVERABLE_ERROR:
                $errCode = "EC100022";
                $errType = "Catchable Fatal Error";
                break;
            case E_USER_DEPRECATED:
                $errCode = "EC100022";
                $errType = "User Report Deprecated";
                break;
            default :
                $errCode = "EC100022";
                $errType = "System Exception";
        }
        if(!is_bool (stripos ($errStr,"syntax error, "))) {
            $errCode = "EC100017";
            $errStr = str_replace ("syntax error, ","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"fatal error, "))) {
            $errCode = "EC100018";
            $errStr = str_replace ("syntax error, ","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"Class '")) && !is_bool (stripos ($errStr,"' not found"))) {
            $errCode = "EC100009";
            $errStr = str_replace ("' not found","",str_replace ("Class '","",$errStr));
        }
        elseif(!is_bool (stripos ($errStr,"Call to undefined function "))) {
            $errCode = "EC100010";
            $errStr = str_replace ("Call to undefined function ","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"Call to undefined method "))) {
            $errCode = "EC100026";
            $errStr = str_replace ("Call to undefined method ","",$errStr);
        }
        elseif(!is_bool (stripos ($errStr,"Too few arguments to function ")) && !is_bool (stripos ($errStr,"exactly"))) {
            $errCode = "null";
            $pattern = '/Too few arguments to function (\S*), (\d*) passed in (.*) on line (.*) and exactly (.*) expected/';
            $errStr = preg_replace ($pattern,"函数$1需要提供$5个参数，已提供$2个",$errStr);
            $errFile = $errTrace[0]["file"];
            $errLine = $errTrace[0]["line"];
        }
        elseif(!is_bool (stripos ($errStr,"Too few arguments to function ")) && !is_bool (stripos ($errStr,"least"))) {
            $errCode = "null";
            $pattern = '/Too few arguments to function (\S*), (\d*) passed in (.*) on line (.*) and at least (.*) expected/';
            $errStr = preg_replace ($pattern,"函数$1至少需要提供$5个参数，已提供$2个",$errStr);
            $errFile = $errTrace[0]["file"];
            $errLine = $errTrace[0]["line"];
        }
        elseif(!is_bool (stripos ($errStr,"syntax error, unexpected"))) {
            $errCode = "EC100017";
        }

        Exception::throw($errFile, $errLine, $errCode, $errType, $errStr,$errTrace);
        return true;
    }

    public static function appShutdown ()
    {
        if (!is_null ($error = error_get_last ()) && self::isFatal ($error['type'])) {
            self::runError ($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    protected static function isFatal ($type): bool
    {
        return in_array ($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    public static function init ()
    {
        error_reporting (E_ALL);
        set_error_handler ([__CLASS__, "runError"]);
        set_exception_handler ([__CLASS__, "runException"]);
        register_shutdown_function ([__CLASS__, "appShutdown"]);
    }
}