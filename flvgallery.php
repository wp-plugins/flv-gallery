<?php
/*
Plugin Name: FLV Gallery
Plugin URI: http://northpoint.org
Description: This plugin allows you to add multiple FLV videos to a single page, using thumbnails and a single modal player. 
Version: 1.3	
Author: Russell Todd
Author URI: http://northpoint.org
*/

/*  Copyright 2009  Russell Todd  (email : russell.todd@northpoint.org)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/* Russell Todd - add shortcode for flv gallery */
$flvItemCount = 0;
// [flvgallery video="x.flv" streamer="<optional streaming URL>" title="" caption="" thumbnail="x.jpg" url="<optional click to>" url_text="<optional link text"> url_icon="<optional link icon>" width="" height=""]
function flvgallery_shortcode($atts) {
	global $flvItemCount;
	
	$tw = get_option('flvgallery_thumbnail_width');
	$th = get_option('flvgallery_thumbnail_height');

	
	extract(shortcode_atts(array(
		'video' => null,
		'streamer' => null,
		'title' => null,
		'caption' => null,
		'thumbnail' => null,
		'url' => null,
		'url_text' => null,
		'url_icon' => null,
		'width' => 426,
		'height' => 240,

		
	), $atts));

	if (!$video || !$title) return "";
	
	$flvItemCount++;
	
	// if we have a valid video then add the action for the javascript
	if ($flvItemCount == 1) {  ?>
	<script type="text/javascript">	
	var flvconfig = new Array();
	function VideoConfig(v,w,h,s) {
		this.video = v;
		this.width = w;
		this.height = h;
		this.streamer = s;
	}
	</script>
		<?php
	}
	
	
	$h = '<div class="flvgallery-item">';
	$h .= '<a href="video-'.$flvItemCount.'" class="flvgallery-link">';
	$h .= '<h2>' . $title . '</h2>';
	if ($thumbnail) $h .= '<div class="flvgallery-thumbnail"><img src="' . $thumbnail . '" alt="' . $title .'" /></div></a><br />';
	if ($caption && !$url) $h .= '<span class="flvgallery-caption" style="width:100%;">'. $caption . '</span>';
    if ($url) {
		if ($caption) $h .= '<span class="flvgallery-caption">'. $caption . '</span>';
    	$h .= '<span class="flvgallery-link">';
    	if ($url_text) $h .= '<a href="' . $url . '" target="_blank">' . $url_text . '</a>';
    	if ($url_icon) $h .= '<a href="' . $url . '" target="_blank">' . $url_icon . '</a>';	
    	$h .= '</span>';
    }	
	$h .= '</div>';
	$h .= '<script type="text/javascript">flvconfig["video-' . $flvItemCount . '"] = new VideoConfig("' . $video . '",' . $width . ',' . $height . ',"' . $streamer . '");</script>';
	/* if ($flvItemCount % 3 == 0) $h .= '<div style="clear:both; line-height: 1px;" >&nbsp;</div>'; */
	return $h . "\n";
}
function flvgallery_jquery() {
// add the capability to show a streamed video. The following options get set in the flashvars array.
// 'streamer' : 'rtmp://streaming.baseurl/basefolder',
// 'type' : 'rtmp',
// 'file' : 'path to video file on streaming server'
	global $flvItemCount;
	echo "<!-- inside flvgallery_jquery -->\n";
	if ($flvItemCount > 0) {	
	?>
	<div id="flvgallery-player" style="width:426px;height:240px;display:none;"><span id="flv-gallery-player">&nbsp;</span></div>
	<script type="text/javascript">
	var maxRowH = 0;
	jQuery(document).ready(function() {
		jQuery("a.flvgallery-link").click(function(evt) {
			evt.preventDefault();
			var flvId = jQuery(this).attr('href');
			var video = flvconfig[flvId].video;
			var width = flvconfig[flvId].width;
			var height = flvconfig[flvId].height;
			var streamer = flvconfig[flvId].streamer;
			var flashvars = { wmode: "opaque", 
							  allowfullscreen: "false", 
							  "autostart": true, 
							  "file": video, 
							  "bufferlength": "15", 
							  "volume": "100", 
							  "backcolor": "FFFFFF", 
							  "frontcolor": "000000", 
							  "lightcolor": "000000", 
							  "screencolor": "000000" };
			if (streamer) {
				flashvars["streamer"] = streamer;
				flashvars["type"] = "rtmp";
			}
			//var dw = width + 20;
			//var dh = height + 20;
			var m = -1 * (width / 2);
			jQuery("#flvgallery-player").modal( 
				{ close: true, 
				  onOpen: animateOpen, 
				  overlayCss: {
				    backgroundColor: '#000',
				    cursor: 'wait'
				  },
				  containerCss: {
				    height: height,
				    width: width,
				    padding: 10,
				    left: '50%',
				    top: '15%',
				    'margin-left': m,
				    'background-color': '<?php echo get_option('flvgallery_modal_bg_color'); ?>',
				    border: '<?php echo get_option('flvgallery_modal_border') ?>'
				    
				  } 
			});
			var xh = 'Flashvars: {\n';
			for (x in flashvars) {
				xh += x + ': ['+flashvars[x]+']\n';
			}
			
			swfobject.embedSWF("<?php echo WP_PLUGIN_URL; ?>/flv-gallery/player-viral.swf", "flv-gallery-player", width, height, "9", null, flashvars, { wmode: "opaque", allowfullscreen: "true" }, {});

		});
		jQuery(".flvgallery-item").each( function() {
			var h = jQuery(this).height();
			if (h > maxRowH) maxRowH = h;		
		});
		setHeights();
	});
	function animateOpen(dialog) {
		dialog.overlay.fadeIn('slow', function () {
		    dialog.container.slideDown('slow', function () {
		      dialog.data.fadeIn('slow');
		    });
		});
	}
	function setHeights() {
		if (maxRowH == 0) {
			alert("max is 0");
			return;
		}
		jQuery(".flvgallery-item").height(maxRowH);

	}
	</script>
<?php
	}
    return $id;
}

/* Add the CSS necessary to display the simple modal close button and properly layout the thumbnails */
function flvgallery_head() {
	wp_enqueue_script("simplemodal",WP_PLUGIN_URL . "/flv-gallery/jquery.simplemodal.js",array("jquery")); 
	wp_enqueue_script("swfobject",WP_PLUGIN_URL . "/flv-gallery/swfobject.js"); 
	wp_print_scripts();

 	// do the math first
 	$cw = get_option('flvgallery_container_width'); // default 580
	$tw = get_option('flvgallery_thumbnail_width'); // default 125
	$th = get_option('flvgallery_thumbnail_height'); // default 125
	
	if (!$cw) $cw = 580;
	if (!$tw) $tw = 125;
	if (!$th) $th = 125;
	
	$num_cols =  floor($cw / $tw );
	$horiz_space = $cw % $tw;

	if ($horiz_space == 0) { // if it's exact then there's no spacing
		$num_cols--;
		$horiz_space = $tw;
	}
	$half_horiz_space = int_divide ( $horiz_space, 2 );
	
	$h_pad = int_divide ($half_horiz_space, ($num_cols * 2) );
	$remaining_h = $horiz_space - ( $h_pad * ($num_cols * 2));
	
	$h_margin = floor ($remaining_h / ( $num_cols * 2));
	
?>
<!-- FLV Gallery Styles -->
<style type="text/css">
#modalContainer a.modalCloseImg {
  background:url(<?php echo WP_PLUGIN_URL; ?>/flv-gallery/x.png) no-repeat;
  width:25px;
  height:29px;
  display:inline;
  z-index:3200;
  position:absolute;
  top:-14px;
  right:-18px;
  cursor:pointer;
}
div.flvgallery-item {
	width: <?php echo $tw; ?>px;
	margin: 5px <?php echo $h_margin; ?>px;
	padding: 5px <?php echo $h_pad; ?>px;
	float: left;
	display: inline;
}
div.flvgallery-item h2 {
	margin: 0px;
}
div.flvgallery-thumbnail {
	width: <?php echo $tw; ?>px;
	max-width: <?php echo $tw; ?>px;
	height: <?php echo $th; ?>px;
	max-height: <?php echo $th; ?>px;
}
div.flvgallery-thumbnail img {
	max-width: <?php echo $tw; ?>px;
	max-height: <?php echo $th; ?>px;
}

span.flvgallery-caption {
	width: 70%;
	float: left;
}
span.flvgallery-link {
	width: 28%;
	float: right;
	text-align: right;
	padding-left: 2%;
}
</style>
<?php
}

function int_divide($x, $y) {
    if ($x == 0) return 0;
    if ($y == 0) return FALSE;
    return ($x - ($x % $y)) / $y;
}


/* Display the Settings Page */
function flvgallery_admin_page() {

	$var_cw = 'flvgallery_container_width';
	$var_tw = 'flvgallery_thumbnail_width';
	$var_th = 'flvgallery_thumbnail_height';
	$var_bg = 'flvgallery_modal_bg_color';
	$var_bd = 'flvgallery_modal_border';
/*	
	if ( $_REQUEST['action'] == 'update') {
		register_setting( 'flvgallery', $var_cols, $_REQUEST[$var_cols] );
		register_setting( 'flvgallery',  $var_tw, $_REQUEST[$var_tw] );
		register_setting( 'flvgallery', $var_th, $_REQUEST[$var_th] );
		register_setting( 'flvgallery', $var_css, $_REQUEST[$var_css] );
	}
*/

	$cw = get_option($var_cw);
	$tw = get_option($var_tw);
	$th = get_option($var_th);
	$bg = get_option($var_bg);
	$bd = get_option($var_bd);
	
	print '<!-- cols ['. $cols . '] tw [' . $tw . '] th [' . $th . '] bg [' . $bg . '] bd [' . $bd . '] -->' . "\n";
?>
    
	<div class="wrap">
		<h2>FLV Gallery Options</h2>
		
			<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<?php settings_fields('flvgallery'); ?>

			
			<table class="form-table">
			
				<tr valign="top">
					<th scope="row">Width of the parent container</th>
					<td><input type="text" name="flvgallery_container_width" value="<?php echo $cw ?>" />
					</td>
				</tr>
			 
			<tr valign="top">
				<th scope="row">Thumbnail Size</th>
				<td><input type="text" name="flvgallery_thumbnail_width" value="<?php echo $tw ?>" />&nbsp;x&nbsp; 
					<input type="text" name="flvgallery_thumbnail_height" value="<?php echo $th; ?>" /></td>
			</tr>
	
			<tr valign="top">
				<th scope="row">Modal Player Background Color (e.g. <em>#ffffff</em>)</th>
				<td><input type="text" name="flvgallery_modal_bg_color" value="<?php echo $bg ?>" size="60" /></td>
			</tr>
			
			<tr valign="top">
				<th scope="row">Modal Player Border Style (use border CSS shorthand, e.g. <em>3px solid #75001b</em>)</th>
				<td><input type="text" name="flvgallery_modal_border" value="<?php echo $bd ?>" size="60" /></td>
			</tr>
		
			</table>
			
<?php /*			<input type="hidden" name="page_options" value="flvgallery_columns,flvgallery_thumbnail_width,flvgallery_thumbnail_height,flvgallery_modal_css" /> */ ?>
			
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		
		</form>
	</div>

<?php
}

$defaults = array ( "flvgallery_container_width" => 580, 
					"flvgallery_thumbnail_width" => 125,
					"flvgallery_thumbnail_height" => 125,
					"flvgallery_modal_bg_color" => "#fff",
					"flvgallery_modal_border" => "3px solid #75001b"
				);
	
function flvgallery_plugin_activate() {			
	foreach ($defaults as $k => $v) {
		update_option($k,$v);
	}
}

function flvgallery_clear() {
	echo '<div style="clear:both;line-height:1px">&nbsp;</div>';
}

add_action( 'admin_init', 'flvgallery_admin_init' );
function flvgallery_admin_init(){
	register_setting( 'flvgallery', 'flvgallery_container_width');
	register_setting( 'flvgallery', 'flvgallery_thumbnail_width' );
	register_setting( 'flvgallery', 'flvgallery_thumbnail_height' );
	register_setting( 'flvgallery', 'flvgallery_modal_bg_color' );
	register_setting( 'flvgallery', 'flvgallery_modal_border' );
}

add_action( 'admin_menu', 'RegisterSettingsPage' );
function RegisterSettingsPage() {
	add_options_page( "FLV Gallery Conifguration", 'FLV Gallery', 8, __FILE__, 'flvgallery_admin_page' );
}

add_shortcode('flvgallery', 'flvgallery_shortcode');
add_action('wp_head','flvgallery_head');
add_action('wp_footer','flvgallery_jquery');
add_action('loop_end','flvgallery_clear');
register_activation_hook( __FILE__, 'flvgallery_plugin_activate' );

?>