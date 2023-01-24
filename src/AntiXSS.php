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
class AntiXSS
{
    public function antiXss ($str)
    {
        //php防注入和XSS攻击通用过滤
        $_GET && $this->SafeFilter ($_GET);
        $_POST && $this->SafeFilter ($_POST);
        $_COOKIE && $this->SafeFilter ($_COOKIE);
        return $this->SafeFilter ($str);
    }

    function SafeFilter (&$arr)
    {
        $ra = ['/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '/script/', '/javascript/', '/vbscript/', '/expression/', '/applet/'
            , '/meta/', '/xml/', '/blink/', '/link/', '/style/', '/embed/', '/object/', '/frame/', '/layer/', '/title/', '/bgsound/'
            , '/base/', '/onload/', '/onunload/', '/onchange/', '/onsubmit/', '/onreset/', '/onselect/', '/onblur/', '/onfocus/',
            '/onabort/', '/onkeydown/', '/onkeypress/', '/onkeyup/', '/onclick/', '/ondblclick/', '/onmousedown/', '/onmousemove/'
            , '/onmouseout/', '/onmouseover/', '/onmouseup/', '/onunload/'];

        if (is_array ($arr)) {
            foreach ($arr as $key => $value) {
                if (!is_array ($value)) {
                    if (!get_magic_quotes_gpc ()) {
                        $value = addslashes ($value);
                    }
                    $value = preg_replace ($ra, '', $value);
                    $arr[$key] = htmlentities (strip_tags ($value));
                } else {
                    SafeFilter ($arr[$key]);
                }
            }
        }
    }
}