<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base;

abstract class AbstractArchiveStrategy extends AbstractStrategy
{
    /**
     * @return string
     */
    public function getType()
    {
        return 'object';
    }

    /**
     * @return int
     */
    public function getPaginationPagesCount()
    {
        return $GLOBALS['wp_query']->max_num_pages;
    }

    /**
     * @return string
     */
    public function getPaginationBase()
    {
        global $wp_rewrite;

        return trailingslashit($wp_rewrite->pagination_base);
    }

    /**
     * @return string
     */
    public function getPaginationPageVar()
    {
        return 'paged';
    }
}
