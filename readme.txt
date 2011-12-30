=== WP-GPX-Maps ===
Contributors: bastianonm
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=bastianonm@hotmail.com&item_name=WP-GRX-Maps&item_number=WP-GRX-Maps&amount=5&currency_code=EUR
Tags: maps, gpx, gps, graph, google maps, google chart, track, garmin
Requires at least: 2.0.0
Tested up to: 3.3
Stable tag: 1.0.9
License: GPLv2 or later

Draws a gpx track with altitude graph

== Description ==
This plugin has, as input, the GPX file with the track you've made. As output it shows the map of the track, fixed with an altitude graph (where aviable).

- iphone/ipad/ipod Compatible

Try this plugin on <a href="http://www.pedemontanadelgrappa.it/category/mappe/">http://www.pedemontanadelgrappa.it/category/mappe/</a>

Thanks to: <a href="http://www.securcube.net/">www.securcube.net</a>, <a href="http://www.darwinner.it/">www.darwinner.it</a>, <a href="http://www.pedemontanadelgrappa.it/">www.pedemontanadelgrappa.it</a>, 

<a href="http://www.darwinner.it/featured/wp-gpx-maps/">Bugs, problems, thanks and anything else here!</a>

Supported gpx namespace are:

1. http://www.topografix.com/GPX/1/0

1. http://www.topografix.com/GPX/1/1

1. http://www.garmin.com/xmlschemas/GpxExtensions/v3

== Installation ==

1. Use the classic wordpress plugin installer or copy the plugins folder to the `/wp-content/plugins/` directory

1. Activate the plugin through the 'Plugins' menu in WordPress

1. Add the shortcode [sgpx gpx="&gt;relative path to your gpx&lt;"]

== Frequently Asked Questions ==

= what are all available shortcode attributes? =

The attributes are:

1. gpx: relative path to gpx

1. width: width in pixels

1. mheight: map height

1. gheight: graph height

1. mtype: map aviable types are: HYBRID, ROADMAP, SATELLITE, TERRAIN

1. waypoints: print the gpx waypoints inside the map (default is FALSE)

shortcode with all the attributes : [sgpx gpx="&gt;relative path to your gpx&lt;" width=100% mheight=300px gheight=200px mtype=SATELLITE waypoints=true]

= What happening if I've a very large gpx? =
This plugin will print a small amout of points to to speedup javascript and pageload.

= Is it free? =
Yes!

== Screenshots ==
1. Simple Gpx
1. Gpx with waypoints
2. Admin area

== Changelog ==
= 1.0.9 =
* minor bug fixes
* Windows/IIS compatibility
= 1.0.8 =
* New icons (from google maps)
* Added interactivity over the map
= 1.0.7 =
* Added waypoints support
* New icons
= 1.0.6 =
* minor bug fixes
= 1.0.5 =
* Fixed javscript errors with slow javascript loading
= 1.0.4 =
* Fixed Upload file error
* Added support for Garmin gpx (http://www.garmin.com/xmlschemas/GpxExtensions/v3 namespace)
= 1.0.3 =
* Added Settings link on plugins list
* Added attributes width, mheight, gheight, mtype on shortcode. 
= 1.0.2 =
* You can manage your gpx files in the admin area.
= 1.0.1 =
* Small changes on javascript localization.
= 1.0.0 =
* Initial release.

== Upgrade Notice ==
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
= 1.0.0 =
Initial release.