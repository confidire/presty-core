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

namespace presty\exception;

use presty\Exception;

class InvalidReturnException extends Exception
{
    public function __construct ($errorMessage = '',$errorFile = __FILE__,$errorLine = __LINE__) {
        parent::throw ($errorFile,$errorLine,"EC100023","Return Value Error",$errorMessage);
    }
}