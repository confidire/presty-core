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

namespace presty\Facade;
use presty\Facade\Facade;

class Container extends Facade
{
    public static function setFacade () {
        return "\presty\Container\Container";
    }
}