<?php

namespace presty\exception;

use presty\Exception;

class RunTimeException extends Exception
{
    public function __construct ($errorMessage = '',$errorFile = __FILE__,$errorLine = __LINE__,$errorCode = "EC100002") {
        parent::throw ($errorFile,$errorLine,$errorCode,"Runtime Error",$errorMessage);
    }
}