<?php namespace Premmerce\SeoAddon\Core\Config;

class Config
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var SettingsInterface[]
     */
    private $settings;

    /**
     * @var callable[]
     */
    private $callbacks = array();

    /**
     * Config constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = $id;

        add_action('plugins_loaded', function () {
            $settings = apply_filters('config_' . $this->id, array());
            foreach ($settings as $setting) {
                if ($setting instanceof SettingsInterface) {
                    $this->settings[$setting->getId()] = $setting;
                }
            }
        });

        add_action('admin_init', function () {
            foreach ($this->settings as $setting) {
                $this->register($setting);
            }
        });

        $this->callbacks = array(
            'input'    => array($this, 'renderInput'),
            'select'   => array($this, 'renderSelect'),
            'group'    => array($this, 'renderGroup'),
            'checkbox' => array($this, 'renderCheckbox'),
            'text'     => array($this, 'renderText'),

        );
    }

    /**
     * @param string $id
     *
     * @return ConfigBundle
     */
    public function getBundle($id)
    {
        return new ConfigBundle($id);
    }

    /**
     * @param SettingsInterface $settings
     */
    public function addSettings(SettingsInterface $settings)
    {
        $this->settings[$settings->getId()] = $settings;
    }

    /**
     * @return SettingsInterface[]
     */
    public function getAll()
    {
        return $this->settings;
    }

    /**
     * @param string $id
     *
     * @return null|SettingsInterface
     */
    public function getSettings($id)
    {
        return $this->hasSettings($id) ? $this->settings[$id] : null;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasSettings($id)
    {
        return isset($this->settings[$id]);
    }

    /**
     * @param SettingsInterface $settings
     */
    protected function register(SettingsInterface $settings)
    {
        $group   = $settings->getId();
        $section = $settings->getId();
        $page    = $settings->getId();

        $fields = $settings->getFields();
        add_settings_section($section, null, null, $settings->getId());

        $defaults = array('title' => '', 'type' => 'text');
        foreach ($fields as $name => $field) {
            $field = array_replace($defaults, $field);

            $id = $settings->getId() . '_' . $name;

            if ($field['type'] === 'section') {
                $section = $settings->getId() . '_' . $name;

                add_settings_section($section, $field['title'], function () use ($field) {
                    echo $this->getDescription($field);
                }, $settings->getId());
            } else {
                if ($field['type'] === 'group') {
                    foreach ($field['fields'] as $subName => $subField) {
                        register_setting($group, $id . '_' . $subName);
                    }
                } else {
                    register_setting($group, $id);
                }

                $field['name'] = $id;

                if ($callback = $this->getCallback($field)) {
                    add_settings_field($id, $field['title'], $callback, $page, $section, $field);
                }
            }
        }
    }

    /**
     * @param string $id
     */
    public function render($id)
    {
        if ($this->hasSettings($id)) {
            $this->renderSettingsPage($this->getSettings($id));
        }
    }

    /**
     * @param SettingsInterface $settings
     */
    public function renderSettingsPage(SettingsInterface $settings)
    {
        ?>
        <form action="<?php echo admin_url('options.php') ?>" method="POST">
            <?php settings_fields($settings->getId()); ?>
            <?php do_settings_sections($settings->getId()); ?>
            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * @param array $field
     *
     * @return array
     */
    public function getCallback($field)
    {
        if (isset($field['type']) && array_key_exists($field['type'], $this->callbacks)) {
            return $this->callbacks[$field['type']];
        }
    }

    /**
     * @param array $data
     */
    public function renderText(array $data)
    {
        printf('<b>%s</b>%s', $data['value'], $this->getDescription($data));
    }

    /**
     * @param array $data
     */
    public function renderGroup(array $data)
    {
        foreach ($data['fields'] as $name => $field) {
            $field['name'] = $data['name'] . '_' . $name;
            if ($callback = $this->getCallback($field)) {
                call_user_func($callback, $field);
            }
        }
    }

    /**
     * @param array $data
     */
    public function renderCheckbox(array $data)
    {
        $checked = checked(! empty(get_option($data['name'])), true, false);

        $input = sprintf('<input type="checkbox" name="%s" %s>', esc_attr($data['name']), esc_attr($checked));

        $label = isset($data['label']) ? $data['label'] : '';
        printf(
            '<fieldset><label>%s%s%s</label></fieldset>',
            $input,
            esc_html($label),
            $this->getDescription($data)
        );
    }

    /**
     * @param array $data
     */
    public function renderInput(array $data)
    {
        printf(
            '<input type="text" name="%s" value="%s">%s',
            esc_attr($data['name']),
            esc_html(get_option($data['name'])),
            $this->getDescription($data)
        );
    }

    /**
     * @param array $data
     */
    public function renderSelect(array $data)
    {
        $options = array();

        $selectedValue = get_option($data['name']);

        foreach ($data['options'] as $value => $title) {
            $selected  = selected($selectedValue, $value, false);
            $options[] = sprintf('<option value="%s" %s>%s</option>', esc_attr($value), $selected, esc_html($title));
        }

        printf(
            '<select name="%s" type="text">%s</select>%s',
            esc_attr($data['name']),
            implode(PHP_EOL, $options),
            $this->getDescription($data)
        );
    }

    /**
     * @param array $data
     *
     * @return int
     */
    public function getDescription(array $data)
    {
        if (! empty($data['description'])) {
            $class = $data['type'] == 'section' ? '' : 'description';

            $data['description'] = wp_kses($data['description'], array(
                'a' => array(
                    'href'   => array(),
                    'target' => array(),
                )
            ));

            return sprintf('<p class="%s">%s</p>', esc_attr($class), $data['description']);
        }
    }
}
