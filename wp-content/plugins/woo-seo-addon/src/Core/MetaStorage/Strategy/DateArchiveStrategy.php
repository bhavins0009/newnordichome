<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy;

use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractArchiveStrategy;

/**
 * Class DateStrategy
 * @property null $object
 * @package Premmerce\SeoAddon\Core\MetaStorage\Strategy
 */
class DateArchiveStrategy extends AbstractArchiveStrategy
{
    /**
     * @return string
     */
    public function getId()
    {
        return 'date';
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        if (is_day()) {
            return get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
        } elseif (is_month()) {
            return get_month_link(get_query_var('year'), get_query_var('monthnum'));
        } elseif (is_year()) {
            return get_year_link(get_query_var('year'));
        }
    }
}
