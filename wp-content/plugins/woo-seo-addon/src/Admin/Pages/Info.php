<?php namespace Premmerce\SeoAddon\Admin\Pages;

use Premmerce\SeoAddon\Core\Config\SettingsInterface;

class Info implements SettingsInterface
{
    public function getId()
    {
        return 'premmerce_seo';
    }

    public function getTitle()
    {
        return __('Info', 'woo-seo-addon');
    }

    public function getFields()
    {
        return array(
            'info'            => array(
                'title'       => __('Information', 'woo-seo-addon'),
                'type'        => 'section',
                'description' => __('These fields will be added to the schema.org markup.', 'woo-seo-addon'),
            ),
            'address'         => array(
                'type'        => 'input',
                'title'       => __('Address', 'woo-seo-addon'),
                'description' => __('For example', 'woo-seo-addon') . ': 20341 Whitworth Institute 405 N. Whitworth',
            ),
            'email'           => array(
                'type'        => 'input',
                'title'       => __('Email', 'woo-seo-addon'),
                'description' => __('For example', 'woo-seo-addon') . ': jane-doe@xyz.ed',
            ),
            'telephone'       => array(
                'type'        => 'input',
                'title'       => __('Phone', 'woo-seo-addon'),
                'description' => __('For example', 'woo-seo-addon') . ': +18005551234',
            ),
            'openingHours'    => array(
                'type'        => 'input',
                'title'       => __('Opening hours', 'woo-seo-addon'),
                'description' => __('For example', 'woo-seo-addon') . ': Mo,Tu,We,Th 09:00-12:00',
            ),
            'paymentAccepted' => array(
                'type'        => 'input',
                'title'       => __('Payment accepted', 'woo-seo-addon'),
                'description' => __('For example', 'woo-seo-addon') . ': Cash, Credit Card',
            ),
        );
    }
}
