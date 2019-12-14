<?php

namespace MageConfigSync\Factory;

use MageConfigSync\Api\ConfigurationAdapterInterface;
use MageConfigSync\Exception\InstallationNotFound;
use MageConfigSync\Magento;
use MageConfigSync\Magento2;
use MageConfigSync\Magento2\ConfigurationAdapter as Magento2Configuration;
use MageConfigSync\Magento\ConfigurationAdapter as MagentoConfiguration;
use Symfony\Component\Console\Input\InputInterface;

class ConfigurationAdapterFactory
{

    /**
     * @param $rootDir
     * @return Magento2Configuration|MagentoConfiguration
     * @throws \Exception
     */
    static public function create($rootDir)
    {
        try {
            $magentoAdapter = self::createMagentoAdapter($rootDir);

            if ($magentoAdapter->isInstallationDetected()) {
                return self::createMagentoConfiguration($magentoAdapter);
            }
        } catch (InstallationNotFound $e) {}

        try {
            $magento2Adapter = self::createMagento2Adapter($rootDir);

            if ($magento2Adapter->isInstallationDetected()) {
                return static::createMagento2Configuration($magento2Adapter);
            }
        } catch (InstallationNotFound $e) {}

        throw new \Exception("Unable to detect Magento version from provided root directory ($rootDir)");
    }

    /**
     * @param Magento2|null $adapter
     * @param null $rootDir
     * @return Magento2Configuration
     */
    static public function createMagento2Configuration(Magento2 $adapter = null, $rootDir = null)
    {
        if ($adapter == null) {
            $adapter = self::createMagento2Adapter($rootDir);
        }

        return new Magento2Configuration($adapter);
    }

    /**
     * @param Magento|null $adapter
     * @param null $rootDir
     * @return MagentoConfiguration
     */
    static public function createMagentoConfiguration(Magento $adapter = null, $rootDir = null)
    {
        if ($adapter == null) {
            $adapter = self::createMagentoAdapter($rootDir);
        }

        return new MagentoConfiguration($adapter);
    }

    /**
     * @param $rootDir
     * @return Magento
     */
    static protected function createMagentoAdapter($rootDir)
    {
        return new Magento($rootDir);
    }

    /**
     * @param $rootDir
     * @return Magento2
     */
    static protected function createMagento2Adapter($rootDir)
    {
        return new Magento2($rootDir);
    }
}
