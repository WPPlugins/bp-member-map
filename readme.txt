=== BP Member Map ===
Contributors: johnjamesjacoby
Tags: buddypress, geo, google, location
Requires at least: 2.9.1.1
Tested up to: 2.9.1.1
Stable tag: 1.1.5

BP Member Map is a plugin that maps the users on your site.

== Description ==

BP Member Map allows people on a BuddyPress site to have a map of their location on their profile. It uses the Google location API to convert real-world addresses into latitude and longitude coordinates.

== Changelog ==

= Version 1.1.5 =
* Complete rewrite from fork.
* Functions put into proper classes
* Added more hooks and filters for easier additional development
* Directory still needs rewrite for BuddyPress 1.2 (use at your own risk)

= Version 1.1 =
* Forked from BraveNewCode's BuddyPress Geo plugin, updated for BuddyPress 1.2, and simplified

= Version 1.0.3 =

* Added ability to delete database table and restore it from the admin panel
* Fixed bug involving a full location table rebuild
* Fixed end condition for table rebuild

= Version 1.0.2 =

* Fixed stray miles string

= Version 1.0.1 =

* Updated verbiage

= Version 1.0.0 =

* Fixed queries to be compatible with HyperDB when the user table is on a different database
* First beta

== Installation ==

= WordPress 2.8.5 and above = 

* Install the plugin into the bp-member-map directory, and activate as site-wide
* Make sure you have a field that represents each user's location.  If not, add one.
* From the settings screen, configure the plugin three simple options
* Use the bp_show_member_map() function to output a map on a members profile

== Frequently Asked Questions ==

None so far!