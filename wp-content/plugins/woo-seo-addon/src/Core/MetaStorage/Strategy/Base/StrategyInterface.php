<?php namespace Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base;

interface StrategyInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return array|null
     */
    public function getImage();

    /**
     * @return array|null
     */
    public function getObject();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getPermalink();

    /**
     * @return string
     */
    public function getCanonical();

    /**
     * @return int
     */
    public function getPaginationPagesCount();

    /**
     * @return string
     */
    public function getPaginationBase();

    /**
     * @return string
     */
    public function getPaginationPageVar();

    /**
     * @return int
     */
    public function getPaginationCurrentPage();
}
