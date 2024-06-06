<?php
$setting_options = wp_parse_args(get_option('orderdetect_license'));
$license_key = array_key_exists('key', $setting_options) ? $setting_options['key'] : '';
$license_expires = array_key_exists('expires', $setting_options) ? $setting_options['expires'] : '';
?>
<div class="order-detect-settings-wrap">
    <?php do_action('order_detect_settings_header'); ?>

    <div class="order-detect-left-right-settings">
        <div class="order-detect-settings">
            <div class="order-detect-settings-content">
                <div class="order-detect-settings-form-wrapper">
                    <form method="post" id="order-detect-settings-form">
                        <div class="order-detect-settings-tab active">
                            <div class="order-detect-settings-section">
                                <table>
                                    <tbody>
                                        <tr data-id="license_key" id="order-detect-meta-license_key" class="order-detect-field order-detect-meta-text type-text ">
                                            <th class="order-detect-label"></th>
                                            <td class="order-detect-control order-detect-license-activate">
                                                <?php if (empty($license_key)) { ?>
                                                    <h2><?php echo __('You are nearly to go!', 'order-detect'); ?></h2>
                                                    <p><?php echo __('Enter your license key here, to activate OrderDetect, and get automatic updates and premium support.', 'order-detect'); ?></p>
                                                    <div class="outer">
                                                        <h4><?php echo __('License Activation', 'order-detect'); ?></h4>
                                                        <ul>
                                                            <li>
                                                                <?php printf(__('Log in to <a rel="nofollow" href="%s" target="_blank">your account</a> to get your license key.', 'order-detect'), '#'); ?>
                                                            </li>
                                                            <li>
                                                                <?php printf(__('If you don\'t yet have a license key, get <a rel="nofollow" href="%s" target="_blank">OrderDetect</a> now.', 'order-detect'), '#'); ?>
                                                            </li>
                                                            <li>
                                                                <?php _e(__('Copy the license key from your account and paste it below.', 'order-detect')); ?>
                                                            </li>
                                                            <li>
                                                                <?php _e(__('Click on <strong>"Activate License"</strong> button.', 'order-detect')); ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php } ?>

                                                <?php if (!empty($license_expires)) {
                                                    $this->check_license_expiration_frontend($license_expires);
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr data-id="license_key" id="order-detect-meta-license_key" class="order-detect-field order-detect-meta-text type-text ">
                                            <th class="order-detect-label">
                                                <label for="license_key">
                                                    <?php echo __('License Key', 'order-detect'); ?>
                                                </label>
                                            </th>
                                            <td class="order-detect-control">
                                                <div class="order-detect-control-wrapper">
                                                    <input class="order-detect-settings-field" id="orderdetect_license_key" type="text" name="orderdetect_license_key" value="<?php echo esc_attr($license_key); ?>" placeholder="<?php echo __('Enter your license key', 'order-detect'); ?>" <?php if (!empty($license_key)) { ?> readonly <?php } ?>>
                                                    <p class="order-detect-field-help"></p>

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <p class="order-detect-license-status"></p>

                        <?php wp_nonce_field('order_detect_nonce', 'order_detect_nonce_field'); ?>
                        <p class="submit">
                            <?php echo $this->license_button(); ?>
                        </p>
                    </form>
                </div>

                <div class="order-detect-settings-right">
                    <div class="order-detect-sidebar">
                        <div class="order-detect-sidebar-block">
                            <div class="order-detect-admin-sidebar-logo">
                                <!-- <img alt="OrderDetect" src="<?php echo ORDERDETECT_ASSETS; ?>/img/order-detect-banner.png"> -->
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