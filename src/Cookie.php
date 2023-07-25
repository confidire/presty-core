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

class Cookie
{

    public function get ($cookieName,$cookieValue = "")
    {
        if(is_string ($cookieName) && is_string ($cookieValue)){
            if($this->isset ($cookieName,$cookieValue)) return $_COOKIE[$cookieName];
            else return false;
        }
        else return $_COOKIE[$cookieName];
    }

    public function set ($cookieName, $cookieValue, $expire = 0, $path = "", $domain = "",$secure = false,$httponly = false): bool
    {
        setcookie ($cookieName,$cookieValue,$expire,$path,$domain,$secure,$httponly);
        return isset($_COOKIE[$cookieName]);
    }

    public function des ($cookieName): bool
    {
        setcookie($cookieName, "", time()-3600);
        return true;
    }

    public function isset ($cookieName,$cookieValue = ""): bool
    {
        if(empty($cookieValue)) return isset($_COOKIE[$cookieName]);
        else return isset($_COOKIE[$cookieName]) && $_COOKIE[$cookieName] == $cookieValue;
    }

    public function empty ($cookieName): bool
    {
        return isset($_COOKIE[$cookieName]) && empty($_COOKIE[$cookieName]);
    }
}