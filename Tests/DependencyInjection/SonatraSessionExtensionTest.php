<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\SessionBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sonatra\Bundle\SessionBundle\SonatraSessionBundle;
use Sonatra\Bundle\SessionBundle\DependencyInjection\SonatraSessionExtension;

/**
 * Tests case for Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SonatraSessionExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testExtensionExist()
    {
        $container = $this->createContainer();

        $this->assertTrue($container->hasExtension('sonatra_session'));
    }

    public function testExtensionLoader()
    {
        $container = $this->createContainer();

        $this->assertTrue($container->hasParameter('sonatra_session.pdo.dsn'));
        $this->assertTrue($container->hasParameter('sonatra_session.pdo.db_options'));
        $this->assertTrue($container->has('sonatra_session.handler.pdo'));
    }

    public function testExtensionLoaderWithoutPdo()
    {
        $container = $this->createContainer(array('pdo' => array('enabled' => false)));

        $this->assertFalse($container->hasParameter('sonatra_session.pdo.dsn'));
        $this->assertFalse($container->hasParameter('sonatra_session.pdo.db_options'));
        $this->assertFalse($container->has('sonatra_session.handler.pdo'));
    }

    public function testExtensionDsnMissing()
    {
        $this->setExpectedException('Sonatra\Bundle\SessionBundle\Exception\InvalidConfigurationException');
        $this->createContainer(array('pdo' => array('dsn' => null)));
    }

    protected function createContainer(array $config = array())
    {
        $configs = empty($config) ? array() : array($config);
        $container = new ContainerBuilder();
        $container->setParameter('database_driver', 'pdo_database_driver');
        $container->setParameter('database_host', 'database_host');
        $container->setParameter('database_name', 'database_name');
        $container->setParameter('database_user', 'database_user');
        $container->setParameter('database_password', 'database_password');

        $bundle = new SonatraSessionBundle();
        $bundle->build($container);

        $extension = new SonatraSessionExtension();
        $container->registerExtension($extension);
        $extension->load($configs, $container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
