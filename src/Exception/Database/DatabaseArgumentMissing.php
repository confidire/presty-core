<?php

namespace presty\exception\database;

class DatabaseArgumentMissing extends \Exception
{
    public function __construct ($errorMessage = '',$errorFile = __FILE__,$errorLine = __LINE__,$errorCode = "EC100003")
    {
        parent::throw ($errorFile,$errorLine,$errorCode,"Database Error","参数缺失：".$errorMessage);
    }
}