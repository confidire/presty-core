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

class Request
{

    protected $server = [];

    protected $method = 'GET';

    protected $post = [];

    protected $get = [];

    protected $request = [];

    protected $files = [];

    protected $session = [];

    protected $cookie = [];

    protected $host = "";

    protected $port = 0;

    protected $domain = "";

    protected $controller = "";

    protected $controllerPath = "";

    protected $controllerApp = "";

    protected $url = "";

    protected $siteUrl = "";

    protected $scheme = "";

    protected $controllerClass = "";

    protected $controllerFunction = "";

    protected $controllerArgs = [];

    protected $env = "";

    protected $config = "";

    protected $global = "";

    protected $httpUserAgent = "";

    protected $requestMethod = "";

    protected $userPLATFORM = "";

    protected $remotePort = 0;

    protected $requestUrl = "";

    protected $requestPage = "";

    protected $requestPagePath = "";

    protected $mimeType = [
        'xml'   => 'application/xml,text/xml,application/x-xml',
        'json'  => 'application/json,text/x-json,application/jsonrequest,text/json',
        'js'    => 'text/javascript,application/javascript,application/x-javascript',
        'css'   => 'text/css',
        'rss'   => 'application/rss+xml',
        'yaml'  => 'application/x-yaml,text/yaml',
        'atom'  => 'application/atom+xml',
        'pdf'   => 'application/pdf',
        'text'  => 'text/plain',
        'image' => 'image/png,image/jpg,image/jpeg,image/pjpeg,image/gif,image/webp,image/*',
        'csv'   => 'text/csv',
        'html'  => 'text/html,application/xhtml+xml,*/*',
    ];

    protected $httpsAgentName = '';

    public function __construct ()
    {

        app ()->setArrayVar ("hasBeenRun", "request", " - [".date("Y-m-d H:i:s")."] => Request_Init");

        $this->server = $_SERVER;

        $this->method = ($this->server['REQUEST_METHOD'] ?? "");

        $this->post = $_POST;

        $this->get = $_GET;

        $this->config = self::get_all_config();

        $this->env = $_ENV;

        $this->request = $_REQUEST;

        $this->files = $_FILES;

        $this->domain = ($this->server['HTTP_HOST'] ?? "");

        $this->port = ($this->server['SERVER_PORT'] ?? "");

        $this->httpUserAgent = ($this->server['HTTP_USER_AGENT'] ?? "");

        $this->requestMethod = ($this->server['REQUEST_METHOD'] ?? "");

        $this->userPLATFORM = ($this->server['HTTP_USER_AGENT'] ?? "");

        $this->remotePort = ($this->server['REMOTE_PORT'] ?? "");

        $this->requestUrl = ($this->server['REQUEST_URI'] ?? "");

        $this->scheme = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';

        $this->siteUrl = $this->scheme . ($_SERVER['HTTP_HOST'] ?? "") . ($_SERVER['REQUEST_URI'] ?? "");

        if (session_status () === PHP_SESSION_ACTIVE) $this->session = $_SESSION;

        $this->cookie = $_COOKIE;

        $this->env = array_merge ($this->env, $_ENV);

    }

    public function __call ($name, $arguments)
    {
        $this->env = get_all_config ();
        $this->global = $GLOBALS;
        if (function_exists ($name) && $name != "env") {
            call_user_func_array ($name, $arguments);
        } else {
            /** @var TYPE_NAME $returnText */
            eval("\$returnText = \$this->$name;");
            return $returnText;
        }
        return 0;
    }

    public function args ($returnString = false)
    {
        if(empty($this->url)) return false;
        if (!$returnString) return $this->controllerArgs;
        if (!is_null ($this->controllerArgs)) {
            $arg = [];
            foreach ($this->controllerArgs as $key => $value) {
                $arg = array_merge ($arg, ["$key=$value"]);
            }
            return implode ("&", $arg);
        }
        return "";
    }

    public function setUrl($url){

        $this->url = $url;

        $this->controller = $url['controller'];

        $this->controllerApp = $url['app'];

        $this->controllerPath = $url['app'];

        $this->controllerClass = ucfirst ($url['controller']);

        $this->controllerFunction = $url['function'];

        $this->controllerArgs = $url['vars'];

        if (!is_null ($this->controllerArgs)) {
            $arg = [];
            foreach ($url['vars'] as $key => $value) {
                if(empty($value)) $arg = array_merge ($arg,["$key"]);
                else $arg = array_merge ($arg, ["$key=$value"]);
            }
            $arg = implode ("&", $arg);
        } else $arg = "";
    }

    public function setRequestPage ($page,$fullPath)
    {
        $this->requestPage = $page;
        $this->requestPagePath = $fullPath;
    }

    public function server(string $name = '', string $default = '')
    {
        if (empty($name)) {
            return $this->server;
        } else {
            $name = strtoupper($name);
        }

        return $this->server[$name] ?? $default;
    }


    public function type(): string
    {
        $accept = $this->server('HTTP_ACCEPT');

        if (empty($accept)) {
            return '';
        }

        foreach ($this->mimeType as $key => $val) {
            $array = explode(',', $val);
            foreach ($array as $k => $v) {
                if (stristr($accept, $v)) {
                    return $key;
                }
            }
        }

        return '';
    }

    public function isJson(): bool
    {
        $acceptType = $this->type();

        return false !== strpos($acceptType, 'json');
    }

    public function isSsl(): bool
    {
        if ($this->server('HTTPS') && ('1' == $this->server('HTTPS') || 'on' == strtolower($this->server('HTTPS')))) {
            return true;
        } elseif ('https' == $this->server('REQUEST_SCHEME')) {
            return true;
        } elseif ('443' == $this->server('SERVER_PORT')) {
            return true;
        } elseif ('https' == $this->server('HTTP_X_FORWARDED_PROTO')) {
            return true;
        } elseif ($this->httpsAgentName && $this->server($this->httpsAgentName)) {
            return true;
        }

        return false;
    }

}
