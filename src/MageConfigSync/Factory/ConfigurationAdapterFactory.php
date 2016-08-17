<?php

namespace MageConfigSync\Factory;

use MageConfigSync\Api\ConfigurationAdapterInterface;
use MageConfigSync\Magento;
use MageConfigSync\Magento2;
use MageConfigSync\Magento2\ConfigurationAdapter as Magento2Configuration;
use MageConfigSync\Magento\ConfigurationAdapter as MagentoConfiguration;
use Symfony\Component\Console\Input\InputInterface;

class ConfigurationAdapterFactory
{

    /**
     * @param InputInterface $input
     *
     * @return ConfigurationAdapterInterface
     */
    static public function create(InputInterface $input)
    {
        if ($input->getOption('magento2')) {
            return static::createMagento2Configuration($input->getOption('magento-root'));
        }

        return static::createMagentoConfiguration($input->getOption('magento-root'));
    }

    /**
     * @param $magento2_root
     *
     * @return Magento2Configuration
     */
    static public function createMagento2Configuration($magento2_root)
    {
        $magento2 = new Magento2($magento2_root);
        return new Magento2Configuration($magento2);
    }

    /**
     * @param $magento_root
     *
     * @return MagentoConfiguration
     */
    static public function createMagentoConfiguration($magento_root)
    {
        $magento = new Magento($magento_root);
        return new MagentoConfiguration($magento);
    }

}
