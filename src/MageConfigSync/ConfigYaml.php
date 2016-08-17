<?php

namespace MageConfigSync;

use MageConfigSync\Api\ConfigurationAdapterInterface;
use MageConfigSync\Util\ArrayUtil;
use Symfony\Component\Yaml\Dumper;

class ConfigYaml
{
    public function __construct(array $data, $environment = false)
    {
        $this->_environment = $environment;
        $this->_raw_data    = $data;
    }

    /**
     * @param ConfigurationAdapterInterface $config_adapter
     * @param bool $environment
     * @return ConfigYaml
     */
    public static function build(ConfigurationAdapterInterface $config_adapter, $environment = false)
    {
        $data_structure = array();

        foreach ($config_adapter->getAllValues() as $row) {

            $scope = $row['scope'];
            $scope_id = $row['scope_id'];
            $path  = $row['path'];
            $value = $row['value'];

            $scope_key = self::buildScopeKey($scope, $scope_id);

            if (!isset($data_structure[$scope_key])) {
                $data_structure[$scope_key] = array();
            }

            $data_structure[$scope_key][$path] = $value;
        }

        return new ConfigYaml($data_structure, $environment);
    }

    /**
     * @param ConfigYaml $a
     * @param ConfigYaml $b
     * @return array
     */
    public static function compare(ConfigYaml $a, ConfigYaml $b)
    {
        $a_data = $a->getData();
        $b_data = $b->getData();

        return ArrayUtil::diffAssocRecursive($a_data, $b_data);
    }

    public function getData()
    {
        if ($this->_environment) {
            return $this->_raw_data[$this->_environment];
        } else {
            return $this->_raw_data;
        }
    }

    /**
     * @param bool $forced_environment
     * @return array
     */
    public function toArray($forced_environment = false)
    {
        if ($forced_environment) {
            $environment = $forced_environment;
        } else if ($this->_environment) {
            $environment = $this->_environment;
        } else {
            $environment = false;
        }

        if ($environment) {
            return array(
                $environment => $this->getData()
            );
        } else {
            return $this->getData();
        }
    }

    /**
     * @param bool $forced_environment
     * @return string
     */
    public function toYaml($forced_environment = false)
    {
        $dumper = new Dumper();
        return $dumper->dump($this->toArray($forced_environment), 3);
    }

    /**
     * @param $scope
     * @param $scope_id
     * @return string
     */
    public static function buildScopeKey($scope, $scope_id)
    {
        if ($scope == 'default') {
            return $scope;
        } else {
            return sprintf("%s-%s", $scope, $scope_id);
        }
    }

    /**
     * @param $scope_key
     * @return array
     */
    public static function extractFromScopeKey($scope_key)
    {
        $scope_key_parts = explode("-", $scope_key);
        $scope_key_parts_count = count($scope_key_parts);

        if ($scope_key_parts_count == 1) {
            $scope = $scope_key_parts[0];
            $scope_id = 0;
        } else {
            $scope = join("-", array_slice($scope_key_parts, 0, $scope_key_parts_count - 1));
            $scope_id = $scope_key_parts[$scope_key_parts_count-1];
        }

        return array(
            'scope'    => $scope,
            'scope_id' => $scope_id
        );
    }
}
