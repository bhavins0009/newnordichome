<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractStrategy;
use WP_Post;

/**
 * Class SingleStrategy
 * @property  WP_Post $object
 * @package Premmerce\SeoAddon\Core\MetaStorage\Strategy
 */
class SingleStrategy extends AbstractStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'post';
    }

    /**
     * @return array|null
     */
    public function getImage()
    {
        return wp_get_attachment_image_src(get_post_thumbnail_id($this->object->ID), 'full');
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->object->post_excerpt;
    }

    /**
     * @return false|string
     */
    public function getPermalink()
    {
        return get_permalink($this->object);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'article';
    }

    /**
     * @return int
     */
    public function getPaginationPagesCount()
    {
        return substr_count($this->object->post_content, '<!--nextpage-->') + 1;
    }

    /**
     * @return string
     */
    public function getPaginationPageVar()
    {
        return 'page';
    }
}
