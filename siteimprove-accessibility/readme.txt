=== Siteimprove Accessibility ===
Contributors: siteimprove
Tags: accessibility, analytics, insights, spelling, seo
Requires at least: 6.7
Requires PHP: 8.0
Tested up to: 6.8
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Catch, monitor, and resolve web accessibility issues in minutes—right from your WordPress CMS.

== Description ==

The Siteimprove Accessibility plugin allows you to catch, monitor, and resolve accessibility issues in minutes—right from your WordPress CMS. Go beyond an overlay solution – ensure your site meets ADA and WCAG 2.2 standards through automated scanning, AI-powered suggestions, and developer tools before content goes live.

== Installation ==

There are a few ways to install the Siteimprove Accessibility plugin on WordPress.

- To install the plugin via the plugin listing page, type ‘Siteimprove Accessibility’ in the search box on the WordPress plugin listing page and click on ‘install’, or
- You can upload the Siteimprove Accessibility plugin zip file using WordPress’ ‘Upload Plugin’ feature, or
- You can upload the unzipped 'siteimprove-accessibility' folder into to the '/wp-content/plugins/' directory on your website via FTP.

The plugin can be activated via your Plugins page immediately after installation.


= Configuration =

You can manage certain features of the plugin, by clicking on the "Settings" link in the plugin list, under the name of this plugin.

A new Siteimprove Accessibility navigation item will also appear on the left menu bar, where you can also find the Settings page amongst other functionality. Click on this navigation item to continue configuring the plugin.

Note, that plugin configuration is only available for users who can manage plugins (usually administrators).


= Usage =

After installing and activating the plugin, you can start testing pages by navigating to them (i.e. your site's homepage, posts, pages, etc.) and clicking on the floating Siteimprove Accessibility overlay on the top-right side (or according to the configured position) of the page.

When the overlay is triggered, a new accessibility check will be executed on that page, and you will see the issues found directly.

You can also review the accessibility issues on the posts and pages you are currently editing in the Gutenberg block editor, if a check was already executed on that piece of content before.

While editing a post or a page, you can also evaluate the impact of your changes by checking the page in draft mode.

Additionally, you will be able to see the summary of known accessibility problems across your site under the Issues navigation item, and see a daily breakdown of the issues under Reports.


== Frequently Asked Questions ==

= Who can use this plugin? =

The plugin may be used by anyone with a WordPress site without mandatory Siteimprove subscriptions.

= Where is my data stored? =

Accessibility checks are local and data is stored locally in your own WordPress database.

= What third-party/external services this plugin use? =

This plugin uses the following third-party/external services:

1. **Siteimprove**
    The plugin uses the [SiteimproveAccessibilityCmsComponents](https://cdn.siteimprove.net/cms/siteimprove-accessibility-cms-components-latest.js) javascript library, loaded via CDN.
    It provides reusable React-based UI components for displaying issue details, issue lists, reports and more.
    These components are designed to be integrated across different CMS platforms, including WordPress, to ensure consistency and reduce the need for custom UI development.

    The plugin also uses Siteimprove's open source accessibility conformance testing engine called [Alfa](https://alfa.siteimprove.com/). Some of its features that requires to connect to [Siteimprove API](https://api.siteimprove.com/v2/documentation) are currently not used in this plugin.

3. **Pendo**
    Pendo is used to collect anonymous usage data, such as which features of the plugin users are interacting with. Usage data collection is active only with the explicit consent of the administrator (e.g. when usage tracking is enabled in the plugin settings page).
    This data helps us improve the plugin by understanding how users interact with its features. No personal or sensitive information is ever collected or stored.
    More about Pendo: [privacy policy](https://www.pendo.io/legal/privacy-policy/).

= Where can I find the development repository for this plugin? =

The development happens on GitHub, and the repository is available at: [https://github.com/Siteimprove/cms-wp-alfa-plugin](https://github.com/Siteimprove/cms-wp-alfa-plugin).

== Changelog ==

= 1.0.1 =
* Fixed database compatibility issue that caused the plugin to incorrectly being installed on some environments.
* Fixed terms and conditions link in the settings page.
* Fixed broken Siteimprove API link in the plugin documentation.

= 1.0.0 =
* First public version
