Wise for Silex
==============

[![Build Status]](http://travis-ci.org/herrera-io/php-wise)

Wise for Silex integrates the [Wise][] configuration loader with a [Silex][]
application.

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
    - [Loading Services][]

[Build Status]: https://secure.travis-ci.org/herrera-io/php-silex-wise.png?branch=master
[Wise]: https://github.com/herrera-io/php-wise
[Silex]: http://silex.sensiolabs.org/
[Installing]: doc/00-Installing.md
[Usage]: doc/01-Usage.md
[Loading Services]: doc/02-LoadingServices.md
