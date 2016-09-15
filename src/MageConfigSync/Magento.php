<?php

namespace MageConfigSync;

use MageConfigSync\Exception\InstallationNotFound;
use MageConfigSync\Api\InstallationDetectorInterface;

class Magento implements InstallationDetectorInterface
{
    protected $_magentoRootDir;

    /**
     * @var string
     */
    protected $_magentoBoostrapFile;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->_magentoRootDir = $this->findMagentoRootDir($directory);
        $this->_magentoBoostrapFile = $this->_magentoRootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
    }

    /**
     * @return bool
     */
    public function isInstallationDetected()
    {
        return file_exists($this->_magentoBoostrapFile);
    }

    /**
     * @return \Mage_Core_Model_App
     */
    public function getMagentoEnvironment()
    {
        if (!class_exists('Mage')) {
            require_once $this->_magentoBoostrapFile;
        }

        return \Mage::app();
    }

    /**
     * @return \Varien_Db_Adapter_Interface
     */
    public function getDatabaseReadAdapter()
    {
        $this->getMagentoEnvironment();
        return \Mage::getModel('core/resource')->getConnection('core_read');
    }

    /**
     * @return \Varien_Db_Adapter_Interface
     */
    public function getDatabaseWriteConnection()
    {
        $this->getMagentoEnvironment();
        return \Mage::getModel('core/resource')->getConnection('core_write');
    }

    /**
     * @param string $name
     * @return string
     */
    public function getTableName($name)
    {
        $this->getMagentoEnvironment();
        return \Mage::getModel('core/resource')->getTableName($name);
    }

    /**
     * Given a start directory, work upwards and attempt to identify the Magento root directory.  Throws an
     * exception if it can't be found.
     *
     * @param string $start_directory
     * @return string
     * @throws \Exception
     */
    public function findMagentoRootDir($start_directory)
    {
        $ds = DIRECTORY_SEPARATOR;
        $directory_tree = explode($ds, $start_directory);

        while (count($directory_tree) > 0) {
            $current_directory = join($ds, $directory_tree);
            $current_search_location = join($ds, array_merge($directory_tree, array('app', 'Mage.php')));

            if (file_exists($current_search_location)) {
                return $current_directory;
            }

            array_pop($directory_tree);
        }

        throw new InstallationNotFound("Unable to locate Magento root");
    }
}
