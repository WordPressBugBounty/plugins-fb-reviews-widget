<?php

namespace WP_TrustReviews\Includes;

class Plugin_Support {

    public function __construct() {
    }

    public function register() {
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG . '-support', array($this, 'init'));
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG . '-support', array($this, 'render'));
    }

    public function init() {

    }

    public function render() {

        $tab = isset($_GET[Plugin::SLG . '_tab']) && strlen($_GET[Plugin::SLG . '_tab']) > 0 ? sanitize_text_field(wp_unslash($_GET[Plugin::SLG . '_tab'])) : 'welcome';

        ob_start();

        ?>
        <div class="{slg}-page-title">
            Support and Troubleshooting
        </div>

        <div class="{slg}-support-workspace">

            <div data-nav-tabs="">

                <div class="nav-tab-wrapper">
                    <a href="#welcome" class="nav-tab<?php if ($tab == 'welcome') { ?> nav-tab-active<?php } ?>">
                        <?php echo __('Welcome', Plugin::NAME); ?>
                    </a>
                    <a href="#fig" class="nav-tab<?php if ($tab == 'fig') { ?> nav-tab-active<?php } ?>">
                        <?php echo __('Full Installation Guide', Plugin::NAME); ?>
                    </a>
                    <a href="#support" class="nav-tab<?php if ($tab == 'support') { ?> nav-tab-active<?php } ?>">
                        <?php echo __('Support', Plugin::NAME); ?>
                    </a>
                </div>

                <div id="welcome" class="tab-content" style="display:<?php echo $tab == 'welcome' ? 'block' : 'none'?>;">
                    <h3>Trust Reviews Widget for WordPress</h3>
                    <div class="{slg}-flex-row">
                        <div class="{slg}-flex-col">
                            <span>Trust Reviews plugin is an easy and fast way to integrate Google, Facebook and Yelp reviews right into your WordPress website. This plugin works instantly and keep all Google places and reviews in WordPress database thus it has no depend on external services.</span>
                            <p style="font-size:20px;text-align:center"><b><u>Please read '<a href="<?php echo admin_url('admin.php?page=' . Plugin::SLG . '-support&' . Plugin::SLG . '_tab=fig'); ?>">Full Installation Guide</a>' to completely understand how it works and set up the plugin</u></b>.</p>
                            <p>Also you can find most common answers and solutions for most common questions and issues in next tabs.</p>
                            <div class="{slg}-alert {slg}-alert-success">
                                <strong>Try more features in the Business version</strong>: Merge Google, Facebook and Yelp reviews, Beautiful themes (Slider, Grid, Trust Badges), Shortcode support, Rich Snippets, Rating filter, Any sorting, Include/Exclude words filter, Hide/Show any elements, Priority support and many others.
                                <a class="button-primary button" href="https://trust.reviews" target="_blank" style="margin-left:10px">Upgrade to Business</a>
                            </div>
                            <br>
                            <div class="{slg}-socials">
                                <script src="https://apis.google.com/js/platform.js"></script>
                                <div class="g-ytsubscribe" data-channelid="UCfTAPWvWJkGRVhZ1AN5DtvA" data-layout="default" data-count="default"></div>

                                <div id="fb-root" style="display:inline-block"></div>
                                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0&appId=276175796521349&autoLogAppEvents=1" nonce="MYsLkxjF"></script>
                                <div class="fb-like" data-href="https://trust.reviews/" data-layout="button_count" data-action="like" data-show-faces="true" data-share="false"></div>

                                <a href="https://twitter.com/trust.reviews?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false">Follow @trust.reviews</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                            </div>
                        </div>
                        <div class="{slg}-flex-col">
                            <iframe width="100%" height="315" src="https://www.youtube.com/embed/11JAfRta1iA" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                <div id="fig" class="tab-content" style="display:<?php echo $tab == 'fig' ? 'block' : 'none'?>;">
                    <h3>How to connect Google reviews</h3>
                    <?php include_once(dirname(TRUSTREVIEWS_PLUGIN_FILE) . '/includes/page-setting-fig.php'); ?>
                </div>

                <div id="support" class="tab-content" style="display:<?php echo $tab == 'support' ? 'block' : 'none'?>;">
                    <h3>Most Common Questions</h3>
                    <?php include_once(dirname(TRUSTREVIEWS_PLUGIN_FILE) . '/includes/page-setting-support.php'); ?>
                </div>

            </div>
        </div>
        <?php

        echo preg_replace('/{slg}/', Plugin::SLG, ob_get_clean());
    }
}
