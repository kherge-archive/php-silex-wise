Wise for Silex
==============

[![Build Status]](http://travis-ci.org/herrera-io/php-silex-wise)

Wise for Silex integrates the [Wise][] configuration loader with a [Silex][]
application. It also allows you to register services and routes through one
or more configuration files.

```php
$app = new Silex\Application();
$app->register(
    new Herrera\Wise\WiseServiceProvider(),
    array(
        'wise.path' => '/path/to/config',
    )
);
```

Documentation
-------------

- [Installing][]
- [Usage][]
    - [Services][]
    - [Routes][]

[Build Status]: https://secure.travis-ci.org/herrera-io/php-silex-wise.png?branch=master
[Wise]: https://github.com/herrera-io/php-wise
[Silex]: http://silex.sensiolabs.org/
[Installing]: doc/00-Installing.md
[Usage]: doc/01-Usage.md
[Services]: doc/02-Services.md
[Routes]: doc/03-Routes.md
