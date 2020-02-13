<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractArchiveStrategy;
use WP_User;

/**
 * Class AuthorStrategy
 * @property WP_User|mixed|null $object
 * @package Premmerce\SeoAddon\Core\MetaStorage\Strategy
 */
class AuthorArchiveStrategy extends AbstractArchiveStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'author';
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return get_author_posts_url(get_query_var('author'), get_query_var('author_name'));
    }
}
