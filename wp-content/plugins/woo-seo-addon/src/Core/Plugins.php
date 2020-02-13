<?php namespace Premmerce\SeoAddon\Core;

class Plugins
{
    /**
     * @return bool
     */
    public static function isYoastActive()
    {
        return self::isPluginActive('wordpress-seo/wp-seo.php')
               || self::isPluginActive('wordpress-seo/wp-seo-premium.php');
    }

    /**
     * Check All in one seo pack installation
     * @return bool
     */
    public static function isAIOSPActive()
    {
        return self::isPluginActive('all-in-one-seo-pack/all_in_one_seo_pack.php')
               || self::isPluginActive('all-in-one-seo-pack-pro/all_in_one_seo_pack.php');
    }

    /**
     * @return bool
     */
    public static function isWooActive()
    {
        return self::isPluginActive('woocommerce/woocommerce.php');
    }

    public static function isPluginActive($plugin)
    {
        return in_array($plugin, get_option('active_plugins'));
    }

    /**
     * @return bool
     */
    public static function isAlone()
    {
        return ! self::isAIOSPActive() && ! self::isYoastActive();
    }
}
