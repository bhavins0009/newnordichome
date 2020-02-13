<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base;

trait MainStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'main';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return get_bloginfo('description');
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return get_home_url();
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'website';
    }

    /**
     * @return string
     */
    public function getPaginationBase()
    {
        global $wp_rewrite;

        return trailingslashit($wp_rewrite->pagination_base);
    }
}
