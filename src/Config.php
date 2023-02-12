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

class Config
{
    protected $config = [];

    public function init(): Config
    {
        self::scan (CONFIG,true,function($path,$name){
            $name = str_replace (".php","",$name);
            if(!is_bool (stripos ($path,"route"))) return;
            $data = include $path;
            if(!is_array ($data)) return;
            foreach ($data as $key => $value) {
                $this->config[strtolower ($name).".".$key] = $value;
            }
        });
        return $this;
    }

    public function get(string $name, $default = null)
    {
        if (isset($this->config[$name])) return $this->config[$name];
        return $default;
    }

    public function getAll()
    {
        return $this->config;
    }

    public function getFile(string $filePath)
    {
        if (file_exists($filePath)) return include $filePath;
        return false;
    }

    public function set($name = null, $value = null)
    {
        $this->config[$name] = $value;
    }

    public function overwrite(array $config)
    {
        $this->config = $config;
    }

    private static function scan ($dir,$deepScan,$function)
    {
        $temp = scandir ($dir);
        foreach ($temp as $v) {
            $a = $dir . $v;
            if (is_dir ($a)) {
                if ($v == '.' || $v == '..') {
                    continue;
                }
                if($deepScan)self::scan ($a,$deepScan,$function);
                else continue;
            } else {
                $fileFullPath = $a;
                $fileName = $v;
                $function($fileFullPath,$fileName);

            }
        }
    }
}