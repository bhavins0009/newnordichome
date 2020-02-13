<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;

class TwitterCard implements MetaEngineInterface
{
    /**
     * @var MetaDataStorage
     */
    private $storage;

    private $cardType = 'summary_large_image';

    /**
     * @var array
     */
    private $config;

    /**
     * TwitterCard constructor.
     *
     * @param MetaDataStorage $storage
     * @param array $config
     */
    public function __construct(MetaDataStorage $storage, array $config = array())
    {
        $this->storage = $storage;
        $this->config  = $config;

        $this->cardType = isset($this->config['card_type'])?$this->config['card_type']:'summary_large_image';
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'tc';
    }

    /**
     * Display markup
     */
    public function display()
    {
        $data['card']        = $this->cardType;
        $data['title']       = $this->storage->getTitle();
        $data['description'] = $this->storage->getDescription();
        $data['image']       = $this->storage->getImageSrc();
        $data['site']        = $this->storage->getTwitterName();

        echo $this->prepare(array_filter($data));
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
        return sprintf('<meta name="twitter:%s" content="%s" />%s', esc_attr($name), esc_attr($content), PHP_EOL);
    }
}
