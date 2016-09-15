<?php

namespace MageConfigSync\Command;

use MageConfigSync\ConfigYaml;
use MageConfigSync\Factory\ConfigurationAdapterFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('dump')
            ->setDescription('Output the current configuration.')
            ->addOption(
                'magento-root',
                null,
                InputArgument::OPTIONAL,
                'The Magento root directory, defaults to current working directory.',
                getcwd()
            )
            ->addOption(
                'env',
                null,
                InputArgument::OPTIONAL,
                'Environment to use in the outputted YAML.  If one is not provided, no environment will be used.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config_adapter = ConfigurationAdapterFactory::create($input->getOption('magento-root'));
        $config_yaml = ConfigYaml::build($config_adapter);

        $output->write($config_yaml->toYaml($input->getOption('env')));

        return 0;
    }
}
