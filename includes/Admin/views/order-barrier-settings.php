<?php
settings_errors();
$setting_options = wp_parse_args(get_option($this->_optionName), $this->_defaultOptions);

if (!isset($setting_options['builder_id'])) {
    $current_tab = 'general_settings';
} else {
    $current_tab = $setting_options['builder_id'];
}
?>
<div class="order-detect-settings-wrap">
    <?php do_action('order_detect_settings_header'); ?>

    <div class="order-detect-left-right-settings">
        <div class="order-detect-settings">
            <div class="order-detect-settings-menu">
                <ul>
                    <?php
                    $i = 1;
                    foreach ($settings['tabs'] as $key => $setting) {
                        $active = $current_tab == $key ? 'active' : '';
                        echo '<li class="' . esc_attr($active) . '" data-tab="' . esc_attr($key) . '"><a href="#' . esc_attr($key) . '">' . esc_attr($setting['title']) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            <div class="order-detect-settings-content">
                <div class="order-detect-settings-form-wrapper">
                    <form method="post" id="order-detect-settings-form" action="options.php" novalidate="novalidate">
                        <input id="order_detect_builder_id" type="hidden" name="orderdetect_settings[builder_id]" value="<?php echo esc_attr($current_tab); ?>">
                        <?php settings_fields($this->_optionGroup); ?>
                        <?php
                        $i = 1;
                        foreach ($settings['tabs'] as $sec_id => $section) :
                            $active = $current_tab == $sec_id ? 'active' : '';

                            $child_sections = $section['sections'];
                        ?>
                            <div id="order-detect-<?php echo esc_attr($sec_id); ?>" class="order-detect-settings-tab <?php echo esc_attr($active); ?>">
                                <div id="order-detect-settings-general_settings" class="order-detect-settings-section order-detect-<?php echo esc_attr($sec_id); ?>">
                                    <?php
                                    foreach ($child_sections as $sec_id => $grand_child_section) :
                                        $fields = $grand_child_section['fields'];
                                    ?>
                                        <h2><?php echo esc_attr($grand_child_section['title']); ?></h2>
                                        <table>
                                            <tbody>
                                                <?php foreach ($fields as  $key => $value) : ?>
                                                    <?php $file_name = isset($value['type']) ? $value['type'] : 'text'; ?>
                                                    <?php $style = isset($value['style']) ? $value['style'] : ''; ?>
                                                    <tr data-id="<?php echo esc_attr($value['name']); ?>" id="order-detect-meta-<?php echo esc_attr($value['name']); ?>" class="order-detect-field order-detect-meta-<?php echo esc_attr($file_name); ?> type-<?php echo esc_attr($file_name); ?> <?php echo esc_attr($style); ?>">
                                                        <th class="order-detect-label">
                                                            <label for="<?php echo esc_attr($value['name']); ?>">
                                                                <?php echo esc_attr($value['label']); ?>
                                                            </label>
                                                        </th>
                                                        <td class="order-detect-control">
                                                            <div class="order-detect-control-wrapper">
                                                                <?php
                                                                if ($file_name) {
                                                                    include 'fields/' . $file_name . '.php';
                                                                }
                                                                $whitelisted_tag = [
                                                                    'strong' => []
                                                                ];
                                                                ?>
                                                                <?php if (isset($value['description'])  && !empty($value['description'])) { ?>
                                                                    <p class="order-detect-field-help"><?php echo sprintf(wp_kses(__('%s', 'sample'), $whitelisted_tag), $value['description']); ?></p>
                                                                <?php } ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php do_settings_fields($this->_optionGroup, 'default'); ?>
                        <?php do_settings_sections($this->_optionGroup, 'default'); ?>
                        <?php wp_nonce_field('orderdetect_options_verify', 'orderdetect_nonce'); ?>
                        <?php submit_button('Save', 'btn-settings order-detect-settings-button'); ?>
                    </form>
                </div>

                <div class="order-detect-settings-right">
                    <div class="order-detect-sidebar">
                        <div class="order-detect-sidebar-block">
                            <div class="order-detect-admin-sidebar-logo">
                                <img alt="OrderDetect" src="<?php echo ORDERDETECT_ASSETS; ?>/img/order-detect-banner.jpeg">
                            </div>
                            <div class="order-detect-admin-sidebar-cta">
                                <!-- <a rel="nofollow" href="#" target="_blank"><?php echo __('Upgrade to Pro', 'order-detect'); ?></a> -->
                            </div>
                        </div>
                        <div class="order-detect-sidebar-block order-detect-license-block">
                        </div>
                    </div>
                </div>
            </div>

            <?php do_action('orderdetect_settings_footer'); ?>
        </div>
    </div>
</div>