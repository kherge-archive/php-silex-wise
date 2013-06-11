Loading Services
================

Once you have registered Wise for Silex, you may also load parameters and
register services using a single configuration file. This closesly follows
how Symfony's `config.yml` configuration file functions.

Like Wise for Silex, you simply register another service provider:

```php
$app->register(
    new Herrera\Wise\ServicesServiceProvider()
);
```

Configuration File _Name_
-------------------------

By default, `config.json` is loaded if `$app['debug']` is `false`. Otherwise,
`config_dev.json` will be used. There are multiple options available for you
to change:

- `$app['wise.services.name']` &mdash; The name of the configuration file.
  (default: `config`)
- `$app['wise.services.mode']` &mdash; The file mode suffix.
  (default: `_dev` if debug is enabled, `prod` if not).
- `$app['wise.services.type']` &mdash; The file type.
  (default: `json`)

Basically, the above settings form the following file name template:

```php
$file = $app['wise.services.name'];

if ('prod' !== $app['wise.services.mode']) {
    $file .= '_' . $app['wise.services.mode'];
}

$file .= $app['wise.services.type'];
```

Configuration File
------------------

The `$file` value is what will be used by the Services service provider for
loading your application parameters and service registration list. Speaking
of which, this is what that file will look like:


```json
{
    "parameters": {
        "my_parameter": 123
    },
    "twig": {
        "class": "Silex\\Provider\\TwigServiceProvider",
        "parameters": {
            "twig.path": "/path/to/twig/templates"
        }
    }
}
```

As you have probably inferred, `$app['my_parameter']` now has the value `123`.
The `Silex\Provider\TwigServiceProvider` has also been registered with the
`$app['twig.path']` parameter set to `/path/to/twig/templates`. When you
register the service providers you want, you may leave out the `parameters`
key if your service provider does not need it.
