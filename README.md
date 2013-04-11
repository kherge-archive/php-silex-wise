Wise Service Provider
=====================

[![Build Status]](http://travis-ci.org/herrera-io/php-silex-wise)

What is it?
-----------

This [Silex] service provider adds [Wise] as a service to the application.

How do I get started?
---------------------

> This setup guide is the quick and easy route.

Install the service provider as a [Composer] dependency:

```json
{
    "require": {
        "herrera-io/silex-wise": "~1.0"
    }
}
```

And register the service provider:

```php
$app->register(
    new Herrera\Wise\WiseServiceProvider(),
    array(
        'wise.path' => '/path/to/config/dir',     // or an array of paths
        'wise.cache_dir' => '/path/to/cache/dir', // optional
        'wise.options' => array(
            'parameters' => array(...)            // global parameters
        )
    )
);

$config = $wise->load('myConfig.yml');
```

What else can it do?
--------------------

The service provider registers the following services:

- `$app['wise']` - An instance of `Herrera\Wise\Wise`.
- `$app['wise.collector']` - An instance of `Herrera\Wise\Resource\ResourceCollector`.
- `$app['wise.loader']` - An instance of `Symfony\Component\Config\Loader\DelegatingLoader`.
- `$app['wise.loader_resolver']` - An instance of `Symfony\Component\Config\Loader\LoaderResolver`.
- `$app['wise.loaders']` - An array of loaders to use with `$app['loader_resolver']`. By default the following are returned:
    - `Herrera\Wise\Loader\IniFileLoader`
    - `Herrera\Wise\Loader\JsonFileLoader` if `Herrera\Json\Json` is available.
    - `Herrera\Wise\Loader\PhpFileLoader`
    - `Herrera\Wise\Loader\XmlFileLoader` if `DOMDocument` is available.
    - `Herrera\Wise\Loader\YamlFileLoader` if `Symfony\Component\Yaml\Yaml` is available.
- `$app['wise.locator']` - An instance of `Symfony\Component\Config\FileLocator`.
- `$app['wise.processor']` - An instance of `Herrera\Wise\Processor\DelegatingProcessor`.
- `$app['wise.processor_resolver']` - An instance of `Herrera\Wise\Processor\ProcessorResolver`.
- `$app['wise.processors']` - An array of processors to use with `$app['processor_resolver']`.

and the following parameters:

- `$app['wise.cache_dir']` - The cache directory path.
- `$app['wise.options']` - The optional Wise settings.
- `$app['wise.path']` - The configuration directory path.

All of the above can be customized to better suit your needs.

[Build Status]: https://secure.travis-ci.org/herrera-io/php-silex-wise.png?branch=master
[Composer]: http://getcomposer.org/
[Silex]: http://silex.sensiolabs.org/
[Wise]: https://github.com/herrera-io/php-wise