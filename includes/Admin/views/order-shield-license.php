<?php
settings_errors();
$setting_options = wp_parse_args(get_option($this->main->_optionName), $this->main->_defaultOptions);
$license_key = $setting_options['license_key'];

?>
<div class="order-shield-settings-wrap">
    <?php do_action('order_shield_settings_header'); ?>

    <div class="order-shield-left-right-settings">
        <div class="order-shield-settings">
            <div class="order-shield-settings-content">
                <div class="order-shield-settings-form-wrapper">
                    <form method="post" id="order-shield-settings-form">
                        <div class="order-shield-settings-tab active">
                            <div class="order-shield-settings-section">
                                <table>
                                    <tbody>
                                        <tr data-id="license_key" id="order-shield-meta-license_key" class="order-shield-field order-shield-meta-text type-text ">
                                            <th class="order-shield-label">
                                                <label for="license_key">
                                                    <?php echo __('License Key', 'order-shield'); ?>
                                                </label>
                                            </th>
                                            <td class="order-shield-control">
                                                <div class="order-shield-control-wrapper">
                                                    <input class="order-shield-settings-field" id="ordershield_license_key" type="text" name="ordershield_license_key" value="<?php echo esc_attr($license_key); ?>" placeholder="<?php echo __('Enter your license key', 'order-shield'); ?>">
                                                    <p class="order-shield-field-help"></p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <?php wp_nonce_field('order_shield_nonce', 'order_shield_nonce_field'); ?>
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