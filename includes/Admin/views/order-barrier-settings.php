<?php
settings_errors();
$setting_options = wp_parse_args(get_option($this->_optionName), $this->_defaultOptions);

if (!isset($setting_options['builder_id'])) {
    $current_tab = 'general_settings';
} else {
    $current_tab = $setting_options['builder_id'];
}
?>
<div class="order-barrier-settings-wrap">
    <?php do_action('order_barrier_settings_header'); ?>

    <div class="order-barrier-left-right-settings">
        <div class="order-barrier-settings">
            <div class="order-barrier-settings-menu">
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
            <div class="order-barrier-settings-content">
                <div class="order-barrier-settings-form-wrapper">
                    <form method="post" id="order-barrier-settings-form" action="options.php" novalidate="novalidate">
                        <input id="order_barrier_builder_id" type="hidden" name="orderbarrier_settings[builder_id]" value="<?php echo esc_attr($current_tab); ?>">
                        <?php settings_fields($this->_optionGroup); ?>
                        <?php
                        $i = 1;
                        foreach ($settings['tabs'] as $sec_id => $section) :
                            $active = $current_tab == $sec_id ? 'active' : '';

                            $child_sections = $section['sections'];
                        ?>
                            <div id="order-barrier-<?php echo esc_attr($sec_id); ?>" class="order-barrier-settings-tab <?php echo esc_attr($active); ?>">
                                <div id="order-barrier-settings-general_settings" class="order-barrier-settings-section order-barrier-<?php echo esc_attr($sec_id); ?>">
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
                                                    <tr data-id="<?php echo esc_attr($value['name']); ?>" id="order-barrier-meta-<?php echo esc_attr($value['name']); ?>" class="order-barrier-field order-barrier-meta-<?php echo esc_attr($file_name); ?> type-<?php echo esc_attr($file_name); ?> <?php echo esc_attr($style); ?>">
                                                        <th class="order-barrier-label">
                                                            <label for="<?php echo esc_attr($value['name']); ?>">
                                                                <?php echo esc_attr($value['label']); ?>
                                                            </label>
                                                        </th>
                                                        <td class="order-barrier-control">
                                                            <div class="order-barrier-control-wrapper">
                                                                <?php
                                                                if ($file_name) {
                                                                    include 'fields/' . $file_name . '.php';
                                                                }
                                                                $whitelisted_tag = [
                                                                    'strong' => []
                                                                ];
                                                                ?>
                                                                <?php if (isset($value['description'])  && !empty($value['description'])) { ?>
                                                                    <p class="order-barrier-field-help"><?php echo sprintf(wp_kses(__('%s', 'sample'), $whitelisted_tag), $value['description']); ?></p>
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
                        <?php wp_nonce_field('orderbarrier_options_verify', 'orderbarrier_nonce'); ?>
                        <?php submit_button('Save', 'btn-settings order-barrier-settings-button'); ?>
                    </form>
                </div>

                <div class="order-barrier-settings-right">
                    <div class="order-barrier-sidebar">
                        <div class="order-barrier-sidebar-block">
                            <div class="order-barrier-admin-sidebar-logo">
                                <img alt="OrderBarrier" src="<?php echo ORDERBARRIER_ASSETS; ?>/img/order-barrier-banner.jpeg">
                            </div>
                            <div class="order-barrier-admin-sidebar-cta">
                                <!-- <a rel="nofollow" href="#" target="_blank"><?php echo __('Upgrade to Pro', 'order-barrier'); ?></a> -->
                            </div>
                        </div>
                        <div class="order-barrier-sidebar-block order-barrier-license-block">
                        </div>
                    </div>
                </div>
            </div>

            <?php do_action('orderbarrier_settings_footer'); ?>
        </div>
    </div>
</div>