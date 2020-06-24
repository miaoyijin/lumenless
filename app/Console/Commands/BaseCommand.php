<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /**
     * @param InputInterface $input input
     * @param OutputInterface $output output
     * @return int
     */
    final public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info("start at: " . date('Y-m-d H:i:s'));
        $res = $this->handle($input, $output);
        $this->info("end at: " . date('Y-m-d H:i:s'));
        return $res;
    }

    /**
     * @param InputInterface $input input
     * @param OutputInterface $output output
     * @return int
     */
    abstract protected function handle(InputInterface $input, OutputInterface $output) :int;
}
