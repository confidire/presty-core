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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;

class Run extends Command
{
    protected $files = [];

    protected function configure ()
    {
        $this->setName ('run')
            ->setDescription ('View the local server built into the PHP system.')
            ->addOption ('host', 's', InputOption::VALUE_OPTIONAL, 'host to server', "127.0.0.1")
            ->addOption ('port', 'o', InputOption::VALUE_OPTIONAL, 'port to server', 8888)
            ->addOption ('path', 'a', InputOption::VALUE_OPTIONAL, 'path to server', ROOT . "public");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption ('host');
        $port = $input->getOption ('port');
        $path = $input->getOption ('path');
        $io = new SymfonyStyle($input, $output);
        $bar = new ProgressBar($output,3);
        $bar->setMessage('System Self-test...');
        $bar->setFormat("%message%\r\n%current%/%max% [%bar%]  %percent%%");
        $bar->start();
        sleep(1);
        $bar->setMessage('Checking PHP version...');
        sleep(2);
        if (version_compare(PHP_VERSION, MINIMUM_PHP_VERSION) < 0) {
            echo "<error>Your PHP version is below the minimum version required for Presty to run (".MINIMUM_PHP_VERSION.")</error>";
            exit(-1);
        }else{
            $bar->advance(1);
        }
        $bar->setMessage('Checking server entry file...');
        sleep(2);
        if(!file_exists ($path . DIRECTORY_SEPARATOR . 'test.php')){
            echo "<error>Entry file is not exists (".$path . DIRECTORY_SEPARATOR . 'test.php'.")</error>";
            exit(-1);
        }
        else{
            $bar->advance(1);
        }
        $bar->setMessage('Server startup preparation...');
        sleep(3);
        $command = sprintf (
            'php -S %s:%d -t %s %s', $host, $port, escapeshellarg ($path), escapeshellarg ($path . DIRECTORY_SEPARATOR . 'test.php')
        );
        $bar->setMessage('Server self-test successful!');
        $bar->finish();
        $io->newLine();
        $io->success('Presty local web server is starting...');
        sleep(3);
        $debugMode = env("system.debug_mode",false) ? "<comment>Enable</comment>" : "<info>Unable</info>";
        $databaseAutoLoad = get_config("env.database_auto_load",false) ? "<info>Enable</info>" : "<comment>Unable</comment>";
        $sessionAutoloada = get_config("env.session_auto_load",false) ? "<info>Enable</info>" : "<comment>Unable</comment>";
        $saveLogs = get_config("env.save_running_log",false) ? "<info>Enable</info>" : "<comment>Unable</comment>";
        $xssProtect = get_config("env.auto_xss_protect",false) ? "<info>Enable</info>" : "<comment>Unable</comment>";
        $indexRoute = get_config("env.use_system_index_route",false) ? "<info>Enable</info>" : "<comment>Unable</comment>";
        $output->writeln ("  _____               _         \r\n |  __ \             | |\r\n | |__) | __ ___  ___| |_ _   _ \r\n |  ___/ '__/ _ \/ __| __| | | |\r\n | |   | | |  __/\__ \ |_| |_| |\r\n |_|   |_|  \___||___/\__|\__, |\r\n                           __/ |\r\n                          |___/ \r\n");
        $output->writeln ("---------------------------------------------------------------");
        $output->writeln ("·PHP: v".PHP_VERSION."                       "."·Zend: v".zend_version ());
        $output->writeln ("·Presty: v".MAIN_VERSION."              "."·Load Mode: ".php_sapi_name ());
        $output->writeln ("·Debug Mode: ".$debugMode."                "."·Database Auto Load: ".$databaseAutoLoad);
        $output->writeln ("·Session Auto Load: ".$sessionAutoloada."         "."·Save Running Log: ".$saveLogs);
        $output->writeln ("·XSS Protect: ".$xssProtect."               "."·Index Page Route: ".$indexRoute);
        $output->writeln ("---------------------------------------------------------------");
        $output->writeln (sprintf ('Your Presty Server is successfully started on <info><http://%s:%s/></info>', $host, $port));
        $output->writeln (sprintf ('Your site\'s root is: %s', $path));
        $output->writeln ('You can press <info>`CTRL-C`</info> to stop running');
        $output->writeln ("Running Logs:");
        system ($command);
        return 0;
    }
}