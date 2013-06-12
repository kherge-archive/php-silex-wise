<?php

namespace Herrera\Wise\Test;

use Silex\Application;
use Silex\ServiceProviderInterface;

class Service implements ServiceProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        if (!isset($app['test.service'])) {
            $app['test.service'] = 0;
        }

        $app['test.service'] = $app['test.service'] + 1;
    }
}
