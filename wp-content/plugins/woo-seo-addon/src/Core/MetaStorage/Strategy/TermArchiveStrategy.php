<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractArchiveStrategy;
use WP_Term;

/**
 * Class TermStrategy
 * @property WP_Term|mixed|null $object
 * @package Premmerce\SeoAddon\Core\MetaStorage\Strategy
 */
class TermArchiveStrategy extends AbstractArchiveStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'term';
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
        return get_term_link($this->object);
    }
}
