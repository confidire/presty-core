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

namespace presty\Response\Driver;

use presty\Response;

class View extends Response
{

    protected $view;

    public function __construct ($view,$content,$code = 200) {
        $this->init($content,$code);
        $this->view = $view;
    }

    public function handle ($content = "")
    {
        if(empty($content)) $content = $this->content;
        $this->filter ($this->content);

        return $this;
    }

    public function filter ($content = "")
    {
        if(empty($content)) $content = $this->content;
        return $this->view->filter($content)->render("%self%",$this);
    }

}