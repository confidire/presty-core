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

namespace presty\Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BuildApp extends Command
{
    protected function configure ()
    {
        $this->setName ('make:app')
            ->addArgument('name', InputOption::VALUE_REQUIRED, "The name of the app");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $name = ucfirst(trim($input->getArgument('name')));
        if(is_dir (\presty\Container::getInstance ()->getAppPath() . $name)) {
            $output->writeln('<error> Error: 应用目录已存在！' . '</error>');
            return 0;
        }
        mkdir (\presty\Container::getInstance ()->getAppPath() . $name);
        mkdir (\presty\Container::getInstance ()->getAppPath() . $name . DS . "controller");
        mkdir (\presty\Container::getInstance ()->getAppPath() . $name . DS . "model");
        mkdir (\presty\Container::getInstance ()->getAppPath() . $name . DS . "config");
        system ("php presty make:app_entrance " . $name,$appEntranceBuildResult);
        system ("php presty make:controller " . $name . "@index true",$controllerBuildResult);
        system ("php presty make:model " . $name . "@index",$modelBuildResult);
        if(is_dir (\presty\Container::getInstance ()->getAppPath() . $name)) $output->writeln('<info>App: ' . $name . ' built successfully.</info>');
        else {
            $output->writeln('<error> Error: 应用目录创建失败！' . '</error>');
            return -1;
        }
        if($appEntranceBuildResult != 0 || $controllerBuildResult != 0 || $modelBuildResult != 0) $output->writeln('<error> Error: 部分文件创建失败，请手动检查应用目录完整性！' . '</error>');
        return 0;
    }
}