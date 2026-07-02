<?php

namespace WP_TrustReviews\Includes;

class View {

    private $view_svg;

    public function __construct(View_Svg $view_svg) {
        $this->view_svg = $view_svg;
    }

    public function render($feed_id, $businesses, $reviews, $options, $is_admin = false) {
        ob_start();

        $max_width = $options->max_width;
        if (is_numeric($max_width)) {
            $max_width = $max_width . 'px';
        }
        $max_height = $options->max_height;
        if (is_numeric($max_height)) {
            $max_height = $max_height . 'px';
        }

        $style = '';
        if (!empty($max_width)) {
            $style .= 'width:' . $max_width . '!important;';
        }
        if (!empty($max_height)) {
            $style .= 'height:' . $max_height . '!important;overflow-y:auto!important;';
        }
        if ($options->centered) {
            $style .= 'margin:0 auto!important;';
        }

        ?><div class="{slg}<?php if ($options->dark_theme) { ?> wp-dark<?php } ?>"<?php if ($style) { ?> style="<?php echo esc_attr($style);?>"<?php } ?> data-id="<?php echo esc_attr($feed_id); ?>" data-layout="<?php echo esc_attr($options->view_mode); ?>" data-exec="false"><?php
            switch ($options->view_mode) {
                case 'slider':
                    $this->render_slider($businesses, $reviews, $options, $is_admin);
                    break;
                case 'grid':
                    $this->render_grid($businesses, $reviews, $options, $is_admin);
                    break;
                default:
                    $this->render_list($businesses, $reviews, $options, $is_admin);
            }
            $this->view_svg->render();
        ?></div><?php
        return preg_replace('/{slg}/', Plugin::SLG, preg_replace('/[\n\r]|(>)\s+(<)/', '$1$2', ob_get_clean()));
    }

    public function render_svg() {
        $this->view_svg->render();
    }

    private function render_slider($businesses, $reviews, $options, $is_admin = false) {
        ?>
        <div class="{slg}-row {slg}-row-m" data-options='<?php
            echo json_encode(
                array(
                    'speed'    => $options->slider_speed ? $options->slider_speed : 5,
                    'autoplay' => $options->slider_autoplay
                )
            ); ?>'>
            <?php if (count($businesses) > 0) { ?>
            <div class="{slg}-header">
                <div class="{slg}-header-inner">
                    <div class="{slg}-flex<?php if ($options->header_center) { ?> {slg}-place-center<?php } ?>" style="--gap:12px;--dir:row;">
                    <?php $this->place(
                        $businesses[0]->rating,
                        $businesses[0],
                        $businesses[0]->photo,
                        $reviews,
                        $options,
                        true,
                        true
                    ); ?>
                    </div>
                </div>
            </div>
            <?php }
            if (count($reviews) > 0) { ?>
            <div class="{slg}-content">
                <div class="{slg}-content-inner">
                    <div class="{slg}-reviews">
                        <?php foreach ($reviews as $review) { $this->slider_review($review, false, $options, $is_admin); } ?>
                    </div>
                    <?php if (!$options->slider_hide_prevnext) { ?>
                    <div class="{slg}-controls">
                        <div class="{slg}-btns {slg}-prev">
                            <svg viewBox="0 0 24 24"><path d="M14.6,18.4L8.3,12l6.4-6.4l0.7,0.7L9.7,12l5.6,5.6L14.6,18.4z"></path></svg>
                        </div>
                        <div class="{slg}-btns {slg}-next">
                            <svg viewBox="0 0 24 24"><path d="M9.4,18.4l-0.7-0.7l5.6-5.6L8.6,6.4l0.7-0.7l6.4,6.4L9.4,18.4z"></path></svg>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php if (!$options->slider_hide_dots) { ?><div class="{slg}-dots"></div><?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php $this->js_loader('TrustReviews', 'Plugin.init', '\'slider\'');
    }

    private function render_grid($businesses, $reviews, $options, $is_admin = false) {
        if (count($businesses) > 0) { ?>
        <div class="{slg}-header">
            <div class="{slg}-header-inner">
                <div class="{slg}-flex<?php if ($options->header_center) { ?> {slg}-place-center<?php } ?>" style="--gap:12px;--dir:row;">
                <?php $this->place(
                    $businesses[0]->rating,
                    $businesses[0],
                    $businesses[0]->photo,
                    $reviews,
                    $options,
                    true,
                    true
                ); ?>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="{slg}-row {slg}-row-m" data-options='<?php
            echo json_encode(
                array(
                    'speed'    => $options->slider_speed ? $options->slider_speed : 5,
                    'autoplay' => $options->slider_autoplay
                )
            ); ?>'>
            <?php if (count($reviews) > 0) { ?>
            <div class="{slg}-content">
                <div class="{slg}-content-inner">
                    <div class="{slg}-reviews">
                        <?php
                        $hr = false;
                        if (count($reviews) > 0) {
                            $i = 0;
                            foreach ($reviews as $review) {
                                if ($options->pagination > 0 && $options->pagination <= $i++) {
                                    $hr = true;
                                }
                                $this->slider_review($review, $hr, $options, $is_admin);
                            }
                        }
                        ?>
                    </div>
                    <?php if ($options->pagination > 0 && $hr) { ?>
                    <a class="{slg}-url" href="#" onclick="return TrustReviews.Plugin.next.call(this, '{slg}', <?php echo (int) $options->pagination; ?>);">
                        <?php echo __('More Reviews', Plugin::NAME); ?>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </div>
        <?php $this->js_loader('TrustReviews', 'Plugin.init', '\'grid\'');
    }

    private function render_list($businesses, $reviews, $options, $is_admin = false) {
        ?>
        <div class="{slg}-list">
            <div class="{slg}-flex" style="--dir:row;--align:center;--gap:12px;--wrap:wrap">
                <?php foreach ($businesses as $business) { ?>
                <div class="{slg}-place<?php if ($options->header_center) { ?> {slg}-place-center<?php } ?> {slg}-flex" style="--dir:row;--align:star;--gap:12px">
                <?php $this->place(
                    $business->rating,
                    $business,
                    $business->photo,
                    $reviews,
                    $options
                ); ?>
                </div>
                <?php } ?>
            </div>
            <?php if (!$options->hide_reviews) { ?>
            <div class="{slg}-content-inner">
                <?php $this->place_reviews($reviews, $options, $is_admin); ?>
            </div>
            <?php } ?>
        </div>
        <?php $this->js_loader('TrustReviews', 'Plugin.init');
    }

    function place($rating, $place, $place_img, $reviews, $options, $show_powered = true, $show_writereview = false) {
        ?>
        <?php if (!$options->header_hide_photo) { ?>
        <img src="<?php echo esc_url($place_img); ?>" class="{slg}-img" alt="<?php echo esc_attr($place->name); ?>" width="50" height="50" title="<?php echo esc_attr($place->name); ?>">
        <?php } ?>
        <div class="{slg}-flex" data-platform="<?php echo esc_attr($place->provider); ?>" style="--dir:column;--gap:8px">
            <?php if (!$options->header_hide_name) { ?>
            <div class="{slg}-name">
                <?php $place_name_content = '<span>' . $place->name . '</span>';
                echo $this->anchor($place->url, '', $place_name_content, $options->open_link, $options->nofollow_link); ?>
            </div>
            <?php } ?>

            <?php $this->place_rating($rating, $place->review_count, $place->provider, $options->hide_based_on); ?>

            <?php if ($show_powered) { $this->powered($place, $options); } ?>

            <?php if (!$options->hide_writereview) { ?>
            <div class="{slg}-wr">
                <a href="<?php echo $this->writereview_url($place); ?>" onclick="return TrustReviews.Plugin.leave_popup.call(this)">
                    <?php echo __('review us on', Plugin::NAME); $this->social_logo($place->provider); ?>
                </a>
            </div>
            <?php } ?>
        </div>
        <?php
    }

    function place_rating($rating, $review_count, $provider, $hide_based_on) {
        $this->stars($rating, $provider, true);
        if (!$hide_based_on && isset($review_count)) {
        ?><div class="{slg}-powered"><?php echo vsprintf(__('Based on %s reviews', Plugin::NAME), $this->array($review_count)); ?></div><?php
        }
    }

    function place_reviews($reviews, $options, $is_admin = false) {
        $pid = null;
        $place_url = null;
        $hr = false;
        if (is_array($reviews) && count($reviews) > 0) {
            $i = 0;
            foreach ($reviews as $review) {
                if (!$pid) {
                    $pid = $review->biz_id;
                    $place_url = $review->biz_url;
                }
                if ($options->pagination > 0 && $options->pagination <= $i++) {
                    $hr = true;
                }
                $this->place_review($review, $hr, $options, $is_admin);
            }
        }
        if ($options->pagination > 0 && $hr) { ?>
        <a class="{slg}-url" href="#" onclick="return TrustReviews.Plugin.next.call(this, '{slg}', <?php echo (int) $options->pagination; ?>);">
            <?php echo __('More Reviews', Plugin::NAME); ?>
        </a>
        <?php
        } else {
            $reviews_link = $options->google_def_rev_link ? $place_url : 'https://search.google.com/local/reviews?placeid=' . $pid;
            $this->anchor($reviews_link, '{slg}-url', __('See All Reviews', Plugin::NAME), $options->open_link, $options->nofollow_link);
        }
    }

    function place_review($review, $hr, $options, $is_admin = false) {
        $addcls = $is_admin && $review->hide != '' ? " wp-review-hidden" : "";
        ?><div class="{slg}-list-review<?php echo $addcls; ?><?php if ($hr) { echo ' {slg}-hide'; } ?>" data-rev="<?php echo esc_attr($review->provider); ?>">
            <div class="{slg}-flex" style="--dir:row;--align:star;--gap:12px"><?php
                if (!$options->hide_avatar) {
                    $default_avatar = Plugin::ASSETS_URL() . 'img/guest.png';
                    $author_img = empty($review->author_img) ? $default_avatar : $review->author_img;
                    if (isset($options->reviewer_avatar_size)) {
                        $author_img = str_replace('s128', 's' . $options->reviewer_avatar_size, $author_img);
                        $default_avatar = str_replace('s128', 's' . $options->reviewer_avatar_size, $default_avatar);
                    }
                    $this->image($author_img, '{slg}-img', $review->author_name, $options->lazy_load_img, $default_avatar);
                }
                ?><div class="{slg}-flex" style="--dir:column;--align:star;--gap:4px">
                    <?php
                    if (empty($review->author_url)) {
                        $author_name = empty($review->author_name) ? __('Google User', Plugin::NAME) : $review->author_name;
                        ?><div class="{slg}-name"><?php echo esc_html($author_name); ?></div><?php

                    } else {
                        $this->anchor($review->author_url, '{slg}-name', $review->author_name, $options->open_link, $options->nofollow_link);
                    }
                    ?>
                    <div class="{slg}-time" data-time="<?php echo $review->time; ?>"><?php echo esc_html(gmdate("H:i d M y", (int)$review->time)); ?></div>
                    <div class="{slg}-feedback">
                        <?php echo $this->stars($review->rating, $review->provider); ?>
                        <span class="{slg}-text"><?php echo $this->trim_text($review->text, $options->text_size); ?></span>
                    </div>
                    <?php if ($is_admin && !empty($review->id)) {
                        echo '<a href="#" class="wp-review-hide" data-id=' . $review->id . '>' . ($review->hide == '' ? 'Hide' : 'Show') . ' review</a>';
                    }
                ?></div>
            </div>
        </div><?php
    }

    function slider_review($review, $hr, $options, $is_admin = false) {
        $addcls = $options->hide_backgnd ? "" : " {slg}-backgnd";
        $addcls .= $options->show_round ? " {slg}-round" : "";
        $addcls .= $options->show_shadow ? " {slg}-shadow" : "";
        $addcls .= $is_admin && $review->hide != '' ? " wp-review-hidden" : "";
        ?><div class="{slg}-review<?php if ($hr) { echo ' {slg}-hide'; } ?>" data-rev="<?php echo esc_attr($review->provider); ?>">
            <div class="{slg}-review-inner<?php echo $addcls; ?>">
                <div class="{slg}-flex" style="--gap:12px;--dir:row;--align:center"><?php
                    if (!$options->hide_avatar) {
                        $default_avatar = Plugin::ASSETS_URL() . 'img/guest.png';
                        $author_img = empty($review->author_img) ? $default_avatar : $review->author_img;
                        if (isset($options->reviewer_avatar_size)) {
                            $author_img = str_replace('s128', 's' . $options->reviewer_avatar_size, $author_img);
                            $default_avatar = str_replace('s128', 's' . $options->reviewer_avatar_size, $default_avatar);
                        }
                        $this->image($author_img, '{slg}-img', $review->author_name, $options->lazy_load_img, $default_avatar);
                    }
                    ?><div class="{slg}-flex" style="--gap:6px;--dir:column;--align:star;--overflow:hidden"><?php
                        // Google reviewer name
                        if (empty($review->author_url)) {
                            $author_name = empty($review->author_name) ? __('Google User', Plugin::NAME) : $review->author_name;
                            ?><div class="{slg}-name"><?php echo esc_html($author_name); ?></div><?php
                        } else {
                            $this->anchor($review->author_url, '{slg}-name', $review->author_name, $options->open_link, $options->nofollow_link);
                        }
                        ?><div class="{slg}-time" data-time="<?php echo $review->time; ?>"><?php
                            echo esc_html(gmdate("H:i d M y", (int)$review->time));
                        ?></div>
                    </div>
                </div><?php

                echo $this->stars($review->rating, $review->provider);

                ?><div>
                    <div class="{slg}-feedback" <?php if (!empty($options->slider_text_height)) {?> style="height:<?php echo esc_attr($options->slider_text_height); ?>!important"<?php } ?>>
                        <?php if (!empty($review->text)) { ?>
                        <span class="{slg}-text"><?php echo $this->trim_text($review->text, $options->text_size); ?></span>
                        <?php } ?>
                    </div>
                </div><?php $this->social_logo($review->provider);
                if ($is_admin && isset($review->id)) {
                    echo '<a href="#" class="wp-review-hide" data-id=' . $review->id . '>' . ($review->hide == '' ? 'Hide' : 'Show') . ' review</a>';
                }
            ?></div>
        </div><?php
    }

    function stars($rating, $provider = '', $show_rating = false) {
        switch ($provider) {
            case 'facebook':
                if ($show_rating) {
                    ?><span class="rpi-stars" style="--rating:<?php echo esc_attr($rating); ?>"><?php echo $rating; ?></span><?php
                } else {
                    $text = ($rating < 2 ? "doesn't" : "") . ' recommends';
                    ?><span class="rpi-star-fb" data-rating="<?php echo $rating; ?>"><?php echo $text; ?></span><?php
                }
                break;
            case 'yelp':
            case 'tripadvisor':
                $data_atts = ' data-stars="' . floor($rating * 2) / 2 . '"';
                $data_atts .= $show_rating ? ' data-rating="' . $rating . '"' : '';
                ?><span class="rpi-stars-<?php echo $provider; ?>"<?php echo $data_atts; ?>><i></i><i></i><i></i><i></i><i></i></span><?php
                break;
            default:
                ?><span class="rpi-stars" style="--rating:<?php echo esc_attr($rating); ?>"><?php if ($show_rating) echo $rating; ?></span><?php
        }
    }

    private function writereview_url($biz) {
        switch ($biz->provider) {
            case 'google':      return 'https://search.google.com/local/writereview?placeid=' . $biz->id;
            case 'facebook':    return 'https://facebook.com/' . $biz->id . '/reviews';
            case 'tripadvisor': return 'https://www.tripadvisor.com/UserReview-d' . $biz->id;
            case 'yelp':        return 'https://www.yelp.com/writeareview/biz/' . $biz->id;
        }
    }

    private function social_logo($prov) {
        switch ($prov) {
            case 'google'      : $this->google_logo();      break;
            case 'facebook'    : $this->facebook_logo();    break;
            case 'yelp'        : $this->yelp_logo();        break;
            case 'tripadvisor' : $this->tripadvisor_logo(); break;
        }
    }

    private function google_logo() {
        ?><svg viewBox="0 0 512 512" width="44" height="44"><use xlink:href="#{slg}-logo-g"/></svg><?php
    }

    private function facebook_logo() {
        ?><svg viewBox="0 0 100 100" width="44" height="44" class="{slg}-fb"><use xlink:href="#{slg}-logo-f"/></svg><?php
    }

    private function yelp_logo() {
        ?><svg viewBox="0 0 533.33 533.33" width="44" height="44"><use xlink:href="#{slg}-logo-y"/></svg><?php
    }

    private function tripadvisor_logo() {
        ?><svg viewBox="0 0 132 86" width="44" height="44" class="{slg}-ta"><use xlink:href="#{slg}-logo-ta"/></svg><?php
    }

    private function powered($biz, $opt) {
        ?><div class="{slg}-powered {slg}-flex" data-platform="<?php echo esc_attr($biz->provider); ?>" style="--gap: 2px;--dir:row;--align:center"><?php
        switch ($biz->provider) {
            case 'google':
                ?><img src="<?php echo Plugin::ASSETS_URL(); ?>img/powered_by_google_on_<?php if ($opt->dark_theme) { ?>non_<?php } ?>white.png" alt="powered by Google" width="144" height="18" title="powered by Google"><?php
                break;
            case 'facebook':
                ?>powered by <span>Facebook</span><?php
                break;
            case 'yelp':
                ?>powered by <?php echo $this->anchor($biz->url, '', '<img src="' . Plugin::ASSETS_URL() . 'img/yelp-logo.png" alt="Yelp logo" width="60" height="31" title="Yelp logo">', $opt->open_link, $opt->nofollow_link); ?><?php
                break;
            case 'tripadvisor':
                ?>powered by <span>TripAdvisor</span><?php
                break;
        }
        ?></div><?php
    }

    function anchor($url, $class, $text, $open_link, $nofollow_link) {
        $href  = esc_url($url);
        $class_attr = $class ? ' class="' . esc_attr($class) . '"' : '';
        $target_rel = $open_link ? ' target="_blank"' : '';
        $rel_attr   = ' rel="' . ($nofollow_link ? 'nofollow ' : '') . 'noopener"';
        echo '<a href="' . $href . '"' . $class_attr . $target_rel . $rel_attr . '>' . wp_kses_post($text) . '</a>';
    }

    function image($src, $cls, $alt, $lazy, $def_ava = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7', $atts = '') {
        ?><img src="<?php echo esc_url($src); ?>" <?php if ($lazy) { ?>loading="lazy"<?php } ?> class="<?php echo esc_attr($cls); ?>" alt="<?php echo esc_attr($alt); ?>" onerror="this.onerror=null;this.src='<?php echo esc_url($def_ava); ?>';" <?php echo $atts; ?>><?php
    }

    function js_loader($cls, $func, $data = '') {
        ?><img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="js_loader" onload="(function(el, data) {var f = function() { window.<?php echo $cls; ?> ? <?php echo $cls . '.' . $func; ?>('{slg}', el, data) : setTimeout(f, 400) }; f() })(this<?php if (!empty($data)) { ?>, <?php echo str_replace('"', '\'', $data); } ?>);" width="1" height="1" style="display:none"><?php
    }

    function trim_text($text, $size) {
        if ($size > 0 && $this->strlen($text) > $size) {
            $sub_text = $this->substr($text, 0, $size);
            $idx = $this->strrpos($sub_text, ' ') + 1;

            if ($idx < 1 || $size - $idx > ($size / 2)) {
                $idx = $size;
            }
            if ($idx > 0) {
                $visible_text = $this->substr($text, 0, $idx - 1);
                $invisible_text = $this->substr($text, $idx - 1, $this->strlen($text));
            }
            echo wp_kses_post(balanceTags($visible_text, true));
            if ($this->strlen($invisible_text) > 0) {
                ?><span>... </span><span class="wp-more"><?php echo wp_kses_post(balanceTags($invisible_text, true)); ?></span><span class="wp-more-toggle"><?php echo __('read more', Plugin::NAME); ?></span><?php
            }
        } else {
            echo wp_kses_post(balanceTags($text, true));
        }
    }

    function strlen($str) {
        return function_exists('mb_strlen') ? mb_strlen($str, 'UTF-8') : strlen($str);
    }

    function strrpos($haystack, $needle, $offset = 0) {
        return function_exists('mb_strrpos') ? mb_strrpos($haystack, $needle, $offset, 'UTF-8') : strrpos($haystack, $needle, $offset);
    }

    function substr($str, $start, $length = NULL) {
        return function_exists('mb_substr') ? mb_substr($str, $start, $length, 'UTF-8') : substr($str, $start, $length);
    }

    function array($params=null) {
        if (!is_array($params)) {
            $params = func_get_args();
            $params = array_slice($params, 0);
        }
        return $params;
    }
}
