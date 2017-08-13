<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 7:35 PM
 */

namespace Migration\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScriptCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:script')
            ->setDescription('Executes a single script')
            ->addArgument('script', InputArgument::REQUIRED)
            ->addArgument('batch', InputArgument::REQUIRED, "Batch size for each transaction", 100)

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $scriptName = ucfirst($input->getArgument('script'));

        $scriptClass = "Migration\\Scripts\\{$scriptName}MigrationScript";
        if(class_exists($scriptClass)) {
            $total = (new $scriptClass($this->config))->execute();
            $this->say($total . ' Rows updated');

        } else {
            throw new \Exception('Migration script not found: '. $scriptClass);
        }

        //$this->say('Just running '. $input->getArgument('script'));
    }


}