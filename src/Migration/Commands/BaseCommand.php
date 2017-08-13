<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 7:41 PM
 */

namespace Migration\Commands;


use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BaseCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $out;

    /**
     * @var InputInterface
     */
    protected $in;

    protected $config;

    /**
     * ScriptCommand constructor.
     * @param $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->out = $output;
        $this->in = $input;
    }

    protected function say($content, $level = OutputInterface::VERBOSITY_NORMAL)
    {
        if (is_null($this->out)) {
            throw new \RuntimeException('No OutputInterface was set to AppBundle\Traits\Command::$_out');
        }

        if ($this->out->getVerbosity() >= $level) {
            $this->out->writeln(date('[Y-m-d H:i:s] ').$content);
        }
    }



}