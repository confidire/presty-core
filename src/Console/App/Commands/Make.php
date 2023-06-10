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

use presty\exception\RunTimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

abstract class Make extends Command
{

    protected $fileType = "";

    protected $input;

    abstract protected function getStub();

    protected function configure ()
    {
        $this->addArgument('name', InputOption::VALUE_REQUIRED, "The name of the class");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $name = trim($input->getArgument('name'));
        $classname = $this->getClassName($name);
        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error> Error: ' . ucfirst ($this->fileType)."文件 ".$classname.".php 已存在！" . '</error>');
            return 0;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->filter ($classname));
        $output->writeln('<info>' . $this->fileType . ':' . $classname . ' built successfully.</info>');

        return 0;
    }

    protected function filter($name){
        $stub = file_get_contents($this->getStub());
        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
        $app = $this->getAppName ($name);
        $class = str_replace($namespace . '\\', '', $name);
        return str_replace(['%AppName%','%ClassName%', '%namespace%','%Time%'], [
            $app,
            ucfirst ($class),
            $namespace,
            date ("Y-m-d H:i:s"),
        ], $stub);
    }

    protected function getPathName(string $name): string
    {
        $name = ucfirst (substr($name, 4));
        return app()->getAppPath() . ltrim(str_replace('\\', DS, $name), '/') . '.php';
    }

    protected function getClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
            $app = ucfirst ($app);
            
        } else {
            $app = '';
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }
        return $this->getNamespace($app) . '\\' . $this->fileType . '\\' . $name;
    }

    protected function getAppName(string $className): string
    {
        $app = substr ($className,4);
        return explode ("\\",$app)[0];
    }

    protected function getNamespace(string $app): string
    {
        return 'app' . ($app ? '\\' . $app : '');
    }
}