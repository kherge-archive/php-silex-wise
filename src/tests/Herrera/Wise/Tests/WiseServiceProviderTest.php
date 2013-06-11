<?php

namespace Herrera\Wise\Tests;

use ArrayObject;
use Herrera\PHPUnit\TestCase;
use Herrera\Wise\Loader;
use Herrera\Wise\Processor\AbstractProcessor;
use Herrera\Wise\WiseServiceProvider;
use Silex\Application;

class WiseServiceProviderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    private $cwd;
    private $dir;
    private $params;

    /**
     * @var WiseServiceProvider
     */
    private $provider;

    public function getLoaders()
    {
        $loaders = array(
            array(0, 'Herrera\\Wise\\Loader\\IniFileLoader'),
            array(1, 'Herrera\\Wise\\Loader\\PhpFileLoader'),
        );

        if (class_exists('Herrera\\Json\\Json')) {
            $loaders[] = array(2, 'Herrera\\Wise\\Loader\\JsonFileLoader');
        }

        if (class_exists('DOMDocument')) {
            $loaders[] = array(3, 'Herrera\\Wise\\Loader\\XmlFileLoader');
        }

        if (class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            $loaders[] = array(4, 'Herrera\\Wise\\Loader\\YamlFileLoader');
        }

        return $loaders;
    }

    public function testRegister()
    {
        /** @var $wise \Herrera\Wise\Wise */
        $wise = $this->app['wise'];

        $this->assertInstanceOf('Herrera\\Wise\\Wise', $wise);
        $this->assertSame($this->app['wise.loader'], $wise->getLoader());
        $this->assertSame($this->app['wise.collector'], $wise->getCollector());
        $this->assertSame(
            $this->app['wise.options']['parameters'],
            $wise->getGlobalParameters()
        );
        $this->assertSame($this->app['wise.processor'], $wise->getProcessor());
        $this->assertEquals($this->app['wise.cache_dir'], $wise->getCacheDir());

        /** @var $resolver \Herrera\Wise\Loader\LoaderResolver */
        $resolver = $wise->getLoader()->getResolver();

        /** @var $loader \Herrera\Wise\Loader\AbstractFileLoader */
        foreach ($resolver->getLoaders() as $loader) {
            $this->assertSame($wise, $loader->getWise());
            $this->assertSame(
                $wise->getCollector(),
                $loader->getResourceCollector()
            );
        }
    }

    public function testRegisterCollector()
    {
        $this->assertInstanceOf(
            'Herrera\\Wise\\Resource\\ResourceCollector',
            $this->app['wise.collector']
        );
    }

    public function testRegisterLoader()
    {
        /** @var $loader \Symfony\Component\Config\Loader\DelegatingLoader */
        $loader = $this->app['wise.loader'];

        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\Loader\\DelegatingLoader',
            $loader
        );
        $this->assertSame(
            $this->app['wise.loader_resolver'],
            $loader->getResolver()
        );
    }

    public function testRegisterLoaderResolver()
    {
        /** @var $resolver \Herrera\Wise\Loader\LoaderResolver */
        $resolver = $this->app['wise.loader_resolver'];

        $this->assertInstanceOf(
            'Herrera\\Wise\\Loader\\LoaderResolver',
            $resolver
        );
        $this->assertSame(
            $this->app['wise.loaders'],
            $resolver->getLoaders()
        );
    }

    /**
     * @dataProvider getLoaders
     */
    public function testRegisterLoaders($index, $class)
    {
        /** @var $loader \Herrera\Wise\Loader\AbstractFileLoader */
        $loader = $this->app['wise.loaders'][$index];

        $this->assertInstanceOf($class, $loader);
        $this->assertSame(
            $this->app['wise.locator'],
            $loader->getLocator()
        );
    }

    public function testRegisterLocator()
    {
        $this->assertInstanceOf(
            'Symfony\\Component\\Config\\FileLocator',
            $this->app['wise.locator']
        );
        $this->assertEquals(
            array($this->app['wise.path']),
            $this->getPropertyValue($this->app['wise.locator'], 'paths')
        );
    }

    public function testRegisterProcessor()
    {
        /** @var \Herrera\Wise\Processor\DelegatingProcessor */
        $processor = $this->app['wise.processor'];

        $this->assertInstanceOf(
            'Herrera\\Wise\\Processor\\DelegatingProcessor',
            $processor
        );
        $this->assertSame(
            $this->app['wise.processor_resolver'],
            $processor->getResolver()
        );
    }

    public function testRegisterProcessorResolver()
    {
        $this->app['wise.processors'] = array(new TestProcessor());

        /** @var $resolver \Herrera\Wise\Processor\ProcessorResolver */
        $resolver = $this->app['wise.processor_resolver'];

        $this->assertInstanceOf(
            'Herrera\\Wise\\Processor\\ProcessorResolver',
            $resolver
        );
        $this->assertSame(
            $this->app['wise.processors'],
            $resolver->getProcessors()
        );
    }

    public function testRegisterProcessors()
    {
        $this->assertSame(array(), $this->app['wise.processors']);
    }

    protected function setUp()
    {
        $this->app = new Application();
        $this->cwd = getcwd();
        $this->dir = $this->createDir();
        $this->params = new ArrayObject();
        $this->provider = new WiseServiceProvider();

        chdir($this->dir);

        $this->app['wise.cache_dir'] = $this->dir;
        $this->app['wise.path'] = $this->dir;

        $this->provider->register($this->app);
    }

    protected function tearDown()
    {
        chdir($this->cwd);

        parent::tearDown();
    }
}

class TestProcessor extends AbstractProcessor
{
    public function getConfigTreeBuilder()
    {
    }

    public function supports($resource, $type = null)
    {
    }
}
