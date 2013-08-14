<?php

namespace Herrera\Wise\Tests\Silex;

use Herrera\PHPUnit\TestCase;
use Herrera\Wise\Test\Application;
use Herrera\Wise\WiseServiceProvider;

class WiseTraitTest extends TestCase
{
    public function testLoad()
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            $this->markTestSkipped('PHP 5.4 or greater is required.');
        }

        $file = $this->createFile('test.json');
        $data = array(
            'rand' => rand()
        );

        file_put_contents($file, json_encode($data));

        $app = new Application();

        $app->register(
            new WiseServiceProvider(),
            array(
                'wise.path' => dirname($file)
            )
        );

        $this->assertEquals($data, $app->load('test.json'));
    }
}
