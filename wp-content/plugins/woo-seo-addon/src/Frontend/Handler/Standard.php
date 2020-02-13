<?php namespace Premmerce\SeoAddon\Frontend\Handler;

use Premmerce\SeoAddon\Core\Config\ConfigBundle;
use Premmerce\SeoAddon\Core\MetaEngines\EngineAggregator;

class Standard
{
    /**
     * @var EngineAggregator
     */
    private $engine;

    /**
     * @var ConfigBundle
     */
    private $config;

    public function __construct(EngineAggregator $engine, ConfigBundle $config)
    {
        $this->engine = $engine;
        $this->config = $config;

        add_action('wp_head', array($this->engine, 'display'), 1);


        if ($this->config->isEnabled('markup_og')) {
            add_filter('language_attributes', array($this, 'addOgPrefix'));
        }
    }

    /**
     * Add og prefix to head tag
     *
     * @param $data
     *
     * @return string
     */
    public function addOgPrefix($data)
    {
        return $data . ' prefix="og: http://ogp.me/ns#"';
    }
}
