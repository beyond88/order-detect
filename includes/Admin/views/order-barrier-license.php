<?php
$setting_options = wp_parse_args(get_option('orderbarrier_license'));
$license_key = array_key_exists('key', $setting_options) ? $setting_options['key'] : '';
$license_expires = array_key_exists('expires', $setting_options) ? $setting_options['expires'] : '';
?>
<div class="order-barrier-settings-wrap">
    <?php do_action('order_barrier_settings_header'); ?>

    <div class="order-barrier-left-right-settings">
        <div class="order-barrier-settings">
            <div class="order-barrier-settings-content">
                <div class="order-barrier-settings-form-wrapper">
                    <form method="post" id="order-barrier-settings-form">
                        <div class="order-barrier-settings-tab active">
                            <div class="order-barrier-settings-section">
                                <table>
                                    <tbody>
                                        <tr data-id="license_key" id="order-barrier-meta-license_key" class="order-barrier-field order-barrier-meta-text type-text ">
                                            <th class="order-barrier-label"></th>
                                            <td class="order-barrier-control order-barrier-license-activate">
                                                <?php if (empty($license_key)) { ?>
                                                    <h2><?php echo __('You are nearly to go!', 'order-barrier'); ?></h2>
                                                    <p><?php echo __('Enter your license key here, to activate OrderBarrier, and get automatic updates and premium support.', 'order-barrier'); ?></p>
                                                    <div class="outer">
                                                        <h4><?php echo __('License Activation', 'order-barrier'); ?></h4>
                                                        <ul>
                                                            <li>
                                                                <?php printf(__('Log in to <a rel="nofollow" href="%s" target="_blank">your account</a> to get your license key.', 'order-barrier'), '#'); ?>
                                                            </li>
                                                            <li>
                                                                <?php printf(__('If you don\'t yet have a license key, get <a rel="nofollow" href="%s" target="_blank">OrderBarrier</a> now.', 'order-barrier'), '#'); ?>
                                                            </li>
                                                            <li>
                                                                <?php _e(__('Copy the license key from your account and paste it below.', 'order-barrier')); ?>
                                                            </li>
                                                            <li>
                                                                <?php _e(__('Click on <strong>"Activate License"</strong> button.', 'order-barrier')); ?>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php } ?>

                                                <?php if (!empty($license_expires)) {
                                                    $this->check_license_expiration_frontend($license_expires);
                                                } ?>
                                            </td>
                                        </tr>
                                        <tr data-id="license_key" id="order-barrier-meta-license_key" class="order-barrier-field order-barrier-meta-text type-text ">
                                            <th class="order-barrier-label">
                                                <label for="license_key">
                                                    <?php echo __('License Key', 'order-barrier'); ?>
                                                </label>
                                            </th>
                                            <td class="order-barrier-control">
                                                <div class="order-barrier-control-wrapper">
                                                    <input class="order-barrier-settings-field" id="orderbarrier_license_key" type="text" name="orderbarrier_license_key" value="<?php echo esc_attr($license_key); ?>" placeholder="<?php echo __('Enter your license key', 'order-barrier'); ?>" <?php if (!empty($license_key)) { ?> readonly <?php } ?>>
                                                    <p class="order-barrier-field-help"></p>

                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <p class="order-barrier-license-status"></p>

                        <?php wp_nonce_field('order_barrier_nonce', 'order_barrier_nonce_field'); ?>
                        <p class="submit">
                            <?php echo $this->license_button(); ?>
                        </p>
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