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

class Version extends Command
{
    protected function configure ()
    {
        $this->setName ('version')
            ->setDescription ('Prints Hello-World!')
            ->setHelp ('Demonstration of custom commands created by Symfony Console component.');
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $output->writeln (sprintf ('v%s', VERSION));
        return 0;
    }
}