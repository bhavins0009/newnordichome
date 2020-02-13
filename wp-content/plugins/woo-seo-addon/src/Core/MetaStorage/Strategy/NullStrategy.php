<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractStrategy;

class NullStrategy extends AbstractStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'null';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPaginationPageVar()
    {
        return '';
    }
}
