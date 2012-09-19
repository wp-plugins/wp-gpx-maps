=== WP GPX Maps ===

Contributors: bastianonm
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8VHWLRW6JBTML
Tags: maps, gpx, gps, graph, chart, google maps, highcharts, track, garmin, image, nextgen-gallery, nextgen, exif, OpenStreetMap, OpenCycleMap, Hike&Bike, heart rate, heartrate, cadence
Requires at least: 2.0.0
Tested up to: 3.4
Stable tag: 1.1.37

Draws a gpx track with altitude graph. You can also display your nextgen gallery images in the map.

== Description ==

IMPORTANT NOTICE

wp-gpx-maps will no longer be available on wordpress.org, because highcharts is not GPL licensed.
If you want to keep wp-gpx-maps updated, follow this link: http://www.devfarm.it/forums/forum/wp-gpx-maps/


This plugin has, as input, the GPX file with the track you've made and as output it shows the map of the track and an interactive altitude graph (where available).

Fully configurable: 

- Custom colors 
- Custom icons
- Multiple language support

NextGen Gallery Integration:

Display your NextGen Gallery images inside the map! 
Even if you don't have a gps camera, this plugin can retrive the image position starting from the image date and you gpx file. 

Old NGGallery Images (without gps data) and gpx: <a href="http://www.pedemontanadelgrappa.it/mappe/itinerario-3-alta-via-degli-eroi/">http://www.pedemontanadelgrappa.it/mappe/itinerario-3-alta-via-degli-eroi/</a>

Translated into 7 languages:

- English (default)
- Italian it_IT
- German de_DE
- Spanish es_ES
- Dutch nl_NL
- Swedish sv_SE
- Turkish tr_TR

(many thanks to all the guys who helped me with the translations)

- iphone/ipad/ipod Compatible

Try this plugin: <a href="http://www.pedemontanadelgrappa.it/category/mappe/">http://www.pedemontanadelgrappa.it/category/mappe/</a>

<a href="http://www.devfarm.it/forums/forum/wp-gpx-maps/">Support Forum</a>

Supported gpx namespaces are:

1. http://www.topografix.com/GPX/1/0

1. http://www.topografix.com/GPX/1/1

1. http://www.garmin.com/xmlschemas/GpxExtensions/v3

1. http://www.garmin.com/xmlschemas/TrackPointExtension/v1

Thanks to: <a href="http://www.securcube.net/">www.securcube.net</a>, <a href="http://www.devfarm.it/">www.devfarm.it</a>, <a href="http://www.pedemontanadelgrappa.it/">www.pedemontanadelgrappa.it</a>

Up to version 1.1.15 [Highcharts-API](http://www.highcharts.com/) is the only available rendering engine. Please respect their license and pricing (only Free for Non-Commercial usage).

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

1. showhr: show heart rate inside the chart (default is FALSE)

1. showcad: show cadence inside the chart (default is FALSE)

1. glinecolorspeed: speed line color (default is #ff0000)

1. glinecolorhr: heart rate line color (default is #ff77bd)

1. glinecolorcad: cadence line color (default is #beecff)

1. uomspeed: the unit of measure of speed are: 0, 1, 2, 3, 4 (0 = m/s, 1 = km/h, 2 = miles/h, 3 = min/km, 4 = min/miles)

1. chartFrom1: minimun value for altitude chart

1. chartTo1: maxumin value for altitude chart

1. chartFrom2: minimun value for speed chart

1. chartTo2: maxumin value for speed chart

1. startIcon: Start track icon

1. endIcon: End track icon

1. currentIcon: Current position icon (when mouse hover)

1. waypointicon: waypoint custom icon

1. nggalleries: NextGen Gallery id or a list of Galleries id separated by a comma

1. ngimages: NextGen Image id or a list of Images id separated by a comma

1. dtoffset: the difference (in seconds) between your gpx tool date and your camera date

1. zoomonscrollwheel: zoom on map when mouse scroll wheel 

1. download: Allow users to download your GPX file 

1. skipcache: Do not use cache. If TRUE might be very slow (default is FALSE) 

1. summary: Print summary details of your GPX (default is FALSE) 

1. summarytotlen: Print Total distance in summary table (default is FALSE) 

1. summarymaxele: Print Max Elevation in summary table (default is FALSE) 

1. summaryminele: Print Min Elevation in summary table (default is FALSE) 

1. summaryeleup: Print Total climbing in summary table (default is FALSE) 

1. summaryeledown: Print Total descent in summary table (default is FALSE) 

1. summaryavgspeed: Print Average Speed in summary table (default is FALSE) 

1. summarytotaltime: Print Total time in summary table (default is FALSE) 

= What happening if I've a very large gpx? =
This plugin will print a small amout of points to speedup javascript and pageload.

= Is it free? =
Yes!

== Screenshots ==
1. Simple Gpx
1. Gpx with waypoints
1. Admin area - List of tracks
1. Admin area - Settings
1. Altitude & Speed
1. Altitude & Speed & Hearth rate

== Changelog ==
= 1.1.36 =
* Even Editor and Author users can upload their own gpx. Administrators can see all the administrators gpx. The other users can see only their uploads
= 1.1.35 =
* Fix: In the post list, sometime, the maps was not displaying correctly ( the php rand() function was not working?? )
* Various improvements for multi track gpx. Thanks to GPSracks.tv
* Summary table is now avaiable even without chart. Thanks to David 
= 1.1.34 =
* 2 decimals for unit of measure min/km and min/mi
* translation file updated (a couple of phrases added)
* File list reverse order (from the newer to the older)
* nggallery integration: division by zero fixed
= 1.1.33 =
* Decimals reducted to 1 for unit of measure min/km and min/mi
* map zoom and center position is working with waypoints only files 
* automatic scale works again (thanks to MArkus)
= 1.1.32 =
* You can exclude cache (slower and not recommended)
* You can decide what show in the summary table
* German translation (thanks to Ali)
= 1.1.31 =
* Fixed fullscreen map image slideshow
= 1.1.30 =
* Multi track gpx support
* Next Gen Gallery images positions derived from date. You can adjust the date with the shortcode attribute dtoffset
* If you set Chart Height (shortcode gheight) = 0 means hide the graph
* Fix: All images should work, independent from browser cache
= 1.1.29 =
* Decimal separator is working with all the browsers
* minutes per mile and minutes per kilometer was wrong
= 1.1.28 =
* Decimal and thousand separator derived from browser language
* Added summary table (see settings): Total distance, Max elevation, Min elevation, Total climbing, Total descent, Average speed
* Added 2 speed units of measure: minutes per mile and minutes per kilometer
= 1.1.26 =
* Multilanguage implementation (only front-end). I've implemented the italian one, I hope somebody will help me with other languages..
* Map Full screen mode (I'm sure it's not working in ie6. don't even ask!)
* Added waypoint custom icon
= 1.1.25 =
* Added possibility to download your gpx
= 1.1.23 =
* Security fix, please update!
= 1.1.22 =
* enable map zoom on scroll wheel (check settings)
* test attributes in get params
= 1.1.21 =
* google maps images fixed (templates with bad css)
* upgrade to google maps 3.9
= 1.1.20 =
* google maps images fixed in <a href="http://wordpress.org/extend/themes/yoko">Yoko theme</a>
= 1.1.19 = 
* include jQuery if needed
= 1.1.17 = 
* Remove zero values from cadence and heart rate charts
* nextgen gallery improvement
= 1.1.16 = 
* Cadence chart (where available)
* minor bug fixes
= 1.1.15 =
* migration from google chart to highcharts. Highcharts are much better than google chart! This is the base for a new serie of improvements. Stay in touch for the next releases!
* heart rate chart (where available)
= 1.1.14 =
* added css to avoid map bars display issue
= 1.1.13 =
* added new types of maps: Open Street Map, Open Cycle Map, Hike & Bike.
* fixed nextgen gallery caching problem 
= 1.1.12 =
* nextgen gallery display bug fixes

== Upgrade Notice ==
