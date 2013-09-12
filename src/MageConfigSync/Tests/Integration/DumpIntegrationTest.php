<?php

namespace MageConfigSync\Tests\Integration;

use MageConfigSync\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class DumpIntegrationTest extends \PHPUnit_Framework_TestCase {

    /** @var CommandTester */
    protected $_commandTester;
    protected $_commandName = 'dump';

    protected $_magentoRoot;

    public function setUp()
    {
        parent::setUp();

        $application = new Application();
        $command = $application->find($this->_commandName);

        $this->_commandTester = new CommandTester($command);

        $this->_magentoRoot = __DIR__ . "/../../../../magento/";
    }

    /**
     * @test
     * @group integration
     */
    public function testPlainContainsKnownConfig()
    {
        $this->_commandTester->execute(array(
            'command'        => $this->_commandName,
            '--magento-root' => $this->_magentoRoot
        ));

        $this->assertNotEmpty($this->_commandTester->getDisplay());
        $this->assertTrue(
            (strpos($this->_commandTester->getDisplay(), "web/unsecure/base_url: 'http://dev/null/'") !== false),
            "Command should contain unsecure base url"
        );
    }

    /**
     * @test
     * @group integration
     */
    public function testPlainValidYaml()
    {
        $this->_commandTester->execute(array(
            'command'        => $this->_commandName,
            '--magento-root' => $this->_magentoRoot
        ));

        $yaml = new Parser();
        $yaml->parse($this->_commandTester->getDisplay());
    }

    /**
     * @test
     * @group integration
     */
    public function testNoEnv()
    {
        $this->_commandTester->execute(array(
            'command'        => $this->_commandName,
            '--magento-root' => $this->_magentoRoot
        ));

        $this->assertStringStartsWith("default:", $this->_commandTester->getDisplay());
    }

    /**
     * @test
     * @group integration
     */
    public function testEnv()
    {
        $this->_commandTester->execute(array(
            'command'        => $this->_commandName,
            '--magento-root' => $this->_magentoRoot,
            '--env'          => 'prod'
        ));

        $this->assertStringStartsWith("prod:", $this->_commandTester->getDisplay());
    }
}
