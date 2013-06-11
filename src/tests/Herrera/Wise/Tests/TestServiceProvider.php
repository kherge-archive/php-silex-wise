<?php

namespace Herrera\Wise\Tests;

use Silex\Application;
use Silex\ServiceProviderInterface;

class TestServiceProvider implements ServiceProviderInterface
{
    private static $counter = 0;

    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['test' . self::$counter] = $this;

        self::$counter++;
    }
}
