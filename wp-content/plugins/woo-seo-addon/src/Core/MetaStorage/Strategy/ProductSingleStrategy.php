<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

class ProductSingleStrategy extends SingleStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'product';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'product';
    }
}
