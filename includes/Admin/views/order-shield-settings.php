<?php
settings_errors();
$setting_options = wp_parse_args(get_option($this->_optionName), $this->_defaultOptions);

if (!isset($setting_options['builder_id'])) {
    $current_tab = 'general_settings';
} else {
    $current_tab = $setting_options['builder_id'];
}
?>
<div class="order-shield-settings-wrap">
    <?php do_action('order_shield_settings_header'); ?>

    <div class="order-shield-left-right-settings">
        <div class="order-shield-settings">
            <div class="order-shield-settings-menu">
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
            <div class="order-shield-settings-content">
                <div class="order-shield-settings-form-wrapper">
                    <form method="post" id="order-shield-settings-form" action="options.php" novalidate="novalidate">
                        <input id="samply_builder_id" type="hidden" name="samply_settings[builder_id]" value="<?php echo esc_attr($current_tab); ?>">
                        <?php settings_fields($this->_optionGroup); ?>
                        <?php
                        $i = 1;
                        foreach ($settings['tabs'] as $sec_id => $section) :
                            $active = $current_tab == $sec_id ? 'active' : '';

                            $child_sections = $section['sections'];
                        ?>
                            <div id="order-shield-<?php echo esc_attr($sec_id); ?>" class="order-shield-settings-tab <?php echo esc_attr($active); ?>">
                                <div id="order-shield-settings-general_settings" class="order-shield-settings-section order-shield-<?php echo esc_attr($sec_id); ?>">
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
                                                    <tr data-id="<?php echo esc_attr($value['name']); ?>" id="order-shield-meta-<?php echo esc_attr($value['name']); ?>" class="order-shield-field order-shield-meta-<?php echo esc_attr($file_name); ?> type-<?php echo esc_attr($file_name); ?> <?php echo esc_attr($style); ?>">
                                                        <th class="order-shield-label">
                                                            <label for="<?php echo esc_attr($value['name']); ?>">
                                                                <?php echo esc_attr($value['label']); ?>
                                                            </label>
                                                        </th>
                                                        <td class="order-shield-control">
                                                            <div class="order-shield-control-wrapper">
                                                                <?php
                                                                if ($file_name) {
                                                                    include 'fields/' . $file_name . '.php';
                                                                }
                                                                $whitelisted_tag = [
                                                                    'strong' => []
                                                                ];
                                                                ?>
                                                                <?php if (isset($value['description'])  && !empty($value['description'])) { ?>
                                                                    <p class="order-shield-field-help"><?php echo sprintf(wp_kses(__('%s', 'sample'), $whitelisted_tag), $value['description']); ?></p>
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
                        <?php submit_button('Save', 'btn-settings order-shield-settings-button'); ?>
                    </form>
                </div>

                <div class="order-shield-settings-right">
                    <div class="order-shield-sidebar">
                        <div class="order-shield-sidebar-block">
                            <div class="order-shield-admin-sidebar-logo">
                                <img alt="OrderShield" src="<?php echo ORDERSHIELD_ASSETS; ?>/img/order-shield-logo.svg">
                            </div>
                            <div class="order-shield-admin-sidebar-cta">
                                <!-- <a rel="nofollow" href="#" target="_blank"><?php echo __('Upgrade to Pro', 'order-shield'); ?></a> -->
                            </div>
                        </div>
                        <div class="order-shield-sidebar-block order-shield-license-block">
                        </div>
                    </div>
                </div>
            </div>

            <?php do_action('ordershield_settings_footer'); ?>
        </div>
    </div>
</div>