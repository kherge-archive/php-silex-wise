Usage
=====

Using Wise for Silex is pretty much as straightforward as any other Silex
service provider. You register the service provider with one or more service
parameters:

```php
$app = new Silex\Application();

$app->register(
    new Herrera\Wise\WiseServiceProvider(),
    array(
        'wise.path' => '/path/to/config'
    )
);
```

As demonstrated above, Wise will be made available as `$app['wise']`, and will
load all of your configuration files from `/path/to/config`. To use more than
one directory path, you may pass an array of directory paths instead of just
one.

Loaders
-------

By default, every supported file loader will be registered:

- INI
- PHP
- JSON (if the Herrera\Json library is installed)
- XML (if the DOMDocument class is available)
- YAML (if the Symfony\Component\Yaml library is installed)

You may specify your own list of loaders by replacing the `$app['wise.loaders']`
array value. This list of loaders will be automatically registered with Wise
once `$app['wise']` is requested.

Processors
----------

In addition to being able to specify your own loaders, you may also specify
your own list of configuration processors. To automatically register your
processors, simply replace the `$app['wise.processors']` array value with your
list of configuration processors.

Global Parameters
-----------------

To specify global parameters for your configuration files to use, you may set
the `$app['wise.options']['parameters']` value with an associative array. This
value may also be an object that implements the `ArrayAccess` interface. This
will allow you to use the `$app` object itself as a global parameter.

Caching
-------

To enable caching, you will need to set the `$app['wise.cache_dir']` value
with your cache directory path. If this value is not set, the configuration
files will not be cached once they have been loaded.
