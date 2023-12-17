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

namespace presty\Exception;
use presty\Exception;
use Psr\Cache\InvalidArgumentException as PsrCacheInvalidArgumentInterface;
use Psr\SimpleCache\InvalidArgumentException as SimpleCacheInvalidArgumentInterface;

//Presty Exception Class
class InvalidArgumentException extends Exception implements PsrCacheInvalidArgumentInterface,SimpleCacheInvalidArgumentInterface
{
    public function __construct ($errorMessage = '',$errorFile = __FILE__,$errorLine = __LINE__,$errCode = "EC100031")
    {
        parent::throw ($errorFile,$errorLine,$errCode,"Illegal Arguments",$errorMessage);
    }
}