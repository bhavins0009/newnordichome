<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base;

abstract class AbstractStrategy implements StrategyInterface
{
    /**
     * @var mixed|null
     */
    protected $object;

    /**
     * AbstractStrategy constructor.
     *
     * @param mixed|null $object
     */
    public function __construct($object = null)
    {
        $this->object = $object;
    }

    /**
     * @return array|mixed|null
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return wp_get_document_title();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @return array|null
     */
    public function getImage()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getCanonical()
    {
        return $this->getPermalink();
    }

    /**
     * @return int
     */
    public function getPaginationPagesCount()
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getPaginationBase()
    {
        return '';
    }

    /**
     * @return int|mixed
     */
    public function getPaginationCurrentPage()
    {
        return get_query_var($this->getPaginationPageVar()) ?: 1;
    }
}
