<?php namespace Premmerce\SeoAddon\Frontend;

use Premmerce\SeoAddon\Core\Config\ConfigBundle;
use Premmerce\SeoAddon\Core\MetaEngines\EngineAggregator;
use Premmerce\SeoAddon\Core\MetaEngines\LinkedData;
use Premmerce\SeoAddon\Core\MetaEngines\LinksEngine;
use Premmerce\SeoAddon\Core\MetaEngines\MetaDataEngine;
use Premmerce\SeoAddon\Core\MetaEngines\OpenGraph;
use Premmerce\SeoAddon\Core\MetaEngines\TwitterCard;
use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\AuthorArchiveStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\StrategyInterface;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\DateArchiveStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\MainArchiveStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\MainSingleStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\SingleStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\PostTypeArchiveStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\ProductSingleStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\SearchArchiveStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\TermArchiveStrategy;
use Premmerce\SeoAddon\Core\Plugins;
use Premmerce\SeoAddon\Frontend\Handler\Standard;
use Premmerce\SeoAddon\Frontend\Handler\YoastSEO;
use WC_Product;
use WP_Post;
use WP_Post_Type;
use WP_Term;

class Seo
{
    /**
     * @var EngineAggregator
     */
    private $engine;

    /**
     * @var MetaDataStorage
     */
    private $metaStorage;

    /**
     * @var ConfigBundle
     */
    private $config;

    /**
     * Standard constructor.
     */
    public function __construct()
    {
        $this->config = new ConfigBundle('premmerce_seo_settings');

        $this->initEngines();

        $this->runHandler();

        add_action('wp_head', array($this, 'setMetaStorageStrategy'), 0);

        if ($this->config->isEnabled('markup_ld')) {
            add_filter('woocommerce_structured_data_product', array($this, 'extendLdProductData'), 10, 2);
        }

        if ($this->config->isEnabled('image_alts')) {
            add_filter('get_post_metadata', array($this, 'changeImageAlt'), 10, 3);
        }
    }

    /**
     * Run metadata handler depending from environment
     */
    public function runHandler()
    {
        if (Plugins::isYoastActive()) {
            new YoastSEO($this->engine, $this->config);
        } else {
            new Standard($this->engine, $this->config);
        }
    }

    /**
     * Extend woo product data - add brand
     *
     * @param array $data
     * @param WC_Product $product
     *
     * @return array
     */
    public function extendLdProductData(array $data, WC_Product $product)
    {
        /** @var LinkedData $ld */
        if ($ld = $this->engine->getEngine('ld')) {
            $data = $ld->extendProductData($data, $product);
        }

        return $data;
    }

    /**
     * Init metadata engines
     */
    protected function initEngines()
    {
        $this->metaStorage = new MetaDataStorage();

        $this->engine = new EngineAggregator($this->metaStorage);

        $config = new ConfigBundle('premmerce_seo_settings_links', array('canonical', 'navigation'));

        if ($config->isEnabled('canonical') || $config->isEnabled('navigation')) {
            $this->engine->addEngine(new LinksEngine($this->metaStorage, $config->toArray()));
        }
        $config = new ConfigBundle('premmerce_seo_metadata_indexing', array(
            'disable_search',
            'disable_date',
            'disable_author',
            'disable_post_tag',
            'disable_category'
        ));

        $this->engine->addEngine(new MetaDataEngine($this->metaStorage, $config->toArray()));

        if ($this->config->isEnabled('markup_og')) {
            $this->engine->addEngine(new OpenGraph($this->metaStorage));
        }

        if ($this->config->isEnabled('markup_tc')) {
            $config = (new ConfigBundle('premmerce_seo_settings_twitter', array('card_type')))->toArray();
            $this->engine->addEngine(new TwitterCard($this->metaStorage, $config));
        }

        if ($this->config->isEnabled('markup_ld')) {
            $this->engine->addEngine(new LinkedData($this->metaStorage));
        }
    }

    /**
     * Change image alt to product name in product
     *
     * @param string $value
     * @param int $objectId
     * @param string $metaKey
     *
     * @return null|string
     */
    public function changeImageAlt($value, $objectId, $metaKey)
    {
        if ($metaKey == '_wp_attachment_image_alt' && get_post_type() == 'product') {
            global $product;

            /** @var WC_Product $product */
            if ($product instanceof WC_Product) {
                $images = $product->get_gallery_image_ids();

                $productImage = (int)$product->get_image_id();
                if ($productImage) {
                    $images[] = $productImage;
                }

                if (in_array($objectId, $images)) {
                    return get_the_title();
                }
            }
        }

        return $value;
    }

    /**
     * Set metadata storage strategy depending from current page
     */
    public function setMetaStorageStrategy()
    {
        $object = get_queried_object();

        $strategy = null;

        if (is_search()) {
            $strategy = new SearchArchiveStrategy($object);
        } elseif (is_front_page() || is_home()) {
            $strategy = is_singular() ? new MainSingleStrategy($object) : new MainArchiveStrategy($object);
        } elseif (is_singular() && $object instanceof WP_Post) {
            if ($object->post_type == 'product') {
                $strategy = new ProductSingleStrategy($object);
            } else {
                $strategy = new SingleStrategy($object);
            }
        } elseif ($object instanceof WP_Term) {
            $strategy = new TermArchiveStrategy($object);
        } elseif ($object instanceof WP_Post_Type) {
            $strategy = new PostTypeArchiveStrategy($object);
        } elseif (is_author()) {
            $strategy = new AuthorArchiveStrategy($object);
        } elseif (is_archive() && is_date()) {
            $strategy = new DateArchiveStrategy();
        }


        if ($strategy instanceof StrategyInterface) {
            $this->metaStorage->setStrategy($strategy);
        }
    }
}
