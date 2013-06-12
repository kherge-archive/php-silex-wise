<?php

namespace Herrera\Wise\Tests\Silex\Loader;

use Herrera\PHPUnit\TestCase;
use Herrera\Wise\Silex\Loader\PhpFileLoader;
use Silex\Application;
use Symfony\Component\Config\FileLocator;

class PhpFileLoaderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var PhpFileLoader
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

        $data = $this->loader->load('test.php');

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

        $this->loader = new PhpFileLoader(
            new FileLocator($dir)
        );

        file_put_contents(
            "$dir/test.php",
            <<<CONFIG
<?php return array(
    'parameters' => array(
        'test' => 123
    )
);
CONFIG
        );
    }
}
