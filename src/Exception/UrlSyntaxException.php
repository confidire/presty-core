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

//Presty Exception Class
class UrlSyntaxException extends Exception
{
    public function __construct ($errorMessage = '',$errorFile = __FILE__,$errorLine = __LINE__,$errCode = "EC100032")
    {
        parent::throw ($errorFile,$errorLine,$errCode,"Url Syntax Error",$errorMessage);
    }
}