<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;

class EngineAggregator implements MetaEngineInterface
{
    /**
     * @var MetaDataStorage
     */
    private $storage;

    /**
     * @var MetaEngineInterface[]
     */
    private $engines = array();

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
        $this->storage = $storage;
        $this->config  = $config;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'aggregator';
    }

    /**
     * @param MetaEngineInterface $engine
     */
    public function addEngine(MetaEngineInterface $engine)
    {
        $this->engines[$engine->getId()] = $engine;
    }

    /**
     * @return MetaEngineInterface[]
     */
    public function getAll()
    {
        return $this->engines;
    }

    /**
     * @param string $id
     *
     * @return MetaEngineInterface|null
     */
    public function getEngine($id)
    {
        return $this->hasEngine($id) ? $this->engines[$id] : null;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function hasEngine($id)
    {
        return array_key_exists($id, $this->engines);
    }

    /**
     * Display markup
     */
    public function display()
    {
        if (! empty($this->engines)) {
            print('<!-- Premmerce SEO for WooCommerce  -->' . PHP_EOL);
            foreach ($this->engines as $engine) {
                $engine->display();
            }
            print('<!-- /Premmerce SEO for WooCommerce  -->' . PHP_EOL);
        }
    }
}
