<?php
/*
Plugin Name: Flixster Reviews
Plugin URI: http://phiva.doesntexist.com/?page_id=104
Description: Displays lastest flixter reviews associated to a user ID
Author: Philippe Sam-Long
Version: 1.1
Author URI: http://phiva.doesntexist.com

	Flixster Reviews is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl.txt

	This is a WordPress plugin (http://wordpress.org) and widget
	(http://automattic.com/code/widgets/).
*/

require_once 'flixster_content_displayer.php';

// We're putting the plugin's functions in one big function we then
// call at 'plugins_loaded' (add_action() at bottom) to ensure the
// required Sidebar Widget functions are available.
function widget_flixsterreviews_init() {

	// Check to see required Widget API functions are defined...
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return; // ...and if not, exit gracefully from the script.

	// This function prints the sidebar widget--the cool stuff!
	function widget_flixsterreviews($args) {

		// $args is an array of strings which help your widget
		// conform to the active theme: before_widget, before_title,
		// after_widget, and after_title are the array keys.
		extract($args);

		$options = get_option('widget_flixsterreviews');
		$title 		= empty($options['title']) ? 'Movies (<a href="http://www.flixster.com">flixster</a>)' : $options['title'];
		
		?>
		<script type="text/javascript" src="<?php echo get_bloginfo('wpurl') ?>/wp-includes/js/tw-sack.js"></script>
		<script src="<?php echo get_bloginfo('siteurl'); ?>/wp-content/plugins/flixster/flixster_content_displayer.js" type="text/javascript"></script>

		<?php
 		// It's important to use the $before_widget, $before_title,
 		// $after_title and $after_widget variables in your output.
		echo $before_widget;
		echo $before_title . $title . $after_title;
		?>
		
		<span id="flixster_widget_container">
		<?php echo __("Loading..."); ?>
		</span>
		<?php echo $after_widget; ?>
		
		<script type="text/javascript">

		//<![CDATA[
			var contentSite = '<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/flixster/flixster_content.php';
			ajax = new sack(contentSite);
			ajax.element = 'flixster_widget_container';
			ajax.runAJAX();
//			window.setInterval("ajax.runAJAX()", <?php echo /*$options['ws_update_interval']*/10 * 1000; ?>);
		//]]>

		</script>
		<?php
	}

	// This is the function that outputs the form to let users edit
	// the widget's title and so on. It's an optional feature, but
	// we'll use it because we can!
	function widget_flixsterreviews_control() {

		// Collect our widget's options.
		$options = get_option('widget_flixsterreviews');

		if (!is_array($options)) {
			$options = array(	'title'				=>'Movies (<a href="http://www.flixster.com">flixster</a>)',
								'comments_display'	=> 'on',
								'movies_thumbs' 	=> 'on',
								'shadowable' 		=> 'on');
		}
		
		// This is for handing the control form submission.
		if (isset( $_POST['flixsterreviews-submit'] )) {
			// Clean up control form submission options
			$newoptions['title']			= stripslashes($_POST['flixsterreviews-title']);
			$newoptions['user_id'] 			= strip_tags(stripslashes($_POST['flixsterreviews-user_id']));
			$newoptions['comments_display']	= strip_tags(stripslashes($_POST['flixsterreviews-comments_display']));
			$newoptions['shadowable'] 		= strip_tags(stripslashes($_POST['flixsterreviews-shadowable']));
			$newoptions['link_to_user'] 	= strip_tags(stripslashes($_POST['flixsterreviews-link_to_user']));
			$newoptions['movies_thumbs'] 	= strip_tags(stripslashes($_POST['flixsterreviews-movies_thumbs']));
		}

		// If original widget options do not match control form
		// submission options, update them.
		if (isset($newoptions) && ( $options != $newoptions )) {
			$options = $newoptions;
			update_option('widget_flixsterreviews', $options);
		}

		// Format options as valid HTML. Hey, why not.
		$title 				= htmlspecialchars($options['title'], ENT_QUOTES);
		$user_id			= htmlspecialchars($options['user_id'], ENT_QUOTES);
		$shadowable 		= htmlspecialchars($options['shadowable'], ENT_QUOTES);
		$comments_display	= htmlspecialchars($options['comments_display'], ENT_QUOTES);
		$link_to_user		= htmlspecialchars($options['link_to_user'], ENT_QUOTES);
		$movies_thumbs		= htmlspecialchars($options['movies_thumbs'], ENT_QUOTES);
		// The HTML below is the control form for editing options.
?>
		<div>
		<label for="flixsterreviews-title" style="line-height:30px;display:block;">Title: <input type="text" id="flixsterreviews-title" name="flixsterreviews-title" value="<?php echo $title; ?>" /></label>
		<label for="flixsterreviews-user_id" style="line-height:30px;display:block;">User ID: <input type="text" id="flixsterreviews-user_id" name="flixsterreviews-user_id" value="<?php echo $user_id; ?>" /></label>
		<label	for="flixsterreviews-shadowable" style="line-height:30px;display:block;">Shadowable reviews: <input type="checkbox" id="flixsterreviews-shadowable" name="flixsterreviews-shadowable" <?php if ($shadowable == 'on') echo 'checked="checked"'; ?> /></label>
		<label for="flixsterreviews-comments_display" style="line-height:30px;display:block;">Display comments: <input type="checkbox" id="flixsterreviews-comments_display" name="flixsterreviews-comments_display" <?php if ($comments_display == 'on') echo "checked"; ?> /></label>
		<label for="flixsterreviews-movies_thumbs" style="line-height:30px;display:block;">Thumbnails instead of full size images: <input type="checkbox" id="flixsterreviews-movies_thumbs" name="flixsterreviews-movies_thumbs" <?php if ($movies_thumbs == 'on') echo 'checked="checked"'; ?> /></label>
		<label for="flixsterreviews-link_to_user" style="line-height:30px;display:block;">Link scores to user: <input type="checkbox" id="flixsterreviews-link_to_user" name="flixsterreviews-link_to_user" <?php if ($link_to_user == 'on') echo 'checked="checked"'; ?>" /></label>
		<input type="hidden" name="flixsterreviews-submit" id="flixsterreviews-submit" value="1" />
		</div>
	<?php
	// end of widget_flixsterreviews_control()
	}

	// This registers the widget. About time.
	register_sidebar_widget('flixster Reviews', 'widget_flixsterreviews');

	// This registers the (optional!) widget control form.
	register_widget_control('flixster Reviews', 'widget_flixsterreviews_control');
}

// Delays plugin execution until Dynamic Sidebar has loaded first.
add_action('plugins_loaded', 'widget_flixsterreviews_init');
?>
