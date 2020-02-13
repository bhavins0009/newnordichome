<?php namespace Premmerce\SeoAddon\Core\Config;

class ConfigBundle
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $values;

    /**
     * ConfigBundle constructor.
     *
     * @param $id
     * @param array $keys
     */
    public function __construct($id, array $keys = array())
    {
        $this->id     = $id;
        $this->values = array();

        $this->load($keys);
    }

    /**
     * @param array $keys
     */
    public function load(array $keys = array())
    {
        foreach ($keys as $key) {
            $this->values[$key] = $this->get($key);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * @param string $key
     * @param bool|mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = false)
    {
        return get_option($this->id . '_' . $key, $default);
    }

    public function isEnabled($key)
    {
        return ! empty($this->get($key));
    }
}
