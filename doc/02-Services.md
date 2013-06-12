Services
========

You may choose to register some, or all, of your services using a file. The
`WiseServiceProvider` class provides the method `registerServices()` to help
you with just that.

**config/services.yml**:

```yaml
monolog:
  class: Silex\Provider\MonologServiceProvider
  parameters:
    monolog.logfile: "%app_path%/logs/app.log"
    monolog.level: 100
    monolog.name: app

twig:
  class: Silex\Provider\TwigServiceProvider
  parameters:
    twig.path: "%app_path%/views"
```

> If you were following the directions shown in **Usage**, the `%app_path%`
> value would be the value of the `app_path` parameter in your `$app` instance.

```php
Herrera\Wise\WiseServiceProvider::registerServices($app);
```

This example will register the Monolog and Twig service providers with your
application.

Configuration
-------------

The example above can be a little misleading in regards to how you name your
configuration file. The name used by `registerServices()` actually depends
on a few factors.

- The value of `$app['wise.options']['config']['services']`.
  (default: `services`)
- The value of `$app['wise.options']['mode']`.
  (default: `prod` if debugging is disabled, `dev` if enabled)
- The value of `$app['wise.options']['type']`.
  (default: `json`)

The purpose of having the name broken out into parts like this is to give you
more control over how files are named, and under what conditions it is named.
By default, the services configuration file is called `services.yml` when debug
is disabled, and `services_dev.yml` when it is enabled.

If you want to use YAML for defining your services, you may simply change the
`$app['wise.options']['type']` parameter to `yml`. If you want to use your own
custom application mode, you can change `$app['wise.options']['mode']` to your
needs.
