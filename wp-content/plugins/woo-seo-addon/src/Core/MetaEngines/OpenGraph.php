<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;
use WC_Product;

class OpenGraph implements MetaEngineInterface
{
    /**
     * @var MetaDataStorage
     */
    private $storage;

    /**
     * @var array
     */
    private $config;

    /**
     * OpenGraph constructor.
     *
     * @param MetaDataStorage $storage
     * @param array $config
     */
    public function __construct(MetaDataStorage $storage, array $config = array())
    {
        $this->storage = $storage;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'og';
    }

    /**
     * Display markup
     */
    public function display()
    {
        $type = $this->storage->getType();

        $data = array(
            'locale'       => $this->storage->getLocale(),
            'type'         => $type,
            'title'        => $this->storage->getTitle(),
            'description'  => $this->storage->getDescription(),
            'url'          => $this->storage->getPermalink(),
            'site_name' => $this->storage->getSiteName(),
        );

        if ($image = $this->storage->getImage()) {
            list($data['image'], $data['image:width'], $data['image:height']) = $image;
        }

        switch ($type) {
            case 'website':
                $data = $this->extendWebsite($data);
                break;
            case 'product':
                $data = $this->extendProduct($data);
                break;
        }

        echo $this->prepare($data);
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function prepare(array $data)
    {
        $string = '';
        $data   = array_filter($data);
        foreach ($data as $key => $value) {
            $string .= $this->format($key, $value);
        }

        return $string;
    }

    /**
     * @param string $property
     * @param string $content
     *
     * @return string
     */
    public function format($property, $content)
    {
        return sprintf('<meta property="og:%s" content="%s" />%s', esc_attr($property), esc_attr($content), PHP_EOL);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function extendProduct(array $data)
    {
        $product = wc_get_product();

        if ($this->storage->getType() === 'product' && $product instanceof WC_Product) {
            if ($price = $product->get_price()) {
                $data['price:amount']   = $price;
                $data['price:currency'] = esc_attr(get_woocommerce_currency());
            }

            $data['product:availability'] = $product->is_in_stock() ? 'instock' : 'pending';
            $data['product:retailer_item_id'] = $product->get_sku();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function extendWebsite(array $data)
    {
        if ($logo = $this->storage->getSiteLogo()) {
            $data['logo'] = $logo;
        }

        return $data;
    }
}
