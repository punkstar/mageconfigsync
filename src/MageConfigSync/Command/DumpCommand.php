<?php

namespace MageConfigSync\Command;

use MageConfigSync\Magento;
use MageConfigSync\Magento\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    public function __construct()
    {
        parent::__construct("mage_config_sync");
    }

    protected function configure()
    {
        $this
            ->setName('dump')
            ->setDescription('Output the current configuration')
            ->addOption(
                'magento-root',
                null,
                InputArgument::OPTIONAL,
                'The Magento root directory, defaults to current working directory',
                getcwd()
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $magento = new Magento($input->getOption('magento-root'));
        $config = new Configuration($magento);

        foreach ($config->getAllValues() as $row) {
            foreach (array_keys($row) as $idx) {
                $row[$idx] = str_replace("\n", '\n', $row[$idx]);
            }

            echo join(",", $row) . "\n";
        }
    }
}
