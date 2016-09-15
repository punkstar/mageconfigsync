<?php
namespace MageConfigSync\Magento2;

use MageConfigSync\Api\ConfigurationAdapterInterface;
use MageConfigSync\Magento2;

class ConfigurationAdapter implements ConfigurationAdapterInterface
{
    /**
     * @var Magento2
     */
    protected $_magento2;

    /**
     * @var string
     */
    protected $_table_name;

    /**
     * ConfigurationAdapter constructor.
     *
     * @param Magento2 $magento2
     */
    public function __construct(Magento2 $magento2)
    {
        $this->_magento2 = $magento2;
    }

    /**
     * @param $path
     * @param $value
     * @param $scope
     * @param $scope_id
     * @return int Number of affected rows
     */
    public function setValue($path, $value, $scope, $scope_id)
    {
        $this->_magento2->getConfig()->saveConfig($path, $value, $scope, $scope_id);

        return 1;
    }

    /**
     * @param $path
     * @param $scope
     * @param $scope_id
     *
     * @return int Number of affected rows
     */
    public function deleteValue($path, $scope, $scope_id)
    {
        $this->_magento2->getConfig()->deleteConfig($path, $scope, $scope_id);

        return 1;
    }

    /**
     * @param $path
     * @return array
     */
    public function getValue($path)
    {
        return $this->_magento2->getScopeConfig()->getValue($path);
    }

    /**
     * @return array
     */
    public function getAllValues()
    {
        $tableName = $this->_magento2->getResourceConnection()->getTableName('core_config_data');
        $connection = $this->_magento2->getResourceConnection()->getConnection();

        $query = $connection->select()
            ->from($tableName, array('scope', 'scope_id', 'path', 'value'));

        return $connection->fetchAll($query);
    }
}
