<?php

namespace Herrera\Wise\Silex;

use Silex\Application;

/**
 * Defines how a Silex aware class must be implemented.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
interface SilexAwareInterface
{
    /**
     * Returns the Silex application instance.
     *
     * @return Application The application instance.
     */
    public function getSilex();

    /**
     * Sets the Silex application instance.
     *
     * @param Application $app The application instance.
     */
    public function setSilex(Application $app);
}
