<?php

namespace Herrera\Wise;

use InvalidArgumentException;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Loads parameters and services from a configuration file.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ServicesServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    // @codeCoverageIgnoreStart
    public function boot(Application $app)
    {
    }
    // @codeCoverageIgnoreEnd

    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        $this->setDefaults($app);

        $config = $this->getConfig($app);

        foreach ($config['parameters'] as $parameter => $value) {
            $app[$parameter] = $value;
        }

        unset($config['parameters']);

        foreach ($config as $name => $service) {
            if (!isset($service['class'])) {
                throw new InvalidArgumentException(
                    sprintf(
                        'The "%s" service did not specify "class".',
                        $name
                    )
                );
            }

            if (!isset($service['parameters'])) {
                $service['parameters'] = array();
            }

            $app->register(
                new $service['class'],
                $service['parameters']
            );
        }
    }

    /**
     * Returns the parameters and services.
     *
     * @param Application $app The application.
     *
     * @return array The parameters and services.
     */
    private function getConfig(Application $app)
    {
        /** @var $wise \Herrera\Wise\Wise */
        $wise = $app['wise'];
        $config = $wise->load($this->getFileName($app));

        unset($config['imports']);

        if (!isset($config['parameters'])) {
            $config['parameters'] = array();
        }

        return $config;
    }

    /**
     * Returns the file name for the configuration settings.
     *
     * @param Application $app The application.
     *
     * @return string The file name.
     */
    private function getFileName(Application $app)
    {
        $file = $app['wise.services.name'];

        if ('prod' !== $app['wise.services.mode']) {
            $file .= '_' . $app['wise.services.mode'];
        }

        $file .= '.' . $app['wise.services.type'];

        return $file;
    }

    /**
     * Sets the default configuration settings.
     *
     * @param Application $app The application.
     */
    private function setDefaults(Application $app)
    {
        $app['wise.services.defaults'] = array(
            'mode' => $app['debug'] ? 'dev' : 'prod',
            'name' => 'config',
            'type' => 'json',
        );

        foreach ($app['wise.services.defaults'] as $key => $value) {
            if (!isset($app["wise.services.$key"])) {
                $app["wise.services.$key"] = $value;
            }
        }
    }
}
