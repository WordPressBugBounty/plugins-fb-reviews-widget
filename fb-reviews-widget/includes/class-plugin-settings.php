<?php

namespace WP_TrustReviews\Includes;

class Plugin_Settings {

    private $debug_info;

    public function __construct(Debug_Info $debug_info) {
        $this->debug_info = $debug_info;
    }

    public function register() {
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG . '-settings', array($this, 'init'));
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG . '-settings', array($this, 'render'));
    }

    public function init() {

    }

    public function render() {

        $tab = isset($_GET[Plugin::SLG . '_tab']) && strlen($_GET[Plugin::SLG . '_tab']) > 0 ? sanitize_text_field(wp_unslash($_GET[Plugin::SLG . '_tab'])) : 'active';

        $enabled         = get_option(Plugin::SLG . '_active') == '1';
        $demand_assets   = get_option(Plugin::SLG . '_demand_assets');
        $minified_assets = get_option(Plugin::SLG . '_minified_assets');
        $google_api_key  = get_option(Plugin::SLG . '_google_api_key');
        $yelp_api_key    = get_option(Plugin::SLG . '_yelp_api_key');
        $activation_time = get_option(Plugin::SLG . '_activation_time');
        $debug_mode      = get_option(Plugin::SLG . '_debug_mode') == '1';

        $revupd_cron     = get_option(Plugin::SLG . '_revupd_cron') == '1';

        echo '<div class="' . Plugin::SLG . '-page-title">Settings</div>';

        do_action(Plugin::SLG . '_admin_notices');

        ob_start();
        ?>

        <div class="{slg}-settings-workspace">

            <div data-nav-tabs="">

                <div class="nav-tab-wrapper">
                    <a href="#{slg}-general"  class="nav-tab<?php if ($tab == 'active')   { ?> nav-tab-active<?php } ?>">General</a>
                    <a href="#{slg}-google"   class="nav-tab<?php if ($tab == 'google')   { ?> nav-tab-active<?php } ?>">Google</a>
                    <a href="#{slg}-yelp"     class="nav-tab<?php if ($tab == 'yelp')     { ?> nav-tab-active<?php } ?>">Yelp</a>
                    <a href="#{slg}-advance"  class="nav-tab<?php if ($tab == 'advance')  { ?> nav-tab-active<?php } ?>">Advance</a>
                </div>

                <div id="{slg}-general" class="tab-content" style="display:<?php echo $tab == 'active' ? 'block' : 'none'?>;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=' . Plugin::SLG . '_settings_save&' . Plugin::SLG . '_tab=active&active=' . (string)((int)($enabled != true)))); ?>">
                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Trust Reviews plugin is currently <b><?php echo $enabled ? 'enabled' : 'disabled' ?></b></label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_active', Plugin::SLG . '-form_nonce_active'); ?>
                                <input type="submit" name="active" class="button" value="<?php echo $enabled ? 'Disable' : 'Enable'; ?>" />
                            </div>
                        </div>
                    </form>
                </div>

                <div id="{slg}-google" class="tab-content" style="display:<?php echo $tab == 'google' ? 'block' : 'none'?>;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=' . Plugin::SLG . '_settings_save&' . Plugin::SLG . '_tab=google')); ?>">
                        <?php wp_nonce_field(Plugin::SLG . '-wpnonce_save', Plugin::SLG . '-form_nonce_save'); ?>
                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Google Places API key</label>
                            </div>
                            <div class="wp-review-field-option">
                                <input type="text" id="google_api_key" name="google_api_key" class="regular-text" value="<?php echo esc_attr($google_api_key); ?>">
                                <?php if (!$google_api_key && time() - $activation_time > 60 * 60 * 48) { ?>
                                <div class="{slg}-warn">Your Google API key is not set for this reason, reviews are not automatically updated daily.<br>Please create your own Google API key and save here.</div>
                                <?php } ?>
                                <p>API key is mandatory to make the reviews automatically updated.</p>
                                <p>If you do not know how to create it, please read: <a href="<?php echo admin_url('admin.php?page=' . Plugin::SLG . '-support&' . Plugin::SLG . '_tab=fig'); ?>" target="_blank">Full Installation Guide</a></p>
                                <div style="padding-top:15px">
                                    <input type="submit" value="Save" name="save" class="button" />
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="{slg}-yelp" class="tab-content" style="display:<?php echo $tab == 'yelp' ? 'block' : 'none'?>;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=' . Plugin::SLG . '_settings_save&' . Plugin::SLG . '_tab=yelp')); ?>">
                        <?php wp_nonce_field(Plugin::SLG . '-wpnonce_save', Plugin::SLG . '-form_nonce_save'); ?>
                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Yelp API key</label>
                            </div>
                            <div class="wp-review-field-option">
                                <input type="text" id="yelp_api_key" name="yelp_api_key" class="regular-text" value="<?php echo esc_attr($yelp_api_key); ?>">
                                <?php if (!$yelp_api_key && time() - $activation_time > 60 * 60 * 48) { ?>
                                <div class="{slg}-warn">Your Yelp API key is not set for this reason, reviews are not automatically updated daily.<br>Please create your own Yelp API key and save here.</div>
                                <?php } ?>
                                <p>API key is mandatory to make the reviews automatically updated.</p>
                                <div style="padding-top:15px">
                                    <input type="submit" value="Save" name="save" class="button" />
                                </div>
                            </div>
                        </div>
                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Instruction: how to create Yelp API key</label>
                            </div>
                            <div class="wp-review-field-option">
                                <p>1. If you do not have a <b>free Yelp account (<u>not a business</u>)</b>, please <a href="https://www.yelp.com/signup" target="_blank">Sign Up Here</a></p>
                                <p>2. Under the free Yelp account, go to the <a href="https://www.yelp.com/developers/v3/manage_app" target="_blank">Yelp developers</a> page and create new app</p>
                                <p>3. Copy <b>API Key</b> to this setting and <b>Save</b></p>
                                <h3>Video instruction</h3>
                                <iframe src="//www.youtube.com/embed/GFhGN36Wf7Q?rel=0" allowfullscreen=""></iframe>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="{slg}-advance" class="tab-content" style="display:<?php echo $tab == 'advance' ? 'block' : 'none'?>;">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=' . Plugin::SLG . '_settings_save&' . Plugin::SLG . '_tab=advance')); ?>">

                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Reviews update daily schedule is <b><?php echo $revupd_cron ? 'enabled' : 'disabled' ?></b></label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_revupd_cron', Plugin::SLG . '-form_nonce_revupd_cron'); ?>
                                <input type="submit" value="<?php echo $revupd_cron ? 'Disable' : 'Enable'; ?>" name="revupd_cron" class="button" />
                            </div>
                        </div>

                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Re-create the database tables of the plugin (service option)</label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_create_db', Plugin::SLG . '-form_nonce_create_db'); ?>
                                <input type="submit" value="Re-create Database" name="create_db" onclick="return confirm('Are you sure you want to re-create database tables?')" class="button" />
                            </div>
                        </div>
                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label><b>Please be careful</b>: this removes all settings, reviews, feeds and install the plugin from scratch</label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_create_db', Plugin::SLG . '-form_nonce_create_db'); ?>
                                <input type="submit" value="Install from scratch" name="install" onclick="return confirm('It will delete all current feeds, are you sure you want to install from scratch the plugin?')" class="button" />
                                <p><label><input type="checkbox" id="install_multisite" name="install_multisite"> For all sites (WP Multisite)</label></p>
                            </div>
                        </div>
                        <div class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label><b>Please be careful</b>: this removes all plugin-specific settings, reviews and feeds</label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_reset_all', Plugin::SLG . '-form_nonce_reset_all'); ?>
                                <input type="submit" value="Delete All Data" name="reset_all" onclick="return confirm('Are you sure you want to reset all plugin data including feeds?')" class="button" />
                                <p><label><input type="checkbox" id="reset_all_multisite" name="reset_all_multisite"> For all sites (WP Multisite)</label></p>
                            </div>
                        </div>
                        <div id="debug_info" class="{slg}-field">
                            <div class="{slg}-field-label">
                                <label>Debug information</label>
                            </div>
                            <div class="wp-review-field-option">
                                <input type="button" value="Copy Debug Information" name="reset_all" onclick="window.{slg}_debug_info.select();document.execCommand('copy');window.{slg}_debug_msg.innerHTML='Debug Information copied, please paste it to your email to support';" class="button" />
                                <textarea id="{slg}_debug_info" style="display:block;width:30em;height:250px;margin-top:10px" onclick="window.{slg}_debug_info.select();document.execCommand('copy');window.{slg}_debug_msg.innerHTML='Debug Information copied, please paste it to your email to support';" readonly><?php $this->debug_info->render(); ?></textarea>
                                <p id="{slg}_debug_msg"></p>
                            </div>
                        </div>
                        <div class="{slg}-field" style="display:none">
                            <div class="{slg}-field-label">
                                <label>Debug mode is currently <b><?php echo $debug_mode ? 'enabled' : 'disabled' ?></b></label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_debug_mode', Plugin::SLG . '-form_nonce_debug_mode'); ?>
                                <input type="submit" name="debug_mode" class="button" value="<?php echo $debug_mode ? 'Disable' : 'Enable'; ?>" />
                            </div>
                        </div>
                        <div class="{slg}-field" style="display:none">
                            <div class="{slg}-field-label">
                                <label>Execute db update manually</label>
                            </div>
                            <div class="wp-review-field-option">
                                <?php wp_nonce_field(Plugin::SLG . '-wpnonce_update_db', Plugin::SLG . '-form_nonce_update_db'); ?>
                                <input type="submit" name="update_db" class="button" />
                                <input type="text" name="update_db_ver" style="width:94px;height:22px" placeholder="version" />
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <?php
        echo preg_replace('/{slg}/', Plugin::SLG, ob_get_clean());
    }

}
