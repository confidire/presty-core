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

class Language
{
    protected $config = [];

    protected $lang = [];

    protected $header = "";

    protected $app = null;

    public function __construct ($app = "")
    {
        if(empty($app)) $app = app();
        $this->app = $app;
        $this->config = $this->config ();
        $this->header = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    public function load ($name = "")
    {
        if ($this->app->newInstance("config")->get('language.judge_header_info',false)) $lang = $this->chooseHeaderLanguage ($this->parseHeader ($_SERVER['HTTP_ACCEPT_LANGUAGE']));
        if(empty($name)) $lang = $this->app->newInstance("config")->get('language.default_languages',"zh-cn");
        $this->lang = require_once (LANGUAGES . strtolower($lang) . ".php");
        return $this->lang;
    }

    public function config ()
    {
        return require CONFIG . "Language.php";
    }

    public function parseHeader ($header = "")
    {
        $header = $header ?? $this->header;
        if (empty($header)) return $header;
        $header = explode (",", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $default = $header[0];
        $data = [];
        $header = array_slice ($header, 1);
        $priority = 0;
        foreach ($header as $key => $value) {
            $value = str_replace ("q=", "", $value);
            $value = explode (";", $value);
            $data = array_merge ($data, [$value[1] => $value[0]]);
        }
        $data[1] = $default;
        arsort ($data);
        return $data;
    }

    public function chooseHeaderLanguage ($header = "")
    {
        $header = $header ?? $this->header;
        if (empty($header)) return $header;
        $result = [];
        $info = "";
        foreach ($header as $value) {
            $value = strtolower ($value);
            $data = $this->getAllLanguagesPacket (LANGUAGES,true);
            if(count ($data) == 1) return $data[0];
            foreach ($data as $v) {
                if (strstr( $v , $value ) !== false ){
                    array_push($result, $v);
                }
            }
            if(count ($result) == 1){
                if($this->app->newInstance("config")->get('language.limit_load_all_language',false) && !in_array ($result,$this->app->newInstance("config")->get('language.allow_load_languages',['zh-cn']))){
                    continue;
                }
                $info = $result[0];
                break;
            }else{
                foreach ($result as $i) {
                    if($this->app->newInstance("config")->get('language.limit_load_all_language',false) && !in_array ($i,$this->app->newInstance("config")->get('language.allow_load_languages',['zh-cn']))){
                        continue;
                    }
                    $info = $i;
                    break;
                }
            }
        }
        if(empty($info)) $info = $this->app->newInstance("config")->get('language.default_languages',"zh-cn");
        return $info;
    }

    public function getAllLanguagesPacket ($dir, $deepScan)
    {
        $temp = scandir ($dir);
        $list = [];
        foreach ($temp as $v) {
            $a = $dir . $v;
            if (is_dir ($a)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if ($deepScan) $this->getAllLanguagesPacket ($a, $deepScan);
                else continue;
            } else {
                $list[] = str_replace (".php","",str_replace ($dir,"",$a));
            }
        }
        return $list;
    }

    public function get ($name)
    {
        return $this->lang[$name] ?? false;
    }

    public function lang ()
    {
        return $this->lang ?? false;
    }
}