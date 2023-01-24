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

class Run extends Command
{
    protected $files = [];

    protected function configure ()
    {
        $this->setName ('run')
            ->setDescription ('Start the local server built into the PHP system.')
            ->addOption ('host', 's', InputOption::VALUE_OPTIONAL, 'host to server', "127.0.0.1")
            ->addOption ('port', 'o', InputOption::VALUE_OPTIONAL, 'port to server', 8888)
            ->addOption ('path', 'a', InputOption::VALUE_OPTIONAL, 'path to server', ROOT . "public");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption ('host');
        $port = $input->getOption ('port');
        $path = $input->getOption ('path');
        $command = sprintf (
            'php -S %s:%d -t %s %s', $host, $port, escapeshellarg ($path), escapeshellarg ($path . DIRECTORY_SEPARATOR . 'test.php')
        );
        $output->writeln (sprintf ('Your Presty Server is started On <info><http://%s:%s/></info>', $host, $port));
        $output->writeln (sprintf ('You can press <info>`CTRL-C`</info> to stop running'));
        $output->writeln (sprintf ('Your site\'s root is: %s', $path));
        system ($command);
        return 0;
    }
}