<?php

namespace Herrera\Wise\Silex;

use Herrera\Wise;

/**
 * Adds a `Wise->load()` alias to the `Application` class.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
trait WiseTrait
{
    /**
     * An alias for `$app['wise']->load()`.
     *
     * @see Wise#load
     */
    public function load($resource, $type = null, $require = false)
    {
        return $this['wise']->load($resource, $type, $require);
    }
}
