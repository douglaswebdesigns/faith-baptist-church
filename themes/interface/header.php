<?php
/**
 * Displays the header section of the theme.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<link rel="apple-touch-icon" sizes="57x57" href="/wp-content/images/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="/wp-content/images/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="/wp-content/images/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="/wp-content/images/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="/wp-content/images/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="/wp-content/images/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="/wp-content/images/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="/wp-content/images/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="/wp-content/images/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="/wp-content/images/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="/wp-content/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="/wp-content/images/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="/wp-content/images/favicon-16x16.png">
<link rel="manifest" href="/manifest.json">
<meta name="msapplication-TileColor" content="#095A4C">
<meta name="msapplication-TileImage" content="/wp-content/images/ms-icon-144x144.png">
<meta name="theme-color" content="#095A4C">
<?php		
		/** 
		 * interface_title hook
		 *
		 * HOOKED_FUNCTION_NAME PRIORITY
		 *
		 * interface_add_meta_name 5
		 *
		 */
			//global $interface_theme_setting_value;
		 //echo $interface_theme_setting_value['home_slogan1' ]; 
		do_action( 'interface_title' );

		/** 
		 * interface_meta hook
		 */
		do_action( 'interface_meta' );

		/** 
		 * interface_links hook
		 *
		 * HOOKED_FUNCTION_NAME PRIORITY
		 *
		 * interface_add_links 10
		 * interface_favicon 15
		 * interface_webpage_icon 20
		 *
		 */
		do_action( 'interface_links' ); ?>
<?php 
		/** 
		 * This hook is important for WordPress plugins and other many things
		 */
		wp_head();
	?>
</head>

<body <?php body_class(); ?>>
<?php

		 if(is_page_template( 'facebook-streaming-video' )) { ?>

		<!-- Load Facebook SDK for JavaScript -->
		  <div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.6";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>

		<?php } ?>


<?php
		/** 
		 * interface_before hook
		 */
		do_action( 'interface_before
			' );
	?>
<div class="wrapper">
<?php
			/** 
			 * interface_before_header hook
			 */
			do_action( 'interface_before_header' );
		?>
<header id="branding" >
  <?php
				/** 
				 * interface_header hook
				 *
				 * HOOKED_FUNCTION_NAME PRIORITY
				 *
				 * interface_headercontent_details 10
				 */
				do_action( 'interface_header' );
			?>
</header>
<?php
			/** 
			 * interface_after_header hook
			 */
			do_action( 'interface_after_header' );
		?>
<?php
			/** 
			 * interface_before_main hook
			 */
			do_action( 'interface_before_main' );
		?>
<div id="main">
<div class="container clearfix">
