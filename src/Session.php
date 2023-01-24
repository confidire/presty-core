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

class Session
{
    public function get ($sessionName,$sessionValue = ""): bool
    {
        if(is_string ($sessionName) && is_string ($sessionValue)){
            if($this->isset ($sessionName,$sessionValue)) return $_SESSION[$sessionName];
            else return false;
        }
        else return $_SESSION[$sessionName];
    }

    public function set ($sessionName,$sessionValue): bool
    {
        $_SESSION[$sessionName] = $sessionValue;
        return isset($_SESSION[$sessionName]);
    }

    public function unset ($sessionName): bool
    {
        unset($_SESSION[$sessionName]);
        return true;
    }

    public function des ($sessionName): bool
    {
        unset($_SESSION[$sessionName]);
        session_destroy ();
        return true;
    }

    public function isset ($sessionName,$sessionValue = ""): bool
    {
        if(empty($sessionValue)) return isset($_SERVER[$sessionName]);
        else return isset($_SERVER[$sessionName]) && $_SERVER[$sessionName] == $sessionValue;
    }

    public function empty ($sessionName): bool
    {
        return isset($_SESSION[$sessionName]) && empty($_SESSION[$sessionName]);
    }
}
