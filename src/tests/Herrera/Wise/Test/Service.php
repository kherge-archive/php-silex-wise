<?php

namespace Herrera\Wise\Test;

use Silex\Application as Silex;
use Silex\ServiceProviderInterface;

class Service implements ServiceProviderInterface
{
    public function boot(Silex $app)
    {
    }

    public function register(Silex $app)
    {
        if (!isset($app['test.service'])) {
            $app['test.service'] = 0;
        }

        $app['test.service'] = $app['test.service'] + 1;
    }
}
