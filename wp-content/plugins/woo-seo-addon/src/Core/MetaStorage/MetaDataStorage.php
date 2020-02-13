<?php namespace Premmerce\SeoAddon\Core\MetaStorage;

use Premmerce\SeoAddon\Core\Config\ConfigBundle;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\Base\AbstractStrategy;
use Premmerce\SeoAddon\Core\MetaStorage\Strategy\NullStrategy;

/**
 * Store metadata for current page type
 * Init should be called queried object is ready e.g: 'wp_head'
 * Class MetaDataStorage
 * @package Premmerce\SeoAddon\Frontend\Storage
 */
class MetaDataStorage
{
    /**
     * @var AbstractStrategy
     */
    protected $strategy;

    /**
     * @var array
     */
    protected $socialNetworks;

    /**
     * @var string|null
     */
    protected $twitterName;

    /**
     * @var array
     */
    protected $socialNetworksKeys = array(
        'facebook',
        'twitter',
        'youtube',
        'google_plus',
        'instagram',
        'linkedIn',
        'pinterest',
    );

    /**
     * MetaDataStorage constructor.
     *
     * @param AbstractStrategy $strategy
     */
    public function __construct(AbstractStrategy $strategy = null)
    {
        $this->strategy       = $strategy ?: new NullStrategy();
        $socialConfig         = new ConfigBundle('premmerce_seo_social', $this->socialNetworksKeys);
        $this->socialNetworks = array_filter($socialConfig->toArray());
    }

    /**
     * @return array
     */
    public function getSocialNetworks()
    {
        return array_values($this->socialNetworks);
    }

    /**
     * @return string|null
     */
    public function getTwitterName()
    {
        if (is_null($this->twitterName) && isset($this->socialNetworks['twitter'])) {
            $data = explode('/', $this->socialNetworks['twitter']);

            $data = array_filter($data);

            $name = array_pop($data);

            $this->twitterName = $name ? '@' . $name : '';
        }

        return $this->twitterName;
    }

    /**
     * @param AbstractStrategy $strategy
     */
    public function setStrategy(AbstractStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * @return AbstractStrategy
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getStrategy()->getType();
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return get_locale();
    }

    /**
     * @return string|null
     */
    public function getSiteName()
    {
        return get_bloginfo('name');
    }

    /**
     * @return string|null
     */
    public function getSiteLogo()
    {
        $custom_logo_id = get_theme_mod('custom_logo');
        $image          = wp_get_attachment_image_src($custom_logo_id, 'full');

        return ! empty($image[0]) ? empty($image[0]) : null;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getStrategy()->getTitle();
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getStrategy()->getDescription();
    }

    /**
     * @return string|null
     */
    public function getImageSrc()
    {
        $image = $this->getStrategy()->getImage();

        return ! empty($image[0]) ? $image[0] : null;
    }

    /**
     * @return array|null
     */
    public function getImage()
    {
        return $this->getStrategy()->getImage();
    }

    /**
     * @return string|null
     */
    public function getPermalink()
    {
        return $this->getStrategy()->getPermalink();
    }

    /**
     * Get canonical url
     * @return string|null
     */
    public function getCanonical()
    {
        $canonical = $this->getStrategy()->getCanonical();

        return $this->addPaginationPart($canonical, $this->strategy->getPaginationCurrentPage());
    }

    /**
     * @return string|null
     */
    public function getPrevPage()
    {
        $current = $this->strategy->getPaginationCurrentPage();
        if ($current > 2) {
            return $this->addPaginationPart($this->getPermalink(), $current - 1);
        } elseif ($current === 2) {
            return $this->getPermalink();
        }
    }

    /**'
     * @return string|null
     */
    public function getNextPage()
    {
        if ($this->strategy->getPaginationCurrentPage() < $this->strategy->getPaginationPagesCount()) {
            return $this->addPaginationPart($this->getPermalink(), $this->strategy->getPaginationCurrentPage() + 1);
        }
    }

    /**
     * Add pagination to url
     *
     * @param $url
     * @param $page
     *
     * @return string
     */
    protected function addPaginationPart($url, $page)
    {
        global $wp_rewrite;

        if ($url && $page > 1) {
            if ($wp_rewrite->using_permalinks()) {
                $url = user_trailingslashit(trailingslashit($url) . $this->getStrategy()->getPaginationBase() . $page);
            } else {
                $url = add_query_arg($this->getStrategy()->getPaginationPageVar(), $page, $url);
            }
        }

        return $url;
    }
}
