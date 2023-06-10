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

class AppEntrance extends Make
{
    protected $fileType = "entrance";

    protected function configure ()
    {
        parent::configure ();
        $this->setName ('make:app_entrance');
    }

    protected function getStub(): string
    {
        $stubPath = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;

        return $stubPath . 'app.entrance.stub';
    }

    protected function getPathName(string $name): string
    {
        return app()->getAppPath() . $this->getEntranceAppName() . '.php';
    }

    protected function getClassName(string $name): string
    {
        return "App\\Entrance";
    }

    protected function getEntranceAppName(): string
    {
        return ucfirst ($this->input->getArgument('name'));
    }

}