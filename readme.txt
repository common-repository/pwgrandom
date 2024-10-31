=== PWGRandom ===
Contributors: jbaessens
Tags: phpwebgallery, photo, random, pwgrandom,widget, image, sidebar
Requires at least: 1.5
Tested up to: 2.5.1
Stable tag: trunk

Display random photos from your phpwebgallery in your sidebar. 

== Description ==

This plugin displays random pictures from a PhpWebGallery (www.phpwebgallery.net). Each photo is followed by a link on its category in the gallery.
The PhpWebGallery installation has to be on the same server than your blog. It can be used as a widget or not.
You will be able to choose the number of photos and the text displayed.

examples : 
http://chezju.318racing.com
http://www.lewebdeseb.fr/blog/

*Feature List*

* Configurable via WordPress admin interface.
* Support for WordPress 1.5, 2.0, 2.1, 2.2, 2.3 and 2.5.1
* Widget support
* Tested on PhpWebGallery 1.7 et 1.6

== Installation ==
1. Upload `pwgrandom.php` to the `/wp-content/plugins/` directory.
2. Activate `PWGRandom` through the 'Plugins' menu in WordPress.
3. Go to the `Options` menu and `PWGRandom` page.
4. Feel the url and path information.
3. What to do now depends on how up to date your theme is:

    **Modern theme with widget support**

    The plugin is a [widget](http://automattic.com/code/widgets/). If your
theme supports widgets, and you have installed the [widget
plugin](http://wordpress.org/extend/plugins/widgets/), adding the plugin to the
sidebar is easy: Go to the presentation menu and drag and drop the widget into
the sidebar. All done.

    **Old school theme without widget support**

    You need to insert the following code snippet into the sidebar template.   
*wp-content/themes/name of theme/sidebar.php*

        <?php if (function_exists('PWGRandom_display_picture')) 
			{
              PWGRandom_display_picture(); 
			}
		?>
		
	Note : you can use the old school method to add this plugin where you want in your blog.
		
== Frequently Asked Questions ==

= The sentence "Check the PWGRandom configuration" is displayed in the sidebar =
You have to go in the Optins panel of your Wordpress blog and check the url and path of your PhpWebGallery.
		
== Contributors/Changelog ==
    Version 	Date		Changes
    
    1.0     	2008/05/08 	Initial Release
	1.1     	2008/05/12 	Add the possibilty to create a table of images with multiple rows
	1.11     	2008/05/13 	Use the images with a case sensitive test on extension
