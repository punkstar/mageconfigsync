<?php

namespace MageConfigSync\Tests\Integration;

use MageConfigSync\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Parser;

class DiffIntegrationTest extends \PHPUnit_Framework_TestCase {

    /** @var CommandTester */
    protected $_commandTester;
    protected $_commandName = 'diff';

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
    public function testNodiffNoenv()
    {
        $this->_commandTester->execute(array(
            'command'          => $this->_commandName,
            '--magento-root'   => $this->_magentoRoot,
            'config-yaml-file' => __DIR__ . "/data/base_noenv.yaml"
        ));

        $this->assertEmpty($this->_commandTester->getDisplay());
    }

    /**
     * @test
     * @group integration
     */
    public function testNodiffNoenvIntegerComparison()
    {
        $this->_commandTester->execute(array(
            'command'          => $this->_commandName,
            '--magento-root'   => $this->_magentoRoot,
            'config-yaml-file' => __DIR__ . "/data/base_noenv_integers.yaml"
        ));

        $this->assertEmpty($this->_commandTester->getDisplay());
    }

    /**
     * @test
     * @group integration
     */
    public function testNodiffWithEnv()
    {
        $this->_commandTester->execute(array(
            'command'          => $this->_commandName,
            '--magento-root'   => $this->_magentoRoot,
            '--env'            => "prod",
            'config-yaml-file' => __DIR__ . "/data/base_prodenv.yaml"
        ));

        $this->assertEmpty($this->_commandTester->getDisplay());
    }

    /**
     * @test
     * @group integration
     */
    public function testDiffNoenv()
    {
        $exit_status = $this->_commandTester->execute(array(
            'command'          => $this->_commandName,
            '--magento-root'   => $this->_magentoRoot,
            'config-yaml-file' => __DIR__ . "/data/base_noenv_GBP.yaml"
        ));

        $this->assertNotEmpty($this->_commandTester->getDisplay());
        $this->assertCount(3, explode("\n", trim($this->_commandTester->getDisplay())));
        $this->assertEquals(3, $exit_status);
    }

    /**
     * @test
     * @group integration
     */
    public function testDiffWithEnv()
    {
        $exit_status = $this->_commandTester->execute(array(
            'command'          => $this->_commandName,
            '--magento-root'   => $this->_magentoRoot,
            '--env'            => "prod",
            'config-yaml-file' => __DIR__ . "/data/base_prodenv_GBP.yaml"
        ));

        $this->assertNotEmpty($this->_commandTester->getDisplay());
        $this->assertCount(3, explode("\n", trim($this->_commandTester->getDisplay())));
        $this->assertEquals(3, $exit_status);
    }
}
