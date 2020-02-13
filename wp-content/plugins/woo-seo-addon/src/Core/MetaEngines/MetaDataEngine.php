<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;

class MetaDataEngine implements MetaEngineInterface
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
        $this->storage = $storage;
        $this->config  = $config;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'meta';
    }

    /**
     * Display markup
     */
    public function display()
    {
        $data = $this->addRobotsData(array());
        echo $this->prepare($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function addRobotsData(array $data)
    {
        $id = $this->storage->getStrategy()->getId();

        if ($id === 'term') {
            $term = $this->storage->getStrategy()->getObject();
            if ($term instanceof \WP_Term) {
                $id = $term->taxonomy;
            }
        }

        if (! empty($this->config['disable_' . $id])) {
            $data['robots'] = 'noindex, follow';
        }

        return $data;
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
     * @param string $name
     * @param string $content
     *
     * @return string
     */
    public function format($name, $content)
    {
        return sprintf('<meta name="%s" content="%s" />%s', esc_attr($name), esc_attr($content), PHP_EOL);
    }
}
