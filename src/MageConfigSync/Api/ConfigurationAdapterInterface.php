<?php

namespace MageConfigSync\Api;

interface ConfigurationAdapterInterface
{
    /**
     * @param $path
     * @param $value
     * @param $scope
     * @param $scope_id
     * @return int Number of affected rows
     */
    public function setValue($path, $value, $scope, $scope_id);

    /**
     * @param $path
     * @param $scope
     * @param $scope_id
     *
     * @return int Number of affected rows
     */
    public function deleteValue($path, $scope, $scope_id);

    /**
     * @param $path
     * @return array
     */
    public function getValue($path);

    /**
     * @return array
     */
    public function getAllValues();
}
