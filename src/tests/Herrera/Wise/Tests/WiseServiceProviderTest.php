<?php

namespace Herrera\Wise\Tests;

use ArrayObject;
use Herrera\PHPUnit\TestCase;
use Herrera\Wise\Loader;
use Herrera\Wise\Test\Processor;
use Herrera\Wise\WiseServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        /** @var \Herrera\Wise\Processor\DelegatingProcessor $processor */
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
        $this->app['wise.processors'] = array(new Processor());

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

    public function testRegisterRoutes()
    {
        file_put_contents(
            'routes_test.json',
            json_encode(
                array(
                    'test' => array(
                        'pattern' => '/test/{id}',
                        'defaults' => array(
                            '_controller' => 'Herrera\\Wise\\Test\\Controller::action'
                        )
                    )
                )
            )
        );

        $this->app['wise.options']['mode'] = 'test';

        WiseServiceProvider::registerRoutes($this->app);

        $this->app->boot();

        $request = Request::create('/test/123');
        /** @var Response $response */
        $response = $this->app->handle($request);

        $this->assertEquals('Action ran.', $response->getContent());
    }

    public function testRegisterRoutesPathAndPattern()
    {
        file_put_contents(
            'routes.json',
            json_encode(
                array(
                    'invalid_route' => array(
                        'path' => '/test',
                        'pattern' => '/test/{id}'
                    )
                )
            )
        );

        $this->setExpectedException(
            'Herrera\\Wise\\Exception\\InvalidArgumentException',
            'The "invalid_route" route must not specify both "path" and "pattern".'
        );

        WiseServiceProvider::registerRoutes($this->app);
    }

    public function testRegisterRoutesNull()
    {
        file_put_contents(
            'routes.json',
            json_encode(
                array(
                    'test' => array(
                        'pattern' => '/test/{id}',
                        'defaults' => array(
                            '_controller' => 'Herrera\\Wise\\Test\\Controller::action'
                        )
                    )
                )
            )
        );

        file_put_contents(
            'routes_test.json',
            json_encode(
                array(
                    'imports' => array(
                        array(
                            'resource' => 'routes.json'
                        )
                    ),
                    'test' => null
                )
            )
        );

        $this->app['wise.options']['mode'] = 'test';

        WiseServiceProvider::registerRoutes($this->app);

        $this->app->boot();

        $request = Request::create('/test/123');
        /** @var Response $response */
        $response = $this->app->handle($request);

        $this->assertRegExp(
            '/could not be found/',
            $response->getContent()
        );
    }

    public function testRegisterRoutesUnsupportedKeys()
    {
        file_put_contents(
            'routes.json',
            json_encode(
                array(
                    'unsupported_route' => array(
                        'alpha' => 123,
                        'beta' => 123
                    )
                )
            )
        );

        $this->setExpectedException(
            'Herrera\\Wise\\Exception\\InvalidArgumentException',
            'The "unsupported_route" route used unsupported keys (alpha, beta). Expected:'
        );

        WiseServiceProvider::registerRoutes($this->app);
    }

    public function testRegisterServices()
    {
        file_put_contents(
            'services_test.json',
            json_encode(
                array(
                    'parameters' => array(
                        'test_parameter' => 123,
                    ),
                    'test_1' => array(
                        'class' => 'Herrera\\Wise\\Test\\Service',
                        'parameters' => array(
                            'test.parameter' => '%test_parameter%'
                        ),
                    ),
                    'test_2' => array(
                        'class' => 'Herrera\\Wise\\Test\\Service'
                    )
                )
            )
        );

        /** @var $wise \Herrera\Wise\Wise */
        $wise = $this->app['wise'];
        $wise->setGlobalParameters($this->app);

        $this->app['wise.options']['mode'] = 'test';

        WiseServiceProvider::registerServices($this->app);

        $this->assertSame(2, $this->app['test.service']);
        $this->assertEquals(123, $this->app['test.parameter']);
    }

    public function testRegisterServicesNoClass()
    {
        file_put_contents(
            'services.json',
            json_encode(
                array(
                    'invalid_service' => array()
                )
            )
        );

        $this->setExpectedException(
            'Herrera\\Wise\\Exception\\InvalidArgumentException',
            'The service "invalid_service" did not specify its "class".'
        );

        WiseServiceProvider::registerServices($this->app);
    }

    public function testRegisterServicesNull()
    {
        file_put_contents(
            'services.json',
            json_encode(
                array(
                    'parameters' => array(
                        'test_parameter' => 123,
                    ),
                    'test_1' => array(
                        'class' => 'Herrera\\Wise\\Test\\Service',
                        'parameters' => array(
                            'test.parameter' => '%test_parameter%'
                        ),
                    ),
                    'test_2' => array(
                        'class' => 'Herrera\\Wise\\Test\\Service'
                    )
                )
            )
        );

        file_put_contents(
            'services_test.json',
            json_encode(
                array(
                    'imports' => array(
                        array(
                            'resource' => 'services.json'
                        )
                    ),
                    'test_1' => null
                )
            )
        );

        /** @var $wise \Herrera\Wise\Wise */
        $wise = $this->app['wise'];
        $wise->setGlobalParameters($this->app);

        $this->app['wise.options']['mode'] = 'test';

        WiseServiceProvider::registerServices($this->app);

        $this->assertSame(1, $this->app['test.service']);
        $this->assertFalse(isset($this->app['test.parameter']));
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
