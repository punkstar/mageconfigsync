<?php

namespace MageConfigSync\Command;

use MageConfigSync\Magento;
use MageConfigSync\Magento\Configuration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;

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

        $data_structure = array();

        foreach ($config->getAllValues() as $row) {

            $scope = $row['scope'];
            $path  = $row['path'];
            $value = $row['value'];

            if (!isset($data_structure[$scope])) {
                $data_structure[$scope] = array();
            }

            $data_structure[$scope][$path] = $value;
        }

        $dumper = new Dumper();
        echo $dumper->dump($data_structure, 2);
    }
}
