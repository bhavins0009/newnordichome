<?php namespace Premmerce\SeoAddon\Admin\Pages;

use Premmerce\SeoAddon\Core\Config\SettingsInterface;

class Social implements SettingsInterface
{
    public function getId()
    {
        return 'premmerce_seo_social';
    }

    public function getTitle()
    {
        return __('Social', 'woo-seo-addon');
    }

    public function getFields()
    {
        return array(
            'social'      => array(
                'type'        => 'section',
                'title'       => __('Accounts', 'woo-seo-addon'),
                'description' => __(
                    'These links will be added to the schema.org markup and search engines will be able to associate your site with your social profiles.',
                    'woo-seo-addon'
                ),
            ),
            'facebook'    => array(
                'type'        => 'input',
                'title'       => 'Facebook',
                'description' => __('For example', 'woo-seo-addon') . ': https://www.facebook.com/YourCompany',
            ),
            'twitter'     => array(
                'type'        => 'input',
                'title'       => 'Twitter',
                'description' => __('For example', 'woo-seo-addon') . ': https://www.twitter.com/YourCompany',
            ),
            'youtube'     => array(
                'type'        => 'input',
                'title'       => 'Youtube',
                'description' => __('For example', 'woo-seo-addon') . ': https://www.youtube.com/channel/YourCompany',
            ),
            'google_plus' => array(
                'type'        => 'input',
                'title'       => 'Google plus',
                'description' => __('For example', 'woo-seo-addon') . ': https://plus.google.com/YourCompany',
            ),
            'instagram'   => array(
                'type'        => 'input',
                'title'       => 'Instagram',
                'description' => __('For example', 'woo-seo-addon') . ': https://instagram.com/YourCompany',
            ),
            'linkedIn'    => array(
                'type'        => 'input',
                'title'       => 'LinkedIn',
                'description' => __('For example', 'woo-seo-addon') . ': https://linkedIn.com/company/YourCompany',
            ),
            'pinterest'   => array(
                'type'        => 'input',
                'title'       => 'Pinterest',
                'description' => __('For example', 'woo-seo-addon') . ': https://pinterest.com/company/YourCompany',
            ),
        );
    }
}
