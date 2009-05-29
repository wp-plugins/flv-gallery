=== FLV Gallery ===
Contributors: russell.todd
Tags: flv, video, gallery
Requires at least: 2.7
Tested up to: 2.7
Stable tag: trunk

Add a gallery of FLV videos to a post.

== Description ==

Add a gallery of FLV videos to a post.

FLV Gallery allows you to add multiple FLV videos to a single page, thumbnail gallery style. Most video plugins assume you only want to show one video on your post, so if you have multiple videos to show, they are stacked one above the other. This plugin uses shortcode notation to allow you to insert as many videos as you want into your post. Using thumbnails, a title and caption, and any additional link, the user will see a nicely formatted gallery of the available videos. Videos are shown in a lightbox dialog.

Online demo: [English: FLV Gallery on insidenorthpoint.org](http://insidenorthpoint.org/groups/assimilation/branding-promotion/ "FLV Gallery on Inside North Point")

Dependencies: The FLV Gallery uses jQuery and the [Simple Modal plugin](http://www.ericmmartin.com/projects/simplemodal/ "Simple Modal plugin") by Eric Martin 

== Installation ==

1. Upload the entire `flv-gallery` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

The control panel of FLV Gallery is in 'Settings > FLV Gallery', where you can set the following:

1. Width of Parent Container (default = 580): This is the number of pixels you have to play with, typically the width of the div that the thumbnail gallery will live in. The plugin uses this total width along with the thumbnail widths to optimize the layout of the thumbnails.

2. Thumbnail size (default = 125 x 125): The dimensions that you will make the thumbnails. The gallery will layout best if you upload thumbnails of the same size.

3. Modal Player Style (default = "backgroundColor: '#fff', border: '3px solid #75001b'"): This is the CSS style you can use for the modal dialog window that the video will play in. The main styles to set are the background color and border, but you can also set the padding. The overall size of the dialog is based on the video size.


To add videos to your post/page, insert a line like the following for each video:

[flvgallery video="/path/to/videos/video1.flv" title="Title for Video 1" caption="Optional Caption" thumbnail="/path/to/thumbnails/thumbnail1.jpg" url="<optional click to>" url_text="<optional link text"> url_icon="<optional link icon>" width="400" height="225"]

- required parameters:
	- video: full path to the video file
	- title: the tile for the video
	- thumbnail: full path to the thumbnail
	- width: width of the video
	- height: height of the video

- optional parameters:
	- caption: an optional caption to go under the thumbnail
	- url: if you want people to click over to a URL from the gallery page, e.g. to a e-commerce page selling a product
	- url_text: if using a url, the text (e.g. "buy now")
	- url_icon: instead of using text for the link, use an icon (e.g. "/path/to/icons/buy.png")
	
	
KNOW ISSUES:

I'm still trying to make it work with FLV videos hosted by a streaming content provider, but I can't get past the cross-domain issue. I'm using essentially the same code (not as a WordPress plugin) on other sites without issue, so I would welcome any help.

== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
