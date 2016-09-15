<?php

namespace MageConfigSync\Command;

use MageConfigSync\ConfigYaml;
use MageConfigSync\Factory\ConfigurationAdapterFactory;
use MageConfigSync\Magento;
use MageConfigSync\Magento\ConfigurationAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class LoadCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('load')
            ->setDescription('Import configuration from a file into Magento.')
            ->addArgument(
                'config-yaml-file',
                InputArgument::REQUIRED,
                'The YAML file containing the configuration settings.'
            )
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
                'Environment to import.  If one is not provided, no environment will be used.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Component\Console\Output\ConsoleOutput $output */

        $config_adapter = ConfigurationAdapterFactory::create($input->getOption('magento-root'));

        $yaml = new Parser();

        if ($input->getArgument('config-yaml-file')) {
            $config_yaml_file = $input->getArgument('config-yaml-file');

            if (!file_exists($config_yaml_file)) {
                throw new \Exception("File ($config_yaml_file) does not exist");
            }

            if (!is_readable($config_yaml_file)) {
                throw new \Exception("File ($config_yaml_file) is not readable");
            }

            $config_file_contents = $yaml->parse(file_get_contents($config_yaml_file));
            $config_file_yaml = new ConfigYaml($config_file_contents, $input->getOption('env'));

            foreach ($config_file_yaml->getData() as $scope_key => $scope_data) {
                foreach ($scope_data as $path => $value) {
                    $scope_data = ConfigYaml::extractFromScopeKey($scope_key);

                    if ($value !== null) {
                        $affected_rows = $config_adapter->setValue($path, $value, $scope_data['scope'], $scope_data['scope_id']);
                    } else {
                        $affected_rows = $config_adapter->deleteValue($path, $scope_data['scope'], $scope_data['scope_id']);
                    }

                    if ($affected_rows > 0) {
                        $line = sprintf(
                            "[%s] %s -> %s",
                            $scope_key,
                            $path,
                            $value ?: 'null'
                        );

                        if (method_exists($output, 'getErrorOutput')) {
                            $output->getErrorOutput()->writeln($line);
                        } else {
                            $output->writeln($line);
                        }
                    }
                }
            }
        }

        return 0;
    }
}
