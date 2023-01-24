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
class ThrowError
{
    static function throw ($errfile, $errline, $errorcode = "System Error", $customSuffix = "", $errtitle = "", $errno = 2)
    {
        global $lang, $config, $hasBeenRun;
        $debug = debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS );
        $lang = lang();
        if ((isset($config['error_auto_clean']) && $config['error_auto_clean'])) {
            ob_clean ();
        } elseif ((!isset($config['error_auto_clean']) && $config['debug_mode'])) {
            ob_clean ();
        }
        if (isset($lang[$errorcode])) $errstr = $lang[$errorcode];
        else $errstr = $errtitle;
        if (!empty($customSuffix)) $errstr .= " - " . $customSuffix;
        if (env('system.debug.mode',false)) {
            global $memoryAnalysis;
            $memoryAnalysis->output ();
        }
        $errDetail = "[ StartPHP " . VERSION . " ]\r\n错误时间： " . date ('Y-m-d H:i:s') . "\r\n错误原因： $errstr\r\n错误网站： " . $_SERVER['HTTP_HOST'] . "\r\n错误文件： $errfile\r\n错误行数： $errline\r\n运行状态： [\r\n" . implode ("\r\n", $hasBeenRun) . "\r\n]";
        if ($config['save_error_log']) {
            \Log::errorOut ($errDetail);
        }
        if ($config['save_running_log']) {
            global $runningResult;
            $runningResult = '失败 - [ ' . $errstr . ' ]';
            $Detail = "[ StartPHP " . VERSION . " ] \r\n 执行时间： " . date ('Y-m-d H:i:s') . "\r\n 运行结果： " . $errstr . " \r\n User-Agent： " . $_SERVER['HTTP_USER_AGENT'];
            \Log::logOut ($Detail);
        }
        if ($config['send_error_log']) {
            $to = $config['developer_email'];                                 // 邮件接收者
            $subject = "[ StartPHP " . VERSION . " ] StartPHP框架运行报错提示邮件";  // 邮件标题
            $message = $errDetail;                                         // 邮件正文
            $from = "StartPHP框架运行报错提示邮件";                                  // 邮件发送者
            $headers = "From:" . $from;                                    // 头部信息设置
            mail ($to, $subject, $message, $headers);
        }
        if(!env('system.debug.mode',false)){
            $errstr = $lang['something_wrong'];
        }
        echo /** @lang text */ "<head>
        <title>" . $errstr . "</title>
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=no\">
        <style>
        body{
            color: #333;
            margin: 0;padding: 0 20px 20px;
            word-break: break-word;
            font: 14px Verdana, 'Helvetica Neue', helvetica, Arial, 'Microsoft YaHei', sans-serif;
            margin-top: 20px;
            line-height: 30px;
        }
        h1{
            margin: 10px 0 0;
            font-size: 28px;
            font-weight: 500;
            line-height: 32px;
        }
        pre{
            background-color: whitesmoke;
        }
        .detailed{
            border-top: 1px solid #eee;
        }
        .errorLine{
            background-color: lightpink;
        }
        .nums{
            float: left;
            line-height: 25px;
            text-align: end;
            background-color: whitesmoke;
            padding-right: .3rem;
            padding-left: 1rem;
            border-right: solid .1px darkgrey;
            margin-top: .5rem;
            margin-bottom: .5rem;
        }
        .code{
            
        }
        </style>
        </head>";
        if(env('system.debug.mode',false)) {
            echo ("<body>");
            echo ("<h1>" . $errstr . "</h1>" . "<p style='line-height:20px'>[错误等级：" . $errno . "]<br>[错误代码：" . $errorcode . "]<br>[错误时间：" . date ('Y-m-d H:i:s') . "]</p>");
            echo ("<div class=\"detailed\">");
            echo ("<h3>错误位置：<br></h3><p><u>" . $errfile . "</u> 第 <b>" . $errline . "</b> 行</p>");
            if(file_exists ($errfile)) {
                $file = file_get_contents ($errfile);
                $order = ["\r\n", "\n", "\r"];
                $replace = '<br/>';
                $content = str_replace ($order, $replace, $file);//解决某些文件的换行符是\n或\r，而有些是\r\n的问题
                $content = explode ($replace, $content);
                array_unshift ($content, ""); // 为了避免数组从0开始计算长度，而文件行数是从1开始计算行数的差异
                $content = array_slice (array_values ($content), $errline - 10, 20, true);
                echo ("<div class=\"nums\">");
                foreach ($content as $key => $value) {
                    echo ($key . ".<br>");
                }
                echo ("</div>");
                echo ("<pre style=\"font-family: ui-sans-serif;line-height: 25px;padding-left: 53px;padding-top: .5rem;padding-bottom: .5rem;\">");
                foreach ($content as $key => $value) {
                    $value = htmlentities ($value);
                    if ($key == $errline) {
                        print_r ("<div class=\"errorLine\">                 " . $value . "</div>");
                    } else {
                        print_r ("                   " . $value . "<br>");
                    }
                }
                echo ("</pre></div>");
            }
            else {
                echo ("<div class=\"nums\">");
                echo ("0". ".<br>");
                echo ("</div>");
                echo ("<pre style=\"font-family: ui-sans-serif;line-height: 25px;padding-left: 53px;padding-top: .5rem;padding-bottom: .5rem;\">");
                $content = printf ("%s", $lang['unable_get_file_content']);
                echo ("</pre></div>");
            }
            echo ("<div class=\"detailed\">");
            echo ("<h3>堆栈追踪：<br></h3>");
            echo ("<div style='line-height:20px'>");
            echo "<pre>" . htmlspecialchars (print_r ($debug, true)) . "</pre>";
            echo ("</div>");
            echo ("</div>");
            echo ("</body>");
            die();
        }
        else{
            echo ("<body style=\"\">");
            echo ("<h1>" . $errstr ."</h1><br>");
            echo ("<div class=\"detailed\">");
            echo ("<h4>本页面由<a href='https://startphp.catcatalpa.com' target='_blank' style='color: #333;'>StartPHP</a>默认错误报错提供支持</h4>");
            echo ("</body>");
            die();
        }
    }
}