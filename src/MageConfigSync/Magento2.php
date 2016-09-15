<?php

namespace MageConfigSync;

use MageConfigSync\Api\InstallationDetectorInterface;
use MageConfigSync\Exception\InstallationNotFound;

class Magento2 implements InstallationDetectorInterface
{

    /**
     * @var string
     */
    protected $_magentoRootDir;

    /**
     * @var string
     */
    protected $_magentoBootstrapFile;

    /**
     * @var \Magento\Framework\App\Http|null
     */
    protected $_magentoEnvironment;

    /**
     * @var \Magento\Framework\App\Bootstrap|null
     */
    protected $_magentoBootstrap;

    /**
     * @var \Magento\Framework\ObjectManagerInterface|null
     */
    protected $_magentoObjectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|null
     */
    protected $_magentoScopeConfig;

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface|null
     */
    protected $_magentoConfig;

    /**
     * @var \Magento\Framework\App\ResourceConnection|null
     */
    protected $_magentoResourceConnection;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->_magentoRootDir = $this->findMagentoRootDir($directory);
        $this->_magentoBootstrapFile = $this->_magentoRootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'bootstrap.php';
    }

    /**
     * @return bool
     */
    public function isInstallationDetected()
    {
        return file_exists($this->_magentoBootstrapFile);
    }

    /**
     * @return \Magento\Framework\App\Http
     */
    public function getMagentoEnvironment()
    {
        if (!$this->_magentoEnvironment) {
            $bootstrap = $this->getBootstrap();
            /** @var \Magento\Framework\App\Http $app */
            $this->_magentoEnvironment = $bootstrap->createApplication('Magento\Framework\App\Http');
        }

        return $this->_magentoEnvironment;
    }

    /**
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getScopeConfig()
    {
        if (!$this->_magentoScopeConfig) {
            $this->_magentoScopeConfig = $this->getObjectManager()->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        }

        return $this->_magentoScopeConfig;
    }

    /**
     * @return \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    public function getConfig()
    {
        if (!$this->_magentoConfig) {
            $this->_magentoConfig = $this->getObjectManager()->create('\Magento\Framework\App\Config\ConfigResource\ConfigInterface');
        }

        return $this->_magentoConfig;
    }

    /**
     * @return \Magento\Framework\App\ResourceConnection
     */
    public function getResourceConnection()
    {
        if (!$this->_magentoResourceConnection) {
            $this->_magentoResourceConnection = $this->getObjectManager()->create('\Magento\Framework\App\ResourceConnection');
        }

        return $this->_magentoResourceConnection;
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
            $current_search_location = join($ds, array_merge($directory_tree, array('app', 'bootstrap.php')));

            if (file_exists($current_search_location)) {
                return $current_directory;
            }

            array_pop($directory_tree);
        }

        throw new InstallationNotFound("Unable to locate Magento 2 root");
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        if (!$this->_magentoObjectManager) {
            $this->_magentoObjectManager = $this->getBootstrap()->getObjectManager();
        }

        return $this->_magentoObjectManager;
    }

    /**
     * @return \Magento\Framework\App\Bootstrap
     */
    protected function getBootstrap()
    {
        if (!$this->_magentoBootstrap) {
            if (!class_exists('\Magento\Framework\App\Bootstrap')) {
                $mage_filename = $this->_magentoRootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'bootstrap.php';
                require_once $mage_filename;
            }

            $this->_magentoBootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
        }

        return $this->_magentoBootstrap;
    }
}
