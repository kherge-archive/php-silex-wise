Usage
=====

First, you will need to register the service provider.

Registering
-----------

```php
$app = new Silex\Application();

$app->register(
    new Herrera\Wise\WiseServiceProvider(),
    array(
        'wise.path' => '/path/to/config'
    )
);
```

> The only configuration parameter required is `wise.path`.

In addition to specify the configuration directory path, you may also specify
the cache directory path (`wise.cache_dir`), and the list of global parameters
(`$app['wise.options']['parameters']`). The following is an example of both in
use:

```php
$app->register(
    new Herrera\Wise\WiseServiceProvider(),
    array(
        'wise.cache_dir' => '/path/to/config/cache',
        'wise.path' => '/path/to/config',
        'wise.options' => array(
            'parameters' => $app
        )
    )
);
```

Notice how `$app` was used as the array of global parameters. This will give
your configuration files access to the parameters set for your application.
You may opt to instead use a regular array, or a different object that uses
the `ArrayAccess` interface.

Loading
-------

Now that Wise is ready, you can load your configuration files:

```php
$config = $app['wise']->load('my_config.yml');
```
