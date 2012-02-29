=== WP GPX Maps ===
Contributors: bastianonm
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=bastianonm@hotmail.com&item_name=WP-GRX-Maps&item_number=WP-GRX-Maps&amount=5&currency_code=EUR
Tags: maps, gpx, gps, graph, google maps, google chart, track, garmin
Requires at least: 2.0.0
Tested up to: 3.3
Stable tag: 1.1.8
License: GPLv2 or later

Draws a gpx track with altitude graph

== Description ==
This plugin has, as input, the GPX file with the track you've made and as output it shows the map of the track and an interactive altitude graph (where available).

- iphone/ipad/ipod Compatible

Try this plugin: <a href="http://www.pedemontanadelgrappa.it/category/mappe/">http://www.pedemontanadelgrappa.it/category/mappe/</a>

Thanks to: <a href="http://www.securcube.net/">www.securcube.net</a>, <a href="http://www.darwinner.it/">www.darwinner.it</a>, <a href="http://www.pedemontanadelgrappa.it/">www.pedemontanadelgrappa.it</a>, 

<a href="http://www.darwinner.it/featured/wp-gpx-maps/">Bugs, problems, thanks and anything else here!</a>

Supported gpx namespaces are:

1. http://www.topografix.com/GPX/1/0

1. http://www.topografix.com/GPX/1/1

1. http://www.garmin.com/xmlschemas/GpxExtensions/v3

== Installation ==

1. Use the classic wordpress plugin installer or copy the plugins folder to the `/wp-content/plugins/` directory

1. Activate the plugin through the 'Plugins' menu in WordPress

1. Add the shortcode [sgpx gpx="&gt;relative path to your gpx&lt;"] or [sgpx gpx="&gt;http://somesite.com/files/yourfile.gpx&lt;"]

== Frequently Asked Questions ==

= what are all available shortcode attributes? =

The attributes are:

1. gpx: relative path to gpx

1. width: width in pixels

1. mheight: map height

1. gheight: graph height

1. mtype: map available types are: HYBRID, ROADMAP, SATELLITE, TERRAIN

1. waypoints: print the gpx waypoints inside the map (default is FALSE)

1. donotreducegpx: print all the point without reduce it (default is FALSE)

1. pointsoffset: skip points closer than XX meters(default is 10)

1. uom: the unit of measure of distance/altitude are values are: 0, 1, 2 (0 = meters, 1 = feet/miles, 2 = meters/kilometers)

1. mlinecolor: map line color (default is #3366cc)

1. glinecolor: altitude line color (default is #3366cc)

1. showspeed: show speed inside the chart (default is FALSE)

1. glinecolorspeed: speed line color (default is #ff0000)

1. uomspeed: the unit of measure of speed are: 0, 1, 2 (0 = m/s, 1 = km/h, 2 = miles/h)

= What happening if I've a very large gpx? =
This plugin will print a small amout of points to speedup javascript and pageload.

= Is it free? =
Yes!

== Screenshots ==
1. Simple Gpx
1. Gpx with waypoints
2. Admin area - List of tracks
2. Admin area - Settings

== Changelog ==
= 1.1.8 =
* cache issues fixed
* added speed when not present in the gpx (derived from datetime)
= 1.1.7 =
* new unit of measure (meters/kilometers)
* mouse wheel scrolling issue fixed
* minor bug fixes
= 1.1.6 =
* improved charts
* improved admin area
* added speed support (where available)
* fixed mootools incompability
= 1.1.5 =
* implemented cache (the plugin is faster, especially on slow servers or external gpx)
* minor bug fixes
= 1.1.4 =
* improved admin area
* added miles/feet unit of measure 
* added map line color and graph line color
* minor bug fixes
= 1.1.3 =
* Allowed gpx files from http url
= 1.1.2 =
* Improved page load time
* Added compatibility to Wordpress Multisite (WPMU)
= 1.1.1 =
* Minor bug fixes
= 1.1.0 =
* Added Advanced Setting in the Admin Area
* Added the shortcode for every entry in the admin area (easy to copy and paste in your posts)
= 1.0.9 =
* Minor bug fixes
* Windows/IIS compatibility
= 1.0.8 =
* New icons (from google maps)
* Added interactivity over the map
= 1.0.7 =
* Added waypoints support
* New icons
= 1.0.6 =
* Minor bug fixes
= 1.0.5 =
* Fixed javscript errors with slow javascript loading
= 1.0.4 =
* Fixed Upload file error
* Added support for Garmin gpx (http://www.garmin.com/xmlschemas/GpxExtensions/v3 namespace)
= 1.0.3 =
* Added Settings link on plugins list
* Added attributes width, mheight, gheight, mtype on shortcode
= 1.0.2 =
* You can manage your gpx files in the admin area
= 1.0.1 =
* Initial release

== Upgrade Notice ==
= 1.1.8 =
= 1.1.7 =
= 1.1.6 =
* Added speed support. To enable this feature please check the plugin settings
= 1.1.5 =
= 1.1.4 =
= 1.1.3 =
= 1.1.2 =
= 1.1.1 =
= 1.1.0 =
= 1.0.9 =
= 1.0.8 =
= 1.0.7 =
* Added waypoints support. To enable this feature please check the plugin settings
= 1.0.6 =
= 1.0.5 =
= 1.0.4 =
= 1.0.3 =
= 1.0.2 =
= 1.0.1 =
Initial release.