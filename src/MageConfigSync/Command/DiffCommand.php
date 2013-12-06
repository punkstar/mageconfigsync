<?php

namespace MageConfigSync\Command;

use MageConfigSync\ConfigYaml;
use MageConfigSync\Magento;
use MageConfigSync\Magento\ConfigurationAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class DiffCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('diff')
            ->setDescription('Compare the current Magento configuration with a file.')
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
                'Environment in the YAML to compare the database to.  If one is not provided, no environment will be used.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $magento = new Magento($input->getOption('magento-root'));
        $config_adapter = new ConfigurationAdapter($magento);

        $yaml = new Parser();

        if ($input->getArgument('config-yaml-file')) {
            $config_yaml_file = $input->getArgument('config-yaml-file');

            if (!file_exists($config_yaml_file)) {
                throw new \Exception("File ($config_yaml_file) does not exist");
            }

            if (!is_readable($config_yaml_file)) {
                throw new \Exception("File ($config_yaml_file) is not readable");
            }

            $config_db_yaml = ConfigYaml::build($config_adapter);

            $config_file_contents = $yaml->parse(file_get_contents($config_yaml_file));
            $config_file_yaml = new ConfigYaml($config_file_contents, $input->getOption('env'));

            $diff = ConfigYaml::compare($config_file_yaml, $config_db_yaml);

            if (count($diff) > 0) {
                $db_data = $config_db_yaml->getData();
                $file_data = $config_file_yaml->getData();

                $diff_count = 0;

                foreach ($diff as $scope => $scope_data) {
                    foreach ($scope_data as $key => $value) {
                        $diff_count++;
                        $diff_message = sprintf(
                            "%s/%s is different (File: '%s', DB: '%s')",
                            $scope,
                            $key,
                            $file_data[$scope][$key],
                            $db_data[$scope][$key]
                        );
                        $output->writeln($diff_message);
                    }
                }

                return $diff_count;
            } else {
                return 0;
            }
        }
    }
}
