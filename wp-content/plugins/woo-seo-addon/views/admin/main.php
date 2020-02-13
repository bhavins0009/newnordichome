<?php
if ( ! defined('WPINC')) {
    die;
}
?>
<div class="wrap">
    <h2>Premmerce SEO for WooCommerce</h2>
    <h2 class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab => $name): ?>
            <?php $class = ($tab == $current) ? ' nav-tab-active' : ''; ?>
            <a class='nav-tab<?php echo $class ?>'
               href='?page=<?php echo esc_attr($page) ?>&tab=<?php echo esc_attr($tab) ?>'><?php echo esc_html($name) ?></a>
        <?php endforeach; ?>
    </h2>
    <?php $config->render($current) ?>
</div>

