<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;
use WC_Product;

class LinkedData implements MetaEngineInterface
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
     * LinkedData constructor.
     *
     * @param MetaDataStorage $storage
     * @param array $config
     */
    public function __construct(MetaDataStorage $storage, array $config = array())
    {
        $this->storage = $storage;
        $this->config  = $config;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'ld';
    }

    /**
     * Display markup
     */
    public function display()
    {
        echo $this->prepare($this->getOrganizationData());
    }

    /**
     * Prepare json ld data
     *
     * @param array $data
     *
     * @return string
     */
    public function prepare(array $data)
    {
        $data = array_filter($data);
        $data = json_encode($data, JSON_UNESCAPED_SLASHES);

        return sprintf("<script type='application/ld+json'>%s</script>%s", $data, PHP_EOL);
    }

    /**
     * @return array
     */
    public function getOrganizationData()
    {
        $data = array(
            "@context" => "https://schema.org",
            "@type"    => "Organization",
            "url"      => home_url(),
            "logo"     => $this->storage->getSiteLogo(),
            "name"     => $this->storage->getSiteName(),
            "sameAs"   => $this->storage->getSocialNetworks(),
        );

        return $this->extendLinkedData($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function extendLinkedData(array $data)
    {
        if (isset($data['@type']) && $data['@type'] === 'Organization') {
            $fields = array("address", "email", "telephone", "openingHours", "paymentAccepted");

            foreach ($fields as $field) {
                $value = get_option('premmerce_seo_' . $field, '');
                if ($value) {
                    $data[$field] = get_option('premmerce_seo_' . $field, '');
                }
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param WC_Product $product
     *
     * @return array
     */
    public function extendProductData(array $data, WC_Product $product)
    {
        $brandTaxonomy = get_option('premmerce_seo_settings_brand_field');

        if (taxonomy_exists($brandTaxonomy)) {
            $brands = wp_get_post_terms($product->get_id(), $brandTaxonomy, array('fields' => 'names'));
            if (! empty($brands[0])) {
                $data['brand'] = $brands[0];
            }
        }

        return $data;
    }
}
