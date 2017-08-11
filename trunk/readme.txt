=== Gallery without the Fluff ===
Plugin Author: Justyn Walker @ revive web
Like us on Facebook: http://facebook.com/reviveweb/
Tags: photo gallery, image gallery, gallery, jquery
License: GPLv2
Requires at least: 3.3
Tested up to: 3.6
Stable tag: 1.1


== Description ==

This plugin is ONLY for people wanting to play around with their theme to get their images displaying in a jQuery gallery or slider script of their choice. You won't find an options page with preset jQuery effects like colorbox or masonry. That's the point. If you want to control which jQuery script you use to style your gallery, all that extra "fluff" sometimes gets in the way.

This simple plugin adds extra features to the wordpress media menu:

- Create any number of Galleries with the "Add Gallery" screen (just like you would create categories for a post).
- Each Gallery you create appears as a media menu item, showing the images in the gallery, with drag and drop image ordering.
- You can also update titles, captions and other meta info for all your gallery images in one bulk action to save time.
- The "Images into Gallery" screen allows you to assign uploaded images to a gallery.
- The BEST bit... you can customise the html and variables that will be outputted with the gallery, so you get exactly what you need for your jQuery script to work. Happy days!
- You then use a shortcode to show your galleries in any page, post or text widget.
- You can also call the gallery directly from a theme file.
- There are some helpful tips in the "Help" tab to the top right of some screens.

Note:

a)  Adding the jQuery script itself is up to you because it should be done inside your theme.
b)  To use this plugin you should be familiar with basic html &amp; how to add jQuery to a theme. See the WordPress Codex for help.

As we're using the default WordPress media menu and uploader all your gallery images are naturally available to your posts and widgets too.

Oh and by the way, there are no separate database tables, uploads folders, or media uploader. We're using the WordPress Media menu and adding the extra functionality we need. This means all your images are accessible to posts, pages and widgets as well as your galleries.

I hope it helps you. If you like the plugin, please vote for it.

== Installation ==

1. Go to your Plugins menu, choose Add new.
2. Browse to the zip file of this plugin and upload it.
3. Activate the plugin when prompted.

Now look at the WordPress Media menu. You will notice the "Add Gallery" submenu and several other changes. Use this to add your first gallery.

Use the "Images into Gallery" menu to drop a few of your uploaded images into the gallery you created.

You need to be logged in as an Administrator to see the "Settings" submenu where you customise the html for your jQuery script. This plugin uses the wordpress loop (WP_Query) to loop through each image in your gallery, so you will probably want some html before and after the loop to act as a container, and then the html inside the loop that needs to repeat for each image.

== Screenshots ==

1. Add new galleries using a custom taxonomy.
2. Check the images you want and select the gallery to put them in.
3. Add gallery metabox in edit image screen.
4. Each gallery you create has a submenu screen like this.
5. Settings for the output of your gallery.

== Other Notes ==

How this plugin works:

As WordPress treats images as posts, the plugin uses the custom taxonomy feature to create terms called "galleries" that we can attach to the images, just like you attach a category to a post.

This plugin uses the WordPress loop (WP_Query) to loop through the images in the galleries you create. It allows you to set markup that will be displayed before and after the loop, as well as inside the loop. You can also include any $post variable in the loop between curly brackets, such as {post_title}.

You can then use a shortcode that is displayed at the top of the gallery screen to show the gallery in your pages and posts. You can also use a template tag: <?php fluff_add_my_gallery('my-gallery','large'); ?> where "my-gallery" is the slug of your gallery (from the shortcode) and the second parameter is the image size to output. You can specify your custom images sizes here too.

A few of the backend screens have help tabs with some useful tips and reminders.

Other Notes:

I have to admit I don't have much time to manage this plugin or respond to questions. It's sort of a "here it is - I hope it helps" thing. But I use it on a number of projects and will do my best to make sure it's working with the current version of WordPress.

== Upgrade Notice ==

Requires WordPress 3.3 or higher. If you use older version of WordPress, you need to upgrade WordPress first.