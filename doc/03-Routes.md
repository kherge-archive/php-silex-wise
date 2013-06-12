Routes
======

You may choose to register some, or all, of your routes using a file. The
`WiseServiceProvider` class provides the method `registerRoutes()` to help
you with just that.

**config/routes.yml**:

```yaml
home:
  path: /
  defaults:
    _controller: My\Controller::action
```

> The configuration is nearly identical to that of Symfony's. However, there
> is no support for loading other route configuration files using `resource`,
> `type`, and `prefix`.

```php
Herrera\Wise\WiseServiceProvider::registerRoutes($app);
```

This example will register the home route with your application.

Configuration
-------------

The example above can be a little misleading in regards to how you name your
configuration file. The name used by `registerRoutes()` actually depends on a
few factors.

- The value of `$app['wise.options']['config']['routes']`.
  (default: `routes`)
- The value of `$app['wise.options']['mode']`.
  (default: `prod` if debugging is disabled, `dev` if enabled)
- The value of `$app['wise.options']['type']`.
  (default: `json`)

The purpose of having the name broken out into parts like this is to give you
more control over how files are named, and under what conditions it is named.
By default, the routes configuration file is called `config.yml` when debug
is disabled, and `config_dev.yml` when it is enabled.

If you want to use YAML for defining your routes, you may simply change the
`$app['wise.options']['type']` parameter to `yml`. If you want to use your own
custom application mode, you can change `$app['wise.options']['mode']` to your
needs.
