<?php

	header('Content-Type: text/html; charset=utf-8');

	include_once '../../../wp-blog-header.php';
	include_once 'flixster_content_displayer.php';
//	require_once ABSPATH . '/wp-includes/class-snoopy.php';

	$options = get_option('widget_flixsterreviews');
//	echo stripslashes($options['output_before']);
	
		$fDisp          			= new flixsterContentDisplayer();
		$fDisp->user_id 			= empty($options['user_id']) 
									? '' 
									: $options['user_id'];
									
		$fDisp->comments_display	= empty($options['comments_display']) 
									? false 
									: ($options['comments_display'] == 'on');
									
		$fDisp->shadowable 			= empty($options['shadowable'])
									? false
									: ($options['shadowable']		== 'on');

		$fDisp->link_to_user		= empty($options['link_to_user'])
									? false
									: ($options['link_to_user']		== 'on');

		$fDisp->movies_thumbs		= empty($options['movies_thumbs'])
									? false
									: ($options['movies_thumbs']	== 'on');
									
									
		$fDisp->display_content();
	
//	echo stripslashes($options['output_after']);
?>