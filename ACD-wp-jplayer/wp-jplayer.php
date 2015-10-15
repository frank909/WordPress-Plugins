<?php
/*
Plugin Name: Angel City WP jPlayer
Description: WP JPlayer provides easy way to embed videos in jPlayer, cross platform video into your web pages.
Author: Frank Thoeny
 
*/

//avoid direct calls to this file, because now WP core and framework has been used
if (!function_exists('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

// Define certain terms which may be required throughout the plugin
define('WPJPLAYER_NAME', 'WP JPlayer');
define('WPJPLAYER_PATH', WP_PLUGIN_DIR . '/ACD-wp-jplayer');
define('WPJPLAYER_URL', WP_PLUGIN_URL . '/ACD-wp-jplayer');
define('WPJPLAYER_BASENAME', plugin_basename(__FILE__));

if (!class_exists('wpjplayer')) {
	class wpjplayer
	{

		static $count = 0;

		function wpjplayer()
		{
			if (is_admin()) {
				// nothing to do here
			} else {
                // inorder for shortcode to work in widget area
                add_filter('widget_text', 'do_shortcode');

                add_action('the_posts', array(&$this, 'has_shortcode'));
				add_shortcode('wpjplayer', array(&$this, 'process_short_code'));
			}
		}

		/**
		 * Loading scripts only if a particular shortcode is present
		 * 
		 * */
		function has_shortcode($posts)
		{
			if (empty($posts)) {
				return $posts;
			}

			$found = false;

			foreach ($posts as $post) {
				if (stripos($post->post_content, '[wpjplayer')) {
					$found = true;
				}
				break;
			}

			if ($found) {
				wp_enqueue_style( 'wpjplayer_jp_style', WPJPLAYER_URL . '/assets/skin/blue.monday/jplayer.blue.monday.css');
				wp_enqueue_script('wpjplayer_jp_script', WPJPLAYER_URL . '/assets/js/jquery.jplayer.min.js', array('jquery'));
			}
			return $posts;
		}


        /**
         * Process shortcode, generate video player markup and return it
         * */
        function process_short_code($atts)
		{
			//increase video count
			 $this->count += 1;

			if (empty($atts['m4v'])) {
				return 'SRC parameter is missing.';
			}
			if (empty($atts['ogg'])) {
				return 'SRC parameter is missing.';
			}			
			if (empty($atts['webm'])) {
				return 'SRC parameter is missing.';
			}
			if (empty($atts['flv'])) {
				return 'SRC parameter is missing.';
			}
			

            $default = array(
                'm4v' => '',
            	'ogg' => '',
            	'webm' => '',
                'title' => '',
                'poster' => '',
                'size' => '270p'
            );

            extract(shortcode_atts($default, $atts));

			// parameter for video player
			$count = $this->count;
			$jsurl = WPJPLAYER_URL.'/assets/js';
			
			$player = <<<PLY
			<script type="text/javascript">
				//<![CDATA[
				jQuery(function(){
					jQuery("#jquery_jplayer_$count").jPlayer({
						ready: function () {
							jQuery(this).jPlayer("setMedia", {
								flv: "$flv",
								m4v: "$m4v",
						       oga: "$ogg",
						       webm: "$webm"															
							}).jPlayer("play"); // Attempts to Auto-Play the media;;
						},
						size: {
			                         width: "480px",
			                         height: "300px"
			                    },	
						swfPath: "$jsurl",
						supplied: "flv, m4v, ogv, webm",
						cssSelectorAncestor: "#jp_container_$count",
                        loop: true,
                        keyEnabled: true
					});
				});
				//]]>
			</script>
			<div id="jp_container_$count" class="jp-video jp-video-$size">
			<div class="jp-type-single">
				<div id="jquery_jplayer_$count" class="jp-jplayer"></div>
				<div class="jp-no-solution">
					<span>Update Required</span>
					To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
				</div>
			</div>
		</div>
PLY;

			return $player;
		}
	}

	$wpjplayer = new wpjplayer();
}