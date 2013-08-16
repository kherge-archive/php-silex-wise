<?php

namespace Herrera\Wise;

use ArrayObject;
use Herrera\Wise\Exception\InvalidArgumentException;
use Herrera\Wise\Loader\LoaderResolver;
use Herrera\Wise\Silex\Loader;
use Herrera\Wise\Silex\SilexAwareInterface;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Registers Wise as a service.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class WiseServiceProvider implements ServiceProviderInterface
{
    /**
     * The supported route keys and default values.
     *
     * @var array
     */
    private static $supportedKeys = array(
        'defaults' => array(),
        'host' => '',
        'methods' => array(),
        'options' => array(),
        'path' => null,
        'pattern' => null,
        'requirements' => array(),
        'schemes' => array(),
    );

    /**
     * {@inheritDoc}
     */
    // @codeCoverageIgnoreStart
    public function boot(Application $app)
    {
        // do nothing
    }
    // @codeCoverageIgnoreEnd

    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $app['wise'] = $app->share(
            function () use ($app) {
                $wise = new Wise($app['debug']);

                $wise->setLoader($app['wise.loader']);
                $wise->setCollector($app['wise.collector']);
                $wise->setGlobalParameters($app['wise.options']['parameters']);
                $wise->setProcessor($app['wise.processor']);

                if (isset($app['wise.cache_dir'])) {
                    $wise->setCacheDir($app['wise.cache_dir']);
                }

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
                    if ($loader instanceof SilexAwareInterface) {
                        $loader->setSilex($app);
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

        $app['wise.options'] = new \ArrayObject(
            array(
                'config' => array(
                    'routes' => 'routes',
                    'services' => 'services',
                ),
                'mode' => $app['debug'] ? 'dev' : 'prod',
                'type' => 'json',
                'parameters' => array()
            )
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

    /**
     * Registers the configured routes.
     *
     * @param Application $app The application.
     *
     * @throws InvalidArgumentException If a route definition is invalid.
     */
    public static function registerRoutes(Application $app)
    {
        $file = $app['wise.options']['config']['routes'];

        if ('prod' !== $app['wise.options']['mode']) {
            $file .= '_' . $app['wise.options']['mode'];
        }

        $file .= '.' . $app['wise.options']['type'];

        /** @var $wise Wise */
        $wise = $app['wise'];
        $routes = $wise->load($file);
        $collection = new RouteCollection();

        unset($routes['parameters']);
        unset($routes['imports']);

        foreach ($routes as $name => $route) {
            if (null === $route) {
                continue;
            }

            if (isset($route['pattern'])) {
                if (isset($route['path'])) {
                    throw InvalidArgumentException::format(
                        'The "%s" route must not specify both "path" and "pattern".',
                        $name
                    );
                }

                $route['path'] = $route['pattern'];
            }

            $unsupportedKeys = array_diff(
                array_keys($route),
                array_keys(self::$supportedKeys)
            );

            if (!empty($unsupportedKeys)) {
                throw InvalidArgumentException::format(
                    'The "%s" route used unsupported keys (%s). Expected: %s',
                    $name,
                    join(', ', $unsupportedKeys),
                    join(', ', array_keys(self::$supportedKeys))
                );
            }

            foreach (self::$supportedKeys as $key => $value) {
                if (!isset($route[$key])) {
                    $route[$key] = $value;
                }
            }

            $collection->add(
                $name,
                new Route(
                    $route['path'],
                    $route['defaults'],
                    $route['requirements'],
                    $route['options'],
                    $route['host'],
                    $route['schemes'],
                    $route['methods']
                )
            );
        }

        /** @var $routes \Symfony\Component\Routing\RouteCollection */
        $routes = $app['routes'];
        $routes->addCollection($collection);
    }

    /**
     * Registers the configured services.
     *
     * @param Application $app The application.
     *
     * @throws InvalidArgumentException If a service definition is invalid.
     */
    public static function registerServices(Application $app)
    {
        $file = $app['wise.options']['config']['services'];

        if ('prod' !== $app['wise.options']['mode']) {
            $file .= '_' . $app['wise.options']['mode'];
        }

        $file .= '.' . $app['wise.options']['type'];

        /** @var $wise Wise */
        $wise = $app['wise'];
        $services = $wise->load($file);

        unset($services['parameters']);
        unset($services['imports']);

        foreach ($services as $name => $service) {
            if (null === $service) {
                continue;
            }

            if (!isset($service['class'])) {
                throw InvalidArgumentException::format(
                    'The service "%s" did not specify its "class".',
                    $name
                );
            }

            if (!isset($service['parameters'])) {
                $service['parameters'] = array();
            }

            $app->register(new $service['class'](), $service['parameters']);
        }
    }
}
