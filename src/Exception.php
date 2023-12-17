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
use presty\Dumper\Driver\Console;
use presty\Facade\Template;

class Exception
{
    static function throw ($file, $line, $code = "EC100001", $type = "System Error", $title = "",$trace = "")
    {
        if ((Container::getInstance ()->make("config")->get ('env.error_auto_clean') !== null && Container::getInstance ()->make("config")->get('env.error_auto_clean'))) {
            ob_clean ();
        } elseif ((Container::getInstance ()->make("config")->get ('env.error_auto_clean') === null && env ('system.debug.mode', false))) {
            ob_clean ();
        }
        if (isset(self::lang()[$code]) && !empty($title)) $errStr = self::lang()[$code] . " : ".$title;
        elseif(isset(self::lang()[$code])) $errStr = self::lang()[$code];
        elseif($code = "null") $errStr = $title;
        else $errStr = "未捕获错误 : ".$title;
        if (self::env('system.debug.mode',false) && !Container::getInstance ()->runningInConsole()) {
            Container::getInstance ()->make("debugMode")->output ();
        }
        $errDetail = "[ presty " . VERSION . " ]\r\n错误时间： " . date ('Y-m-d H:i:s') . "\r\n错误原因： $errStr\r\n错误网站： " . ($_SERVER['HTTP_HOST'] ?? "") . "\r\n错误文件： $file\r\n错误行数： $line\r\n运行状态： [\r\n" . implode ("\r\n", Container::getInstance ()->get("hasBeenRun")) . "\r\n]";
        if (Container::getInstance ()->make("config")->get('env.save_error_log')) {
            \presty\Facade\Log::errorOut ($errDetail);
        }
        if (Container::getInstance ()->make("config")->get('env.save_running_log')) {
            $Detail = "[ presty " . VERSION . " ] \r\n 执行时间： " . date ('Y-m-d H:i:s') . "\r\n 运行结果： " . $errStr . " \r\n User-Agent： " . $_SERVER['HTTP_USER_AGENT'];
            \presty\Facade\Log::logOut ($Detail);
        }
        if (Container::getInstance ()->make("config")->get('env.send_error_log')) {
            $to = Container::getInstance ()->make("config")->get('env.developer_email');                                 // 邮件接收者
            $subject = "[ presty " . VERSION . " ] Presty框架运行报错提示邮件";                // 邮件标题
            $message = $errDetail;                                                   // 邮件正文
            $from = "Presty框架";                                              // 邮件发送者
            $headers = "From:" . $from;                                              // 头部信息设置
            mail ($to, $subject, wordwrap($message,70), $headers);
        }
        $data = self::renderAsArray(["file" => $file,"line" => $line,"code" => $code,"type" => $type,"message" => $errStr,"trace" => $trace]);
        if(Container::getInstance ()->runningInConsole()) self::renderToConsole ($data);
        else self::render($data)->send();
    }

    static public function render (array $data = [])
    {
        $path = Template::getTemplatePath(Container::getInstance ()->make("config")->get("env.run_exception_template","RunException"));
        if(Container::getInstance ()->make("request")->isJson()) $response = Container::getInstance ()->make("response")->create (self::renderAsArray ($data), 'json', 500)->handle();
        else $response = Container::getInstance ()->make("response")->create (self::getRenderContent ($path,$data), 'html', 500, [app ()->make("viewQueue")->getMainView ()]);
        return $response;
    }

    static public function renderToConsole (array $data = [])
    {
        (new Console())->setErrorCode (2)->renderError(self::renderAsArray ($data));
    }

    public static function renderAsArray (array $data = [])
    {
        $result = [];
        $request = Container::getInstance ()->make("request");
        if(self::env("system.debug_mode",false) || Container::getInstance ()->make("config")->get ('env.show_error_detail',false)){
            if(!empty($data)){
                $result = [
                    "code" => $data["code"],
                    "message" => $data["message"],
                    "file" => $data["file"],
                    "line" => $data["line"],
                    "type" => $data["type"],
                    "trace" => $data["trace"],
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
                    "code" => $data["code"],
                    "message" => self::lang()['something_wrong'],
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

    private static function app ()
    {
        return \presty\Container::getInstance ()->getClass('app');
    }

    private static function lang ($index = "")
    {
        if(empty($index)) return Container::getInstance ()->make("lang")->lang();
        else return Container::getInstance ()->make("lang")->self::lang()[$index];
    }

    private static function env ($name,$default = "")
    {
        return \presty\Env::get($name,$default);
    }
}