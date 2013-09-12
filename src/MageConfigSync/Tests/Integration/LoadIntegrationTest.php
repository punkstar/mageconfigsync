<?php

namespace MageConfigSync\Tests\Integration;

use MageConfigSync\Application;
use MageConfigSync\Magento;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class LoadIntegrationTest extends \PHPUnit_Framework_TestCase {

    /** @var CommandTester */
    protected $_commandTester;
    protected $_commandName = 'load';

    /** @var Magento */
    protected $_magento;

    protected $_magentoRoot;

    public function setUp()
    {
        parent::setUp();

        $application = new Application();
        $command = $application->find($this->_commandName);

        $this->_commandTester = new CommandTester($command);

        $this->_magentoRoot = __DIR__ . "/../../../../magento/";
        $this->_magento = new Magento($this->_magentoRoot);
    }

    /**
     * @test
     * @group integration
     */
    public function testNoEnvNoUpdate()
    {
        $this->markTestSkipped("Need to implement disabling of Magento cache for tests");
    }

    /**
     * @test
     * @group integration
     */
    public function testNoEnvUpdate()
    {
        $this->markTestSkipped("Need to implement disabling of Magento cache for tests");
    }

    /**
     * @test
     * @group integration
     */
    public function testEnvNoUpdate()
    {
        $this->markTestSkipped("Need to implement disabling of Magento cache for tests");
    }

    /**
     * @test
     * @group integration
     */
    public function testEnvUpdate()
    {
        $this->markTestSkipped("Need to implement disabling of Magento cache for tests");
    }
}
