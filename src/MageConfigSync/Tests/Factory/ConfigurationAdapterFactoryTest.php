<?php

namespace MageConfigSync\Tests\Factory;

use MageConfigSync\Factory\ConfigurationAdapterFactory;
use MageConfigSync\Magento2\ConfigurationAdapter as Magento2Configuration;
use MageConfigSync\Magento\ConfigurationAdapter as MagentoConfiguration;

class Magento2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testCreateMagento2Configuration()
    {
        $dir_prefix = __DIR__ . "/../MagentoTest";
        $this->assertFileExists("$dir_prefix/a/b/app/bootstrap.php");

        $this->assertInstanceOf(Magento2Configuration::class, ConfigurationAdapterFactory::createMagento2Configuration(null, "$dir_prefix/a/b/app/bootstrap.php"));
    }

    /**
     * @test
     */
    public function testCreateMagentoConfiguration()
    {
        $this->markTestSkipped("Due to the current architecture of the `Magento` class we cannot test this easily.");
    }
}
