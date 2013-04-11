<?php

namespace Herrera\Wise\Tests;

use Herrera\PHPUnit\TestCase;
use Herrera\Wise\WiseServiceProvider;
use Silex\Application;

class WiseServiceProviderTest extends TestCase
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var WiseServiceProvider
     */
    private $provider;

    public function testRegister()
    {
        $this->app->register(
            $this->provider,
            array(
                'wise.cache_dir' => $this->createDir(),
                'wise.options' => array(
                    'parameters' => array(
                        'global' => array(
                            'value' => 456
                        )
                    )
                ),
                'wise.path' => $this->createDir(),
            )
        );

        $this->app->boot();

        file_put_contents(
            $this->app['wise.path'] . '/test.ini',
            <<<CONTENTS
[imports]
0[resource] = "test.php"

[ini]
test = 123
global = "%global.value%"
CONTENTS
        );

        file_put_contents(
            $this->app['wise.path'] . '/test.php',
            <<<CONTENTS
<?php return array(
    'imports' => array(
        array('resource' => 'test.json')
    ),
    'php' => array(
        'test' => 456
    )
);
CONTENTS
        );

        file_put_contents(
            $this->app['wise.path'] . '/test.json',
            <<<CONTENTS
{
    "imports": [
        {
            "resource": "test.xml"
        }
    ],
    "json": {
        "test": 789
    }
}
CONTENTS
        );

        file_put_contents(
            $this->app['wise.path'] . '/test.xml',
            <<<CONTENTS
<array>
  <array key="imports">
    <array>
      <str key="resource">test.yml</str>
    </array>
  </array>
  <array key="xml">
    <int key="test">987</int>
  </array>
</array>
CONTENTS
        );

        file_put_contents(
            $this->app['wise.path'] . '/test.yml',
            <<<CONTENTS
yaml:
    test: 654
CONTENTS
        );

        $this->assertEquals(
            array(
                'imports' => array(
                    array('resource' => 'test.yml')
                ),
                'ini' => array('test' => '123', 'global' => '456'),
                'php' => array('test' => 456),
                'json' => array('test' => 789),
                'xml' => array('test' =>  987),
                'yaml' => array('test' => 654),
            ),
            $this->app['wise']->load('test.ini')
        );
    }

    protected function setUp()
    {
        $this->app = new Application(array('debug' => true));
        $this->provider = new WiseServiceProvider();
    }
}
