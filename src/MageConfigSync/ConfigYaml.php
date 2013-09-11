<?php

namespace MageConfigSync;

use MageConfigSync\Magento\ConfigurationAdapter;
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
     * @param ConfigurationAdapter $config_adapter
     * @param bool $environment
     * @return ConfigYaml
     */
    public static function build(ConfigurationAdapter $config_adapter, $environment = false)
    {
        $data_structure = array();

        foreach ($config_adapter->getAllValues() as $row) {

            $scope = $row['scope'];
            $path  = $row['path'];
            $value = $row['value'];

            if (!isset($data_structure[$scope])) {
                $data_structure[$scope] = array();
            }

            $data_structure[$scope][$path] = $value;
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

        return ArrayUtil::diff_assoc_recursive($a_data, $b_data);
    }

    public function getData()
    {
        return $this->_raw_data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this->_environment) {
            return array(
                $this->_environment => $this->getData()
            );
        } else {
            return $this->getData();
        }
    }

    /**
     * @return string
     */
    public function toYaml()
    {
        $dumper = new Dumper();
        return $dumper->dump($this->toArray(), 3);
    }
}
