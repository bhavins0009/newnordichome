<?php namespace Premmerce\SeoAddon\Core\Updates;

use Premmerce\SeoAddon\SeoAddonPlugin;

class Updater
{
    const DB_OPTION = 'premmerce_seo_db_version';

    public function checkForUpdates()
    {
        return $this->compare(SeoAddonPlugin::VERSION);
    }

    private function compare($version)
    {
        $dbVersion = get_option(self::DB_OPTION, '1.1');

        return version_compare($dbVersion, $version, '<');
    }

    public function update()
    {
        if ($this->checkForUpdates()) {
            foreach ($this->getUpdates() as $version => $callback) {
                if ($this->compare($version)) {
                    call_user_func($callback);
                }
            }

            update_option(self::DB_OPTION, SeoAddonPlugin::VERSION);
        }
    }

    public function getUpdates()
    {
        return array(
            '2.0' => array($this, 'update2_0'),
        );
    }

    public function update2_0()
    {
        if ($brand = get_option('premmerce_seo_brand_field')) {
            update_option('premmerce_seo_settings_brand_field', 'pa_' . $brand);
            delete_option('premmerce_seo_brand_field');
        }

        update_option('premmerce_seo_settings_markup_tc', 'on');
        update_option('premmerce_seo_settings_markup_ld', 'on');
        update_option('premmerce_seo_settings_markup_og', 'on');
        update_option('premmerce_seo_settings_image_alts', 'on');

        if (taxonomy_exists('product_brand')) {
            update_option('premmerce_seo_settings_brand_field', 'product_brand');
        }
    }
}
