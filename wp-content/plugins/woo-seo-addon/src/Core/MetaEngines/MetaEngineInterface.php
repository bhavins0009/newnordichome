<?php namespace Premmerce\SeoAddon\Core\MetaEngines;

use Premmerce\SeoAddon\Core\MetaStorage\MetaDataStorage;

interface MetaEngineInterface
{
    /**
     * MetaEngineInterface constructor.
     *
     * @param MetaDataStorage $storage
     * @param array $config
     */
    public function __construct(MetaDataStorage $storage, array $config = array());

    /**
     * @return string
     */
    public function getId();

    /**
     * Display markup
     */
    public function display();
}
