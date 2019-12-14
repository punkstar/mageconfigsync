<?php
namespace MageConfigSync\Magento;

use MageConfigSync\Api\ConfigurationAdapterInterface;
use MageConfigSync\Magento;

class ConfigurationAdapter implements ConfigurationAdapterInterface
{
    /**
     * @var Magento
     */
    protected $_magento;

    /**
     * @var string
     */
    protected $_table_name;

    public function __construct(Magento $magento)
    {
        $this->_magento = $magento;
        $this->_table_name = $magento->getTableName('core/config_data');
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
        $write = $this->_magento->getDatabaseWriteConnection();
        return $write->insertOnDuplicate(
            $this->_table_name,
            array(
                'scope'    => $scope,
                'scope_id' => $scope_id,
                'path'     => $path,
                'value'    => $value
            ),
            array('value')
        );
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
        $write = $this->_magento->getDatabaseWriteConnection();
        return $write->delete(
            $this->_table_name,
            array(
                'scope = ?'    => $scope,
                'scope_id = ?' => $scope_id,
                'path = ?'     => $path
            )
        );
    }

    /**
     * @param $path
     * @return array
     */
    public function getValue($path)
    {
        $read = $this->_magento->getDatabaseReadAdapter();
        $query = $read->select()
            ->from($this->_table_name, array('scope', 'scope_id', 'path', 'value'))
            ->where('path = ?', $path);

        return $read->fetchAll($query);
    }

    /**
     * @return array
     */
    public function getAllValues()
    {
        $read = $this->_magento->getDatabaseReadAdapter();
        $query = $read->select()
            ->from($this->_table_name, array('scope', 'scope_id', 'path', 'value'));

        return $read->fetchAll($query);
    }
}
