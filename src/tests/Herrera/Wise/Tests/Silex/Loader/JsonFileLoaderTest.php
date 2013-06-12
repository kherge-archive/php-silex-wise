<?php

namespace Herrera\Wise\Tests\Silex\Loader;

use Herrera\PHPUnit\TestCase;
use Herrera\Wise\Silex\Loader\JsonFileLoader;
use Silex\Application;
use Symfony\Component\Config\FileLocator;

class JsonFileLoaderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var JsonFileLoader
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

        $data = $this->loader->load('test.json');

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

        $this->loader = new JsonFileLoader(
            new FileLocator($dir)
        );

        file_put_contents(
            "$dir/test.json",
            <<<CONFIG
{
    "parameters": {
        "test": 123
    }
}
CONFIG
        );
    }
}
