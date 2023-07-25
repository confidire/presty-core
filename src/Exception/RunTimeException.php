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

namespace presty\exception;

use presty\Exception;

class RunTimeException extends Exception
{
    public function __construct ($errorMessage = '',$errorFile = __FILE__,$errorLine = __LINE__,$errorCode = "EC100002") {
        parent::throw ($errorFile,$errorLine,$errorCode,"Runtime Error",$errorMessage);
    }
}