=== Reviews Widgets for Google, TripAdvisor, Yelp & Recommendations ===
Contributors: widgetpack
Tags: reviews, google reviews, facebook reviews, tripadvisor reviews, yelp reviews
Requires PHP: 5.2
Requires at least: 4.7
Tested up to: 7.0
Stable tag: 2.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Combine Facebook reviews and recommendations with Google, TripAdvisor and Yelp reviews in a widget, block or shortcode. Build a trusted website!

== Description ==

⭐ **Reviews Widgets for Google, TripAdvisor, Yelp & Recommendations** is a simple and powerful WordPress plugin that displays real customer reviews from Google, TripAdvisor and Yelp, together with Facebook recommendations, in a combined feed.

---

## ⭐ Unified feed — Facebook + Google + TripAdvisor + Yelp together!

📌 This plugin brings together reviews from **Facebook Recommendations**, **Google**, **TripAdvisor** and **Yelp** into a single combined feed.
📌 You no longer need separate plugins for each platform — everything is managed in one place using one widget, one shortcode and one settings panel.
📌 This reduces setup time, avoids plugin conflicts and keeps your site lighter and easier to manage.

---

## ⭐ Unlimited business locations & unlimited widgets

📍 **Unlimited locations** — connect any number of Google places, TripAdvisor listings, Yelp listings or Facebook Pages.
🗂️ **Unlimited widgets** — each widget can use its own layout and source selection.
🔧 Each widget can display a different location, different platform or any custom combination of Google, TripAdvisor, Yelp and Facebook recommendations.

There are no limits on:
- number of locations
- number of widgets
- number of shortcodes
- number of pages where you display reviews

---

## ⭐ API usage & platform limitations

This plugin uses the official Facebook Graph API to show **all recommendations** and requires owner/admin rights to the Facebook Page. It displays up to **10 Google reviews**, available **TripAdvisor reviews** and **3 Yelp reviews** at the first install and can collect more reviews over time.

---

## ⭐ Why display social reviews?

📈 Build trust and credibility
📌 Improve conversions
📉 Reduce bounce rate
⏱️ Increase dwell time
🏆 Strengthen local business reputation
🚀 Add verified social proof near your call-to-action

This plugin gives you full control — show a few top reviews or an entire feed.

---

## ⭐ Manual filtering & moderation

You have full manual control over what is displayed.
If a review is irrelevant or not needed — hide it with one click.
This helps maintain a clean and relevant feed.

---

## ⭐ Multiple display layouts

Choose between:

- List
- Grid
- Slider (carousel)

Suitable for sidebars, landing pages, homepage sections, footers and full-width layouts.

---

## ⭐ SEO & performance benefits

- Lightweight and optimized
- Improves engagement
- Clean HTML markup (SEO-friendly)
- Compatible with caching plugins
- Lazy-loading for images
- Fully responsive on all screens

---

## ⭐ Sources & review limits

- **Facebook** — all Recommendations available through the official Graph API (no limits)
- **Google** — up to 10 reviews via public API, more synced manually every 3 days using the “Refresh” option
- **TripAdvisor** — displays available TripAdvisor reviews supported by the connection source
- **Yelp** — up to 3 reviews via public API, more synced manually every 3 days using the “Refresh” option

---

== Features ==

• Display Google, TripAdvisor, Yelp and Facebook recommendations
• Unified feed — combine all platforms into one widget
• Unlimited business locations
• Unlimited widgets and shortcodes
• Multiple layouts: list, grid, slider (carousel)
• Manual moderation (hide/show any review)
• Responsive design
• Manual refresh every 3 days
• Pagination for long lists
• SEO-friendly clean markup
• Lazy-loading for images
• Official APIs only — no scraping
• Compatible with Elementor, Divi, Beaver Builder, SiteOrigin, WPBakery
• Gutenberg block and classic widget included
• Dark mode and light mode
• Works immediately with no coding

---

== Demo ==

[Online demo](https://trust.reviews/demos)

[youtube https://www.youtube.com/watch?v=TEqz4RDr7EI]

---

== Installation ==

1. Upload plugin files to `/wp-content/plugins/` or install via the Plugins screen.
2. Activate **Reviews Widgets for Google, TripAdvisor, Yelp & Recommendations**.
3. Go to **Social Reviews** in wp-admin.
4. Connect your Google places, TripAdvisor listings, Yelp listings and Facebook Pages.
5. Add the widget, shortcode or block anywhere.

---

== FAQ ==

### ❓ How many reviews can I display?

Google: up to 10, via public API.
Yelp: up to 3, via public API.
Facebook: all Recommendations for your own Pages.

### ❓ Does the plugin use official APIs?

Yes. Google and Yelp use public endpoints.
Facebook requires login authorization and uses the official Graph API.

### ❓ How many business locations can I connect?

Unlimited — you can connect any number of locations.

### ❓ How many widgets can I create?

Unlimited — each widget can have its own layout and source combination.

### ❓ Can I hide specific reviews manually?

Yes. You can hide or show any review.

### ❓ What layouts are supported?

List, grid and slider (carousel).

### ❓ Can I combine different platforms into one feed?

Yes — Google + Facebook + TripAdvisor + Yelp in any combination.

### ❓ Do I need a Facebook app?

No. Simply authorize with your Facebook account.

### ❓ Is JavaScript required?

Only for “Read more”, slider and pagination.

---

== Screenshots ==

1. Reviews widget example
2. Shortcode example
3. Shortcode builder
4. Sidebar widget example

---

== Changelog ==

= 2.8 =
* Security fixes: added output escaping across the widget, shortcode and builder.
* Fixed a database collation mismatch issue that could prevent reviews from loading on some installations.
* Improved compatibility with multilingual review text storage for existing installations.
* Fixed review rendering when reviewer avatars are hidden.
* Improved handling of missing or unavailable connected review sources.
* Minor hardening and stability improvements.
* Tested and updated for WordPress 7.0.


= 2.7.3 =
* Improved asset loading by adding version to file path URLs
* Added option to disable inline CSS
* Fixed incorrect inline CSS output
* Fixed star icon conflicts

= 2.7.2 =
* Fixed Google rating and star icons

= 2.7.1 =
* Improved Facebook recommendation icons
* Fixed Facebook rating and stars
* Fixed "Rate Us" popup after first widget creation
* UI and style enhancements

= 2.7 =
* Major rewrite of the plugin architecture
* New Google reviews connection wizard
* Added support for Facebook recommendations with platform icons
* Improved and stabilized Yelp reviews integration
* Added support for TripAdvisor reviews
* Ability to combine reviews from multiple platforms into a single feed or separately
* Fully redesigned reviews data structure with multilingual text support
* Complete refactor of frontend styles
* Performance improvements
* Major bug fixes

= 2.6 =
* Updated star icons for better clarity and consistency
* Added proper escaping on the Overview page.
* Tested and updated for WordPress 6.9.
* Minor style adjustments

= 2.5 =
* Security fixes (added escaping in overall views)
* Bugfix: fixed Yelp reviews connector
* Update to WordPress 6.8

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
