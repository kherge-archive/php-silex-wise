<?php

namespace Herrera\Wise;

use Herrera\Wise\WiseAwareInterface;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * Registers Wise as a service.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class WiseServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app['wise'] = $app->share(
            function () use ($app) {
                $wise = new Wise($app['debug']);
                $app['__wise'] = $wise;

                $wise->setLoader($app['wise.loader']);
                $wise->setCollector($app['wise.collector']);
                $wise->setGlobalParameters($app['wise.options']['parameters']);
                $wise->setProcessor($app['wise.processor']);

                if (isset($app['wise.cache_dir'])) {
                    $wise->setCacheDir($app['wise.cache_dir']);
                }

                unset($app['__wise']);

                return $wise;
            }
        );

        $app['wise.collector'] = $app->share(
            function () use ($app) {
                return new Resource\ResourceCollector();
            }
        );

        $app['wise.loader'] = $app->share(
            function () use ($app) {
                return new DelegatingLoader($app['wise.loader_resolver']);
            }
        );

        $app['wise.loader_resolver'] = $app->share(
            function () use ($app) {
                return new LoaderResolver($app['wise.loaders']);
            }
        );

        $app['wise.loaders'] = $app->share(
            function () use ($app) {
                $loaders = array(
                    new Loader\IniFileLoader($app['wise.locator']),
                    new Loader\PhpFileLoader($app['wise.locator']),
                );

                if (class_exists('Herrera\\Json\\Json')) {
                    $loaders[] = new Loader\JsonFileLoader(
                        $app['wise.locator']
                    );
                }

                if (class_exists('DOMDocument')) {
                    $loaders[] = new Loader\XmlFileLoader($app['wise.locator']);
                }

                if (class_exists('Symfony\\Component\\Yaml\\Yaml')) {
                    $loaders[] = new Loader\YamlFileLoader(
                        $app['wise.locator']
                    );
                }

                foreach ($loaders as $loader) {
                    if ($loader instanceof WiseAwareInterface) {
                        $loader->setWise($app['__wise']);
                    }
                }

                return $loaders;
            }
        );

        $app['wise.locator'] = $app->share(
            function () use ($app) {
                return new FileLocator($app['wise.path']);
            }
        );

        $app['wise.options'] = array(
            'parameters' => array()
        );

        $app['wise.processor'] = $app->share(
            function () use ($app) {
                return new Processor\DelegatingProcessor(
                    $app['wise.processor_resolver']
                );
            }
        );

        $app['wise.processor_resolver'] = $app->share(
            function () use ($app) {
                return new Processor\ProcessorResolver($app['wise.processors']);
            }
        );

        $app['wise.processors'] = array();
    }
}
