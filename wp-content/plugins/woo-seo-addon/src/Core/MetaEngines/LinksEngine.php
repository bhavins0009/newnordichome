<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;

class LinksEngine implements MetaEngineInterface
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
     * MetaEngineInterface constructor.
     *
     * @param MetaDataStorage $storage
     * @param array $config
     */
    public function __construct(MetaDataStorage $storage, array $config = array())
    {

        /**
         * Remove canonical if rule found
         */
        add_action('premmerce_filter_rule_found', function () {
            $this->config['canonical'] = false;
        });

        $this->storage = $storage;
        $this->config  = $config;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'links';
    }

    /**
     * Display markup
     */
    public function display()
    {
        $links = array();

        if (! empty($this->config['canonical'])) {
            $links['canonical'] = $this->storage->getCanonical();
        }
        if (! empty($this->config['navigation'])) {
            $links['prev'] = $this->storage->getPrevPage();
            $links['next'] = $this->storage->getNextPage();
        }

        echo $this->prepare($links);
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
     * @param string $rel
     * @param string $href
     *
     * @return string
     */
    public function format($rel, $href)
    {
        return sprintf('<link rel="%s" href="%s" />%s', esc_attr($rel), esc_attr($href), PHP_EOL);
    }
}
