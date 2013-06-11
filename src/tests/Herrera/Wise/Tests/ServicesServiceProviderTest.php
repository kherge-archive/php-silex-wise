<?php

namespace Herrera\Wise\Tests;

use Herrera\PHPUnit\TestCase;
use Herrera\Wise\ServicesServiceProvider;
use Herrera\Wise\WiseServiceProvider;
use Silex\Application;

class ServicesServiceProviderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    private $cwd;
    private $dir;

    /**
     * @var ServicesServiceProvider
     */
    private $provider;

    public function testRegister()
    {
        file_put_contents(
            'config_dev.json',
            json_encode(
                array(
                    'parameters' => array(
                        'my_parameter' => 123
                    ),
                    'test_0' => array(
                        'class' => 'Herrera\\Wise\\Tests\\TestServiceProvider',
                        'parameters' => array(
                            'test_parameter' => 456
                        )
                    ),
                    'test_1' => array(
                        'class' => 'Herrera\\Wise\\Tests\\TestServiceProvider',
                    ),
                )
            )
        );

        $this->provider->register($this->app);

        $this->assertSame(123, $this->app['my_parameter']);
        $this->assertSame(456, $this->app['test_parameter']);
        $this->assertInstanceOf(
            'Herrera\\Wise\\Tests\\TestServiceProvider',
            $this->app['test0']
        );
        $this->assertInstanceOf(
            'Herrera\\Wise\\Tests\\TestServiceProvider',
            $this->app['test1']
        );
    }

    public function testRegisterNoClass()
    {
        file_put_contents(
            'config_dev.json',
            json_encode(array('test' => array()))
        );

        $this->setExpectedException(
            'InvalidArgumentException',
            'The "test" service did not specify "class".'
        );

        $this->provider->register($this->app);
    }

    protected function setUp()
    {
        $this->app = new Application();
        $this->cwd = getcwd();
        $this->dir = $this->createDir();
        $this->provider = new ServicesServiceProvider();

        chdir($this->dir);

        $this->app['debug'] = true;

        $this->app->register(
            new WiseServiceProvider(),
            array(
                'wise.path' => $this->dir
            )
        );
    }
}
