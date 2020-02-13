<?php
/**
 * Created by PhpStorm.
 * User: Villatheme-Thanh
 * Date: 30-09-19
 * Time: 8:18 AM
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WOOMULTI_CURRENCY_Plugin_Google_Index {
	protected $settings;

	public function __construct() {
		$this->settings = WOOMULTI_CURRENCY_Data::get_ins();
		if ( $this->settings->get_enable() ) {
			add_action( 'wp_head', array( $this, 'set_origin_currency' ) );
		}
	}

	public function set_origin_currency() {
		if ( $this->isBot() ) {
//			if ( class_exists( 'WOOCS' ) ) {
//				global $WOOCS;
//				$WOOCS->reset_currency();
//			}
		}
	}

	public function isBot( &$botname = '' ) {
		$bots = array(
			'rambler',
			'googlebot',
			'aport',
			'yahoo',
			'msnbot',
			'turtle',
			'mail.ru',
			'omsktele',
			'yetibot',
			'picsearch',
			'sape.bot',
			'sape_context',
			'gigabot',
			'snapbot',
			'alexa.com',
			'megadownload.net',
			'askpeter.info',
			'igde.ru',
			'ask.com',
			'qwartabot',
			'yanga.co.uk',
			'scoutjet',
			'similarpages',
			'oozbot',
			'shrinktheweb.com',
			'aboutusbot',
			'followsite.com',
			'dataparksearch',
			'google-sitemaps',
			'appEngine-google',
			'feedfetcher-google',
			'liveinternet.ru',
			'xml-sitemaps.com',
			'agama',
			'metadatalabs.com',
			'h1.hrn.ru',
			'googlealert.com',
			'seo-rus.com',
			'yaDirectBot',
			'yandeG',
			'yandex',
			'yandexSomething',
			'Copyscape.com',
			'AdsBot-Google',
			'domaintools.com',
			'Nigma.ru',
			'bing.com',
			'dotnetdotcom'
		);
		foreach ( $bots as $bot ) {
			if ( stripos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false ) {
				$botname = $bot;

				return true;
			}
		}

		return false;
	}

}
