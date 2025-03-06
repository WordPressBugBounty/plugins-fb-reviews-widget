=== Social Reviews & Recommendations ===
Contributors: trust.reviews
Donate link: https://trust.reviews/
Tags: google reviews, facebook reviews, facebook recommendations, yelp reviews, ratings
Requires at least: 3.0.1
Tested up to: 6.7
Stable tag: 2.4
Requires PHP: 5.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Combines all Facebook Recommendations, Google and Yelp Reviews in widgets and shortcodes! Boost user confidence, number of customers and sales on site!

== Description ==

The plugin display **Facebook Reviews** and **Recommendations**, **Google** and **Yelp Reviews** merged or separated on your WordPress site in sidebar widgets or shortcodes. This plugin uses the official Facebook Graph API to show all reviews and requires an owner rights to the FB page. It displays up to 10 Google reviews and 3 Yelp reviews at the first install and can collect it daily to show more reviews.

Displaying **Facebook Rating**, **Google** and **Yelp Reviews** on your website is the easiest and most effective way to increase user trusts and, as a result, improve sales!

Feel free to try <a href="https://trust.reviews/">the Business version</a> to get more features!

[youtube https://www.youtube.com/watch?v=TEqz4RDr7EI]

[Online demo](https://trust.reviews/demos)

= Plugin Features =

* Combines reviews from Google, Facebook and Yelp
* Multiple widgets and shotcodes
* SEO
* Shortcode, widget and block support
* Auto refresh reviews
* Display ALL Facebook reviews
* Shows "Based on ... reviews" section
* Trim long reviews with "read more" link
* Support page builders: Gutenberg, Elementor, SiteOrigin, Beaver Builder, WPBakery, Divi
* Shows real reviews from Facebook users to increase user confidence
* Easy get of Facebook pages and instantly show reviews
* Review list theme
* Pagination
* Support dark websites
* Nofollow, target="_blank" links
* Fast cache (zero load time)

= Get More Features with Business version! =

[Upgrade to Business](https://trust.reviews/)

* New awesome flash theme!
* Merge reviews between each other from different platforms (Google, Facebook, Yelp) and places
* Display all Google and Facebook reviews
* Google Rich Snippets (schema.org)
* Powerful <b>Collection Builder</b>
* Slider/Grid themes to show G+ reviews like testimonials
* Facebook Trust Badge (right/left float or embedded)
* 'Write a review' button to available leave Google review directly on your website
* Show/hide any elements (business, reviews, avatars, names, time and etc)
* Any Sorting: recent, oldest, rating, striped
* Include/Exclude words filter
* Custom Facebook page photo
* Minimum rating filter
* Priority support

= Additional Free Reviews Plugins =

Why limit your reviews to just Facebook Reviews? Check out our other free reviews plugins to add to your site as well:

* [Google Reviews Widget](https://wordpress.org/plugins/widget-google-reviews/ "Google Reviews Widget")
* [Yelp Reviews Widget](https://wordpress.org/plugins/widget-yelp-reviews/ "Yelp Reviews Widget")

Please keep in mind that plugin requests Facebook permission <a href="https://developers.facebook.com/docs/permissions/reference/pages_show_list" target="_blank">pages_show_list</a>, <a href="https://developers.facebook.com/docs/permissions/reference/pages_read_user_content" target="_blank">pages_read_user_content</a> and <a href="https://developers.facebook.com/docs/permissions/reference/pages_read_engagement" target="_blank">pages_read_engagement</a> to read your page reviews and show it in the widget.

== Installation ==

1. Unpack archive to this archive to the 'wp-content/plugins/' directory inside of WordPress
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Facebook Reviews widget
2. Facebook Reviews shortcode
3. Facebook Reviews shortcode builder
4. Facebook Reviews sidebar widget

== Changelog ==

= 2.4 =
* Security fix: check nonce in rate us and overview controllers
* Improve: possibility to connect up to 10 Google reviews
* Update to WordPress 6.7
* Some style fixes

= 2.3 =
* Improve: added own Yelp API key field on the Settings page
* Update to WordPress 6.6

= 2.2.1 =
* Bugfix: twice CSS file in Remove Unused CSS safelist for WP Rocket plugin

= 2.2 =
* Improve: added main style file to Remove Unused CSS safelist for WP Rocket plugin
* Improve: added Lithuanian language

= 2.1 =
* Bugfix: rename Widget js lib to Plugin to avoid conflict with cloud version
* Bugfix: remove double quotes in shortcode for ID attribute
* Bugfix: correctly save Google API key in settings

= 2.0 =
* Fully architecture redesign
* Reviews feeds
* Reviews stats
* Slider, Grid layouts
* Google and Yelp platforms
* GDPR support
* Update to WordPress 6.4

= 1.7.9 =
* Improve: added 'Reviews count adder' parameter to correcting reviews count
* Improve: contrast (Based on, powered, User links, reviews time, Next Reviews)
* Update to WordPress 6.2

= 1.7.8 =
* Update FB API to version 14
* Added Czech language
* Added Hungarian language
* Added Portugal language
* Update to WordPress 6.1

= 1.7.7 =
* Improve description
* Added Polish language
* Bugfix: removed cookie usage in FB connection
* Bugfix: next reviews button does not work with wp paragraph wrapper
* Update to WordPress 5.9

= 1.7.6 =
* Update to WordPress 5.8

= 1.7.5 =
* Updated readme
* Improve: added Estonian language
* Bugfix: FB API attribute 'rating_count' bug
* Bugfix: removed deprecated manage_pages FB right

= 1.7.4 =
* Changed deprecated manage_pages permission of Facebook Graph API to the current ones
* Improve: added Ukrainian language
* Bugfix: little fixes in Swedish translation

= 1.7.3 =
* Improve: RTL support
* Bugfix: 'read more' supports UTF

= 1.7.2 =
* Improve: Added Slovenian language
* Tested WP 5.6

= 1.7.1 =
* Improve: Added Hebrew
* Improve: Added Greek
* Improve: Added Russian
* Tested WP 5.5

= 1.7 =
* Improve: Upgrade Facebook API to v7.0
* Improve: Added 'Based on ...' translation for Italian
* Bugfix: W3C compatibility

= 1.6.9 =
* Improve: Facebook connection without cross-site cookies

= 1.6.8 =
* Improve: Facebook Rating API has updated

= 1.6.7 =
* Improve: added new locale sk_SK
* Improve: added new locale de_AT
* Improve: update installation video, readme and screenshots
* Bugfix: Yoast XML plugin makes 'Class not found' error

= 1.6.6 =
* Improve: added 'Based on ... reviews' feature
* Improve: added hide reviews option

= 1.6.5 =
* Update to WordPress 5.3
* Improve: added dots for read more link
* Improve: added width, height, title for img elements (SEO)
* Improve: added rel="noopener" option

= 1.6.4 =
* Bugfix: is_admin checks for notice

= 1.6.3 =
* Improve: shortcode support bugfix
* Improve: upload page photo bugfix
* Bugfix: remove undefined grw_i function

= 1.6.2 =
* Improve: shortcode support
* Improve: upload page photo
* Improve: added new locale bg_BG
* Improve: admin notie
* Bugfix: undefined widget property in Elementor

= 1.6.1 =
* Bugfix: some style fixes

= 1.6 =
* Bugfix: escape GET parameters for a setting page

= 1.5.9 =
* Plugin's name changed
* Plugin's logo changed
* Bugfix: sanitize POST parameters

= 1.5.8 =
* Plugin description and images changes

= 1.5.7 =
* Check and fix all translations

= 1.5.6 =
* Bugfix: fix French translation
* Bugfix: fix German translation
* Bugfix: css max-width photo conflict

= 1.5.5 =
* Update to WordPress 5.2
* Bugfix: conflict with a Bootstrap css in the widget

= 1.5.4 =
* Update readme and links to the business version

= 1.5.3 =
* Improve: update user picture dimension to 120x120
* Improve: use Graph API with picture and open_graph_story

= 1.5.2 =
* Improve: option for image lazy loading

= 1.5.1 =
* Bugfix: fixed problem with duplicate image function

= 1.5 =
* Improve: Facebook avatars lazy loading
* Bugfix: fixed problem with Facebook avatars

= 1.4.9 =
* Improve: 'read more' link feature
* Improve: added centered option
* Improve: update widget design
* Improve: update setting page design

= 1.4.8 =
* Update plugin to WordPress 5.0
* Improve: the single Facebook page selected by default after connection in the widget
* Bugfix: fixed the issues with working on site builders (SiteOrigin, Elementor, Beaver Builder and etc)

= 1.4.7 =
* Important note: introduced support of Facebook recommendations, negative is considered as 1 star, positive recommendation 5 stars

= 1.4.6 =
* Bugfix: remove checking of App ID and App Secure in the widget

= 1.4.5 =
* Important note: Facebook has returned the right to get page reviews for our application while verification is in progress. The verification process can take up to several weeks and you can use the plugin in this time without any issues. Please re-install all widgets: make 'Log In with Facebook' again, select the page and save each widget.
* Improve: new option in the 'Advance Options' panel, if Facebook returns error, the plugin can show the latest success response

= 1.4.4 =
* Important note: Facebook still does not review our application to get page reviews and we introduced a workaround: now you need to create Facebook application yourself, save 'App ID' and 'App Secret' keys on the setting page and make 'Connect to Facebook' again in the widget to restore the reviews

= 1.4.3 =
* Feature: added option to disable user profile links
* Improve: the default number of reviews has increased to 250
* Bugfix: fixed broken FB profile links
* Bugfix: remove deprecated function create_function()

= 1.4.2 =
* Improve: support of SiteOrigin builder
* Update plugin's icon

= 1.4.1 =
* Bugfix: remove incorrect div from the theme

= 1.4 =
* Feature: Added pagination
* Feature: Added maximum width and height options
* Bugfix: replace http_build_query to string concatenation in API response
* Bugfix: triggered change event in the widget to enable save button
* Bugfix: corrected time ago messages

= 1.3 =
* Fixes incorrect release 1.2.9

= 1.2.9 =
* Improve: some fixes of Facebook Ratings API
* Bugfix: incorrect dates in the Safari browser
* Update plugin to WP 4.9

= 1.2.8 =
* Bugfix: widget caching
* Added Swedish language (sv_SE)

= 1.2.7 =
* Widget options description corrected
* Bugfix: time translation for Danish language

= 1.2.6 =
* Bugfix: Facebook account's page limit expanded
* Improve: Added Facebook Page Ratings API limit parameter in advance options

= 1.2.5 =
* Bugfix: cURL proxy fix
* Bugfix: CURLOPT_FOLLOWLOCATION for curl used only with open_basedir and safe_mode disable
* Improve: change permission from activate_plugins to manage_options for the plugin's settings
* Improve: extract inline init script of widget to separate js file (rplg.js), common for plugins
* Tested up to WordPress 4.8
* Added French language (fr_FR)
* Added Colombia language (es_CO)

= 1.2.4 =
* Bugfix: Cannot redeclare rplg_json_decode
* Bugfix: Cache plain API response instead of JSON

= 1.2.3 =
* Full refactoring of widget code
* Bugfix: widget options check
* Bugfix: SSL unverify connection
* Added debug information
* Added Danish language (da_DK)
* Added Dutch language (nl_NL)

= 1.2.2 =
* Added Turkish language (tr_TR)
* Added Italian language (it_IT)

= 1.2.1 =
* Bugfix: review text can be empty

= 1.2 =
* Bugfix: 'NaN undefined' date/time in IE and Safari

= 1.1 =
* Bugfix: time-ago on English by default, update readme

== Support ==

* Email support support@trust.reviews
* Forum support https://wordpress.org/support/plugin/fb-reviews-widget/
