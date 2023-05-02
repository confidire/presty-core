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

class Env
{
    public static function loadFile($filePath = "")
    {
        if(empty($filePath))$filePath = ROOT.".env";
        if (!file_exists($filePath)) return false;
        $env = parse_ini_file($filePath, true);
        foreach ($env as $name => $value) {
            if(is_array ($value)){
                $parentNode = $name;
                foreach ($value as $n => $v) {
                    if($n == "DEBUG_MODE" && $v == "1") $v = 0;
                    elseif($n == "DEBUG_MODE" && $v == "") $v = 1;
                    $envItem = $parentNode."_$n=$v";
                    putenv($envItem);
                }
            }
            else{
                $envItem = "$name=$value";
                putenv($envItem);
            }
        }
    }

    public static function get(string $name, $default = null)
    {
        $result = getenv(strtoupper(str_replace('.', '_', $name)));

        if (false !== $result) {
            if(strtoupper(str_replace('.', '_', $name)) == "SYSTEM_DEBUG_MODE" && $result == 0) $result = true;
            elseif(strtoupper(str_replace('.', '_', $name)) == "SYSTEM_DEBUG_MODE" && $result == 1) $result = false;
            return $result;
        }
        return $default;
    }
}