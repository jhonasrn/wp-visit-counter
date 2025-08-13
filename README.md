=== WP Visit Counter ===
Contributors: jhonas
Tags: visits, counter, analytics, stats, dashboard

A lightweight plugin to track daily visits to your WordPress site, with an admin dashboard and date filters.

== Description ==

WP Visit Counter is a simple and efficient plugin that tracks how many visitors access your site each day. It stores visit data in a JSON file and displays it in a clean admin dashboard.

Features include:

* Daily visit tracking
* Display today's visitor count
* Admin dashboard with date range filters
* Shortcode to display total visits: `[visit_counter]`
* No database required — uses a JSON file for storage

Perfect for small sites that want basic analytics without relying on external services.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the "Plugins" menu in WordPress
3. Use the shortcode `[visit_counter]` in any post or page
4. Go to **Visit Counter** in the admin menu to view daily stats

== Frequently Asked Questions ==

= Does this plugin use the database? =
No. All visit data is stored in a JSON file located in the uploads folder.

= Can I reset the visit data? =
Currently, the plugin does not allow full data reset to avoid accidental loss. You can filter visits by date in the admin panel.

= Is it GDPR compliant? =
Yes. The plugin does not store personal data or IP addresses.

== Screenshots ==

1. Admin dashboard showing daily visits
2. Date filter form
3. Today's visitor count

== Changelog ==

= 2.0 =
* Added admin dashboard with date filters
* Display today's visitor count
* Removed full data reset option

= 1.0 =
* Initial release with total visit counter and shortcode

== Upgrade Notice ==

= 2.0 =
Major update: adds admin dashboard and daily tracking. Safe to upgrade.

