<?php

namespace Herrera\Wise\Silex\Loader;

use Herrera\Wise\Loader\XmlFileLoader as Base;
use Herrera\Wise\Silex\SilexAwareInterface;
use Silex\Application;

/**
 * Integrates Silex support into the XML file loader.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class XmlFileLoader extends Base implements SilexAwareInterface
{
    /**
     * The Silex application.
     *
     * @var Application
     */
    private $app;

    /**
     * Returns the Silex application.
     *
     * @return Application $app The application.
     */
    public function getSilex()
    {
        return $this->app;
    }

    /**
     * Sets the Silex application.
     *
     * @param Application $app The application.
     */
    public function setSilex(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @override
     */
    protected function doLoad($file)
    {
        $data = parent::doLoad($file);

        if (isset($data['parameters'])) {
            foreach ($data['parameters'] as $parameter => $value) {
                $this->app[$parameter] = $value;
            }
        }

        return $data;
    }
}
