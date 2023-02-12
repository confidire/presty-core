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
use presty\Dumper\Driver\Console;
use presty\Facade\Template;

class Exception
{
    static function throw ($errFile, $errLine, $errCode = "EC100001", $errType = "System Error", $errTitle = "",$errTrace = "")
    {
        if ((get_config ('env.error_auto_clean') !== null && get_config('env.error_auto_clean'))) {
            ob_clean ();
        } elseif ((get_config ('env.error_auto_clean') === null && get_config('env.debug_mode'))) {
            ob_clean ();
        }
        if (isset(lang()[$errCode]) && !empty($errTitle)) $errStr = lang()[$errCode] . " : ".$errTitle;
        elseif(isset(lang()[$errCode])) $errStr = lang()[$errCode];
        elseif($errCode = "null") $errStr = $errTitle;
        else $errStr = "未捕获错误 : ".$errTitle;
        if (env('system.debug.mode',false) && !app()->runningInConsole()) {
            app()->make("debugMode")->output ();
        }
        $errDetail = "[ presty " . VERSION . " ]\r\n错误时间： " . date ('Y-m-d H:i:s') . "\r\n错误原因： $errStr\r\n错误网站： " . ($_SERVER['HTTP_HOST'] ?? "") . "\r\n错误文件： $errFile\r\n错误行数： $errLine\r\n运行状态： [\r\n" . implode ("\r\n", app()->has("hasBeenRun","",true)) . "\r\n]";
        if (get_config('env.save_error_log')) {
            \presty\Facade\Log::errorOut ($errDetail);
        }
        if (get_config('env.save_running_log')) {
            $Detail = "[ presty " . VERSION . " ] \r\n 执行时间： " . date ('Y-m-d H:i:s') . "\r\n 运行结果： " . $errStr . " \r\n User-Agent： " . $_SERVER['HTTP_USER_AGENT'];
            \presty\Facade\Log::logOut ($Detail);
        }
        if (get_config('env.send_error_log')) {
            $to = get_config('env.developer_email');                                 // 邮件接收者
            $subject = "[ presty " . VERSION . " ] Presty框架运行报错提示邮件";                // 邮件标题
            $message = $errDetail;                                                   // 邮件正文
            $from = "Presty框架";                                              // 邮件发送者
            $headers = "From:" . $from;                                              // 头部信息设置
            mail ($to, $subject, wordwrap($message,70), $headers);
        }
        $data = ["errFile" => $errFile,"errLine" => $errLine,"errCode" => $errCode,"errType" => $errType,"errStr" => $errStr,"errTrace" => $errTrace];
        if(app()->runningInConsole()) self::renderToConsole ($data);
        else self::render($data)->send();
    }

    static public function render (array $data = [])
    {
        $path = Template::getTemplatePath(get_config("env.run_exception_template","RunException"));
        if(app()->make("request")->isJson()) $response = app()->make("response")->create (self::renderAsArray ($data), 'json', 500, [app ()->make("viewQueue")->getMainView ()]);
        else $response = app()->make("response")->create (self::getRenderContent ($path,$data), 'html', 500, [app ()->make("viewQueue")->getMainView ()]);
        return $response;
    }

    static public function renderToConsole (array $data = [])
    {
        (new Console())->setErrorCode (2)->renderError(self::renderAsArray ($data));
    }

    public static function renderAsArray (array $data = [])
    {
        $result = [];
        $request = app()->make("request");
        if(env("system.debug_mode")){
            if(!empty($data)){

                $result = [
                    "code" => $data["errCode"],
                    "message" => $data["errStr"],
                    "file" => $data["errFile"],
                    "line" => $data["errLine"],
                    "trace" => $data["errTrace"],
                    "request" => [
                        "requestMethod" => $request->method(),
                        "get" => $request->get(),
                        "post" => $request->post(),
                        "request" => $request->request(),
                        "cookie" => $request->cookie(),
                        "session" => $request->session(),
                        "env" => $request->env(),
                        "files" => $request->files(),
                    ],
                ];
            }
        }
        else{
            if(!empty($data)){
                $result = [
                    "code" => $data["errCode"],
                    "message" => lang()['something_wrong'],
                ];
            }
        }
        return $result;
    }

    public static function getRenderContent ($path,$data = [])
    {
        ob_start ();
        extract ($data);
        include $path;
        return ob_get_clean ();
    }
}