<?php
/**
 * Created for migration with PhpStorm.
 * Created by: Anis Ahmad <anis.programmer@gmail.com>
 * Created at: 8/12/17 7:35 PM
 */

namespace Migration\Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ScriptCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('migrate:script')
            ->setDescription('Executes a single script')
            ->addArgument('script', InputArgument::REQUIRED)
            ->addOption('batch', 'l', InputOption::VALUE_REQUIRED, "Batch size (limit) for each transaction")
            ->setHelp('This command allows you to create a user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $scriptName = ucfirst($input->getArgument('script'));

        $scriptClass = "Migration\\Scripts\\{$scriptName}MigrationScript";
        if(class_exists($scriptClass)) {
            $script = new $scriptClass($this->config);
            $options = $input->getOptions();
            $totalRows = 0;

            while($data = $script->input($options)) {
                $preparedData = $script->prepare($data, $options);
                $insertedRows = $script->output($preparedData, $options);

                $this->say("Inserted so far: {$insertedRows} rows.", OutputInterface::VERBOSITY_VERBOSE);
                $totalRows += $insertedRows;
            }

            $this->say("Total {$totalRows} inserted.");

        } else {
            throw new \Exception('Migration script not found: '. $scriptClass);
        }
    }


}