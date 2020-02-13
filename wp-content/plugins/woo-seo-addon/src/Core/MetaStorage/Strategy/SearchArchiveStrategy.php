<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractArchiveStrategy;
use WP_Post_Type;

/**
 * Class SearchStrategy
 * @property WP_Post_Type|mixed|null $object
 * @package Premmerce\SeoAddon\Core\MetaStorage\Strategy
 */
class SearchArchiveStrategy extends AbstractArchiveStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'search';
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        $link = get_search_link();

        if ($this->object instanceof WP_Post_Type) {
            add_query_arg('post_type', $this->object->name, $link);
        }

        return $link;
    }
}
