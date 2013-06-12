<?php

namespace Herrera\Wise\Tests\Silex\Loader;

use Herrera\PHPUnit\TestCase;
use Herrera\Wise\Silex\Loader\IniFileLoader;
use Silex\Application;
use Symfony\Component\Config\FileLocator;

class IniFileLoaderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var IniFileLoader
     */
    private $loader;

    public function testGetSilex()
    {
        $this->setPropertyValue($this->loader, 'app', $this->app);

        $this->assertSame($this->app, $this->loader->getSilex());
    }

    public function testLoad()
    {
        $this->loader->setSilex($this->app);

        $data = $this->loader->load('test.ini');

        $this->assertEquals(
            array(
                'parameters' => array(
                    'test' => 123
                )
            ),
            $data
        );

        $this->assertEquals(123, $this->app['test']);
    }

    public function testSetSilex()
    {
        $this->loader->setSilex($this->app);

        $this->assertSame(
            $this->app,
            $this->getPropertyValue($this->loader, 'app')
        );
    }

    protected function setUp()
    {
        $this->app = new Application();

        $dir = $this->createDir();

        $this->loader = new IniFileLoader(
            new FileLocator($dir)
        );

        file_put_contents(
            "$dir/test.ini",
            <<<CONFIG
[parameters]
test = 123
CONFIG
        );
    }
}
