<?php

/**
 *	Flixster Reviews is released under the GNU General Public License (GPL)
 *	http://www.gnu.org/licenses/gpl.txt
 * 
 *	TODO: 
 *	Cache for images
 *  Keep settings
 *
 *	Widget URL: http://209.237.233.34/static/widget/widget.swf?getRev=http://209.237.233.34/servlet/publishrate/userId:[uid]
 *
 *	XML URL: http://209.237.233.34/servlet/publishrate/userId:[uid]
 * 
 */

/**
 * Displays flixster reviews for a particular user ID
 *
 */
class flixsterContentDisplayer {

	// flixster user ID
	var $user_id                    = '';
	// Maximum reviews to be displayed
	var $max_reviews                = -1;
	// Click on reviews to see their contents
	var $shadowable                 = TRUE;
	// Link images to flixster site
	var $movies_link_to             = TRUE;
	// Display titles
	var $movies_display_title       = TRUE;
	// Display images
	var $movies_display_image       = TRUE;
	// Display thumbnails instead of full size images
	var $movies_thumbs				= FALSE;
	// Display scores
	var $ratings_display_score      = TRUE;
	// Link scores to flixster account
	var $link_to_user    	        = FALSE;
	// Display comments
	var $comments_display           = TRUE;
	// Sort reviews by date or name
	var $reviews_sort_by            = 'date';
	// flixster site
	var $doc_base 					= "http://www.flixster.com";
	// Path to XML content
	var $xml_path                   = "/servlet/publishrate/userId:";
	// Name for stars image
	var $stars_img					= 'stars.gif';
	// Path to the stars
	var	$stars_path					= '/static/images/';
	// half stars
	var $stars_half 				= 'half';
	
	/**
	 * Displays movie title
	 */
	function disp_title($aMovieAttribs) {

		if ($this->movies_display_title) {
			if ($this->shadowable) {
				$sID = "flixster_widget_" . $aMovieAttribs['ID'];
				?>
				<a  href="#details"
					onClick="javascript:if (window.document.getElementById('<?php echo $sID; ?>').style.display == 'none') {
						fr_shadow('<?php echo $sID; ?>', false);  
					} else {
						fr_shadow('<?php echo $sID; ?>', true);  
					}">
			    <?php
			} else {
				echo "<a href='" . $this->doc_base . $aMovieAttribs['URL'] . "'>";
			}
			echo $aMovieAttribs['TITLE'];
			?></a><?php
		}
	}

	/**
	 * Displays movie image
	 */
	function disp_image($aMovieAttribs) {

		if ($this->movies_link_to) {
			?><a href="<?php echo $this->doc_base . $aMovieAttribs['URL']; ?>"> <?php
		}
		
		if ($this->movies_display_image) {
			if (!$this->movies_thumbs) {
				$sImage = str_replace('&thumb=true', '', $aMovieAttribs['IMAGE']);
			} else {
				$sImage = $aMovieAttribs['IMAGE'];
				?><p><div style="float: left;">
				<?php
			}
			?> <img src="<?php echo $this->doc_base . $sImage; ?>" /><?php
		}
		if ($this->movies_link_to) {
			?> </a><?php
		}
	}

/**
 * Displays movie rating
 */
	function disp_score($aRatingAttribs) {
	
		if ($this->link_to_user) {
			?><a href="<?php echo $this->doc_base . $aRatingAttribs['URL']; ?>"> 
			<?php
		}
	
		if (($this->ratings_display_score)  && ($aRatingAttribs['SCORE'] != "")) {
			//echo __("Score: ") . $aRatingAttribs['SCORE'];
		    if (($aRatingAttribs['SCORE'] >= 0) && ($aRatingAttribs['SCORE'] <= 5)) {
		    	// 1 to 5 entire stars
		    	$sImgSrc = $aRatingAttribs['SCORE'] . $this->stars_img;
		    } else if ($aRatingAttribs['SCORE'] == 6) {
		    	// half a star
		    	$sImgSrc = $this->stars_half . $this->stars_img;
		    } else if (($aRatingAttribs['SCORE'] >= 7) && ($aRatingAttribs['SCORE'] <= 10)) {
		    	$sImgSrc	= ($aRatingAttribs['SCORE'] - 6) . "_" 
							. $this->stars_half . $this->stars_img;
		    } else if ($aRatingAttribs['SCORE'] == 11) {
		    	$sImgSrc	= 'ni_flat.gif';
		    	
		    } else if ($aRatingAttribs['SCORE'] == 12) {
		    	$sImgSrc	= 'ws_flat.gif';
		    }
			?><img src="<?php echo $this->doc_base . $this->stars_path . $sImgSrc; ?>" /> <br />
			<?php
		}
		if ($this->movies_thumbs) {
			 ?></div></p>
			 <?php
		}
		
		if ($this->link_to_user) {
			?></a>
			<?php
		}
	}


/**
 * Displays movie comment
 */
	function disp_comment($aValue) {
		if ($this->comments_display) {
			echo $aValue;
		}
	}

	/**
	 * Displays movie review
	 */
	function disp_review($aData) {
		?>
		<li><?php
		$this->disp_title($aData['movie_attributes']);
		?>
		<div id="<?php echo "flixster_widget_" . $aData['movie_attributes']['ID']; ?>"<?php 
		if ($this->shadowable) 
			echo " style='display:none;opacity:0;filter:alpha(opacity = 0);zoom:1;'"; ?>><?php
		$this->disp_image($aData['movie_attributes']);?><br /><?php
		$this->disp_score($aData['rating_attributes']);
		$this->disp_comment($aData['comment_value']);
		?>
		<br />
		</div>
		</li>
		<?php
	}

	/**
	 * Displays all reviews
	 */
	function disp_reviews($aReviews) {
		
		?><ul><?php
		$i = 0;
		foreach($aReviews as $idx => $val) {
			if (($this->max_reviews >= 0) && ($i >= $this->max_reviews)) {
				break;
			}
			$this->disp_review($val);
			$i++;
		}
		if ($i == 0) {
			echo __("No recent movie review.");
		}
		?></ul>
		<?php
	}

	/**
	 * Returns an array containing XML data
	 */
	function &get_xml_data_array() {
		$xml_parser = xml_parser_create();
		$aValues  = Array();
		$indexes  = Array();
		
		if (empty($this->user_id)) {
			echo __("Please configure this widget with a user ID.<br />\n");
			return array();
		}
		$sUrl     = $this->doc_base . $this->xml_path . $this->user_id;
		if (!function_exists('curl_init')) {
			echo __("Please install the curl php extension.<br />\n");
			return array();
		}
		$ch       = curl_init();
		curl_setopt($ch, CURLOPT_URL,$sUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = curl_exec($ch);
		if (curl_errno($ch)) {
			echo(curl_error($ch));
			return array();
		} else {
			curl_close($ch);
		}
	
		if (!xml_parse_into_struct($xml_parser, $data, $aValues, $indexes)) {
	
			echo(sprintf(__('XML error: %1$s at line %2$s (url:%3$s)'),
			xml_error_string(xml_get_error_code($xml_parser)),
			xml_get_current_line_number($xml_parser),
			$sUrl
			));
			return array();
		}
		xml_parser_free($xml_parser);
		return $aValues;
	}
	
	function display_content() {
		$aValues	= $this->get_xml_data_array();
		$aReviews	= Array();
		$aData		= Array();
		$i    		= 0;
		foreach($aValues as $idx => $val) {
			if ((strtoupper($val['tag']) == 'RATING') && isset($val['attributes'])) {
				$aData['rating_attributes'] = $val['attributes'];
			}
			if ((strtoupper($val['tag']) == 'MOVIE') && isset($val['attributes'])) {
				$aData['movie_attributes'] = $val['attributes'];
			}
			if ((strtoupper($val['tag']) == 'COMMENT') && isset($val['value'])) {
				$aData['comment_value'] = $val['value'];
			}
			if ((strtoupper($val['tag']) == 'RATING') && ($val['type'] == "close") && (count($aData) > 0)) {
				if ($parameters['reviews_sort_by'] == 'rating') {
					$aReviews[$aData['rating_attributes']['SCORE']] = $aData;
				} else if ($parameters['reviews_sort_by'] == 'name') {
					$aReviews[$aData['movie_attributes']['TITLE']] = $aData;
				} else {
					$aReviews[$i] = $aData;
				}
				$aData = Array();
				$i++;
			}
		}
		if ($this->reviews_sort_by == 'rating') {
			krsort($aReviews);
		} else if ($this->reviews_sort_by == 'name') {
			ksort($aReviews);
		}

		$this->disp_reviews($aReviews);
	}
}
?>