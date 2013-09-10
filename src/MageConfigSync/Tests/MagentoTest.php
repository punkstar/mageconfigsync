<?php

namespace MageConfigSync\Tests;

use MageConfigSync\Magento;

class MagentoTest extends \PHPUnit_Framework_TestCase
{
    public function testFindMagentoRootDirLowerProvided()
    {
        $dir_prefix = __DIR__ . "/MagentoTest";

        $this->assertFileExists("$dir_prefix/a/b/app/Mage.php");
        $this->assertFileExists("$dir_prefix/a/b/c");

        $magento = new Magento("$dir_prefix/a/b/c");
        $magento_root_dir = $magento->findMagentoRootDir("$dir_prefix/a/b/c");

        $this->assertEquals("$dir_prefix/a/b", $magento_root_dir);
    }

    /**
     * @test
     */
    public function testFindMagentoRootDirExactProvided()
    {
        $dir_prefix = __DIR__ . "/MagentoTest";

        $this->assertFileExists("$dir_prefix/a/b/app/Mage.php");

        $magento = new Magento("$dir_prefix/a/b");
        $magento_root_dir = $magento->findMagentoRootDir("$dir_prefix/a/b");

        $this->assertEquals("$dir_prefix/a/b", $magento_root_dir);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function testFindMagentoRootDirNotFound()
    {
        $dir_prefix = __DIR__ . "/MagentoTest";

        $this->assertFileExists("$dir_prefix/a/b/app/Mage.php");

        $magento = new Magento("$dir_prefix/a/b");

        $this->assertFileNotExists("$dir_prefix/a/app/Mage.php");
        $magento->findMagentoRootDir("$dir_prefix/a");
    }
}
