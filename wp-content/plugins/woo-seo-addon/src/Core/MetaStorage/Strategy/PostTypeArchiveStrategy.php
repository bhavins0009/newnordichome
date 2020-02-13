<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractArchiveStrategy;
use WP_Post_Type;

/**
 * Class PostTypeStrategy
 * @property WP_Post_Type|mixed|null $object
 * @package Premmerce\SeoAddon\Core\MetaStorage\Strategy
 */
class PostTypeArchiveStrategy extends AbstractArchiveStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'post_type';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->object->description;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return get_post_type_archive_link($this->object->name);
    }
}
