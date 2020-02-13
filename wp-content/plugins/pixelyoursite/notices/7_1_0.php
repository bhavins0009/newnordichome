<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function adminGetPromoNoticesContent() {
    return [
        'woo' => [
          [
              'disabled' => false,
              'from'     => 0,
              'to'       => 2,
              'content'  => 'PixelYourSite: implement the Microsoft (<b>Bing</b>) UET Tag for WooCommerce with this <a href="https://www.pixelyoursite.com/bing-tag?utm_source=free-plugin-notice&utm_medium=plugin&utm_campaign=free-with-woo-notice" target="_blank">dedicated paid add-on</a>'
          ],
        ],
        'edd' => [
          [
              'disabled' => false,
              'from'     => 0,
              'to'       => 2,
              'content'  => 'PixelYourSite: implement the Microsoft (<b>Bing</b>) UET Tag for Easy Digital Downloads with this <a href="https://www.pixelyoursite.com/bing-tag?utm_source=free-plugin-notice&utm_medium=plugin&utm_campaign=free-with-edd-notice" target="_blank">dedicated paid add-on</a>'
          ],
        ],
        'no_woo_no_edd' => [
            [
                'disabled' => false,
                'from'     => 0,
                'to'       => 2,
                'content'  => 'PixelYourSite: implement the Microsoft (<b>Bing</b>) UET Tag with this <a href="https://www.pixelyoursite.com/bing-tag?utm_source=free-plugin-notice&utm_medium=plugin&utm_campaign=free-notice" target="_blank">dedicated paid add-on</a>'
            ],
        ],
    ];
}
