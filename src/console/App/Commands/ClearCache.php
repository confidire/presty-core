<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Tomanday All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace presty\Console\App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ClearCache extends Command
{
    protected $files = [];

    protected function configure ()
    {
        $this->setName ('clear:cache')
            ->setDescription ('Clears the application cache.')
            ->addOption ('path', 'p', InputOption::VALUE_OPTIONAL, 'path to clear', null)
            ->addOption ('cache', 'c', InputOption::VALUE_NONE, 'clear cache file')
            ->addOption ('dir', 'd', InputOption::VALUE_NONE, 'clear empty dir')
            ->setDescription ('Clear cache files');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $output->writeln (lang()['cache_clear_start']);
        if ($input->getOption ('cache')) {
            $path = ROOT . 'cache';
        } else {
            $path = $input->getOption ('path') ?: CACHE;
        }
        $rmdir = (bool)$input->getOption ('dir');
        if (substr ($path, -1, 1) == "/") {
            $path = substr (rtrim ($path, DIRECTORY_SEPARATOR), 0, strlen ($path) - 1);
        } else {
            $path = rtrim ($path, DIRECTORY_SEPARATOR);
        }
        if(!is_dir ($path)) {
            $output->writeln ("<comment>" . "没有任何文件被清理" . "</comment>");
            return 0;
        }
        $files = $this->clear ($path, $rmdir);
        $output->writeln (lang()['has_been_cleared_files_prefix'] . count ($files) . lang()['has_been_cleared_files_suffix']);
        print_r ($files);
        $output->writeln ("<info>" . lang()['cache_clear_successful'] . "</info>");
        $this->files = [];
        return 0;
    }

    protected function clear ($path, $rmdir)
    {
        $temp = scandir ($path);
        foreach ($temp as $v) {
            $a = $path . DS . $v;
            if (is_dir ($a)) {//如果是文件夹则执行
                if ($v == '.' || $v == '..') {//判断是否为系统隐藏的文件.和..  如果是则跳过
                    continue;
                }
                if ($rmdir) {
                    $this->files[count ($this->files) + 1] = $a;
                }
                $this->clear ($a, $rmdir);//因为是文件夹所以再次调用 selectdir，把这个文件夹下的文件遍历出来
                rmdir ($a);
            } else {
                $this->files[count ($this->files) + 1] = $a;
                unlink ($a);
            }
        }
        return $this->files;
    }
}