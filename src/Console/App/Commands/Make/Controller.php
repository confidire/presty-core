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

namespace presty\Console\App\Commands\Make;

use presty\Console\App\Commands\Make;
use Symfony\Component\Console\Input\InputArgument;

class Controller extends Make
{
    protected $fileType = "controller";

    protected function configure ()
    {
        parent::configure ();
        $this->setName ('make:controller')
             ->addArgument('empty', InputArgument::OPTIONAL, "Make an empty controller class");
    }

    protected function getStub(): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        if($this->input->getArgument('empty')) return $stubPath . 'controller.empty.stub';
        return $stubPath . 'controller.stub';
    }
}