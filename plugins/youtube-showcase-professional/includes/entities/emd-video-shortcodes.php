<?php
/**
 * Entity Related Shortcode Functions
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Shortcode function
 *
 * @since WPAS 4.0
 * @param array $atts
 * @param array $args
 * @param string $form_name
 * @param int $pageno
 *
 * @return html
 */
function yt_scase_pro_video_items_set_shc($atts, $args = Array() , $form_name = '', $pageno = 1) {
	$fields = Array(
		'app' => 'yt_scase_pro',
		'class' => 'emd_video',
		'shc' => 'video_items',
		'form' => $form_name,
		'has_pages' => true,
		'pageno' => $pageno,
		'pgn_class' => 'hidden',
		'theme' => 'bs'
	);
	$args_default = array(
		'posts_per_page' => '8',
		'post_status' => 'publish',
		'orderby' => 'meta_value_num',
		'meta_key' => 'emd_video_display_order',
		'order' => 'ASC'
	);
	return emd_shc_get_layout_list($atts, $args, $args_default, $fields);
}
add_shortcode('video_items', 'video_items_list');
function video_items_list($atts) {
	$show_shc = 1;
	if ($show_shc == 1) {
		$list = "<div class='emd-container'>";
		$list.= yt_scase_pro_video_items_set_shc($atts);
		$list.= "</div>";
	} else {
		$list = '<div class="alert alert-info not-authorized">You are not authorized to access this content.</div>';
	}
	return $list;
}
/**
 * Shortcode function
 *
 * @since WPAS 4.0
 * @param array $atts
 * @param array $args
 * @param string $form_name
 * @param int $pageno
 *
 * @return html
 */
function yt_scase_pro_video_indicators_set_shc($atts, $args = Array() , $form_name = '', $pageno = 1) {
	$fields = Array(
		'app' => 'yt_scase_pro',
		'class' => 'emd_video',
		'shc' => 'video_indicators',
		'form' => $form_name,
		'has_pages' => true,
		'pageno' => $pageno,
		'pgn_class' => 'visible-lg visible-md',
		'theme' => 'bs'
	);
	$args_default = array(
		'posts_per_page' => '8',
		'post_status' => 'publish',
		'orderby' => 'meta_value_num',
		'meta_key' => 'emd_video_display_order',
		'order' => 'ASC'
	);
	return emd_shc_get_layout_list($atts, $args, $args_default, $fields);
}
add_shortcode('video_indicators', 'video_indicators_list');
function video_indicators_list($atts) {
	$show_shc = 1;
	if ($show_shc == 1) {
		$list = "<div class='emd-container'>";
		$list.= yt_scase_pro_video_indicators_set_shc($atts);
		$list.= "</div>";
	} else {
		$list = '<div class="alert alert-info not-authorized">You are not authorized to access this content.</div>';
	}
	return $list;
}
/**
 * Shortcode function
 *
 * @since WPAS 4.0
 * @param array $atts
 * @param array $args
 * @param string $form_name
 * @param int $pageno
 *
 * @return html
 */
function yt_scase_pro_video_grid_set_shc($atts, $args = Array() , $form_name = '', $pageno = 1) {
	$fields = Array(
		'app' => 'yt_scase_pro',
		'class' => 'emd_video',
		'shc' => 'video_grid',
		'form' => $form_name,
		'has_pages' => true,
		'pageno' => $pageno,
		'pgn_class' => '',
		'theme' => 'bs'
	);
	$args_default = array(
		'posts_per_page' => '16',
		'post_status' => 'publish',
		'orderby' => 'meta_value_num',
		'meta_key' => 'emd_video_display_order',
		'order' => 'ASC'
	);
	return emd_shc_get_layout_list($atts, $args, $args_default, $fields);
}
add_shortcode('video_grid', 'video_grid_list');
function video_grid_list($atts) {
	$show_shc = 1;
	if ($show_shc == 1) {
		$list = "<div class='emd-container'>";
		$list.= yt_scase_pro_video_grid_set_shc($atts);
		$list.= "</div>";
	} else {
		$list = '<div class="alert alert-info not-authorized">You are not authorized to access this content.</div>';
	}
	return $list;
}
/**
 * Shortcode function
 *
 * @since WPAS 4.0
 * @param array $atts
 * @param array $args
 * @param string $form_name
 * @param int $pageno
 *
 * @return html
 */
function yt_scase_pro_video_wall_set_shc($atts, $args = Array() , $form_name = '', $pageno = 1) {
	$fields = Array(
		'app' => 'yt_scase_pro',
		'class' => 'emd_video',
		'shc' => 'video_wall',
		'form' => $form_name,
		'has_pages' => true,
		'pageno' => $pageno,
		'pgn_class' => '',
		'theme' => 'bs'
	);
	$args_default = array(
		'posts_per_page' => '16',
		'post_status' => 'publish',
		'orderby' => 'meta_value_num',
		'meta_key' => 'emd_video_display_order',
		'order' => 'ASC'
	);
	return emd_shc_get_layout_list($atts, $args, $args_default, $fields);
}
add_shortcode('video_wall', 'video_wall_list');
function video_wall_list($atts) {
	$show_shc = 1;
	if ($show_shc == 1) {
		$list = "<div class='emd-container'>";
		$list.= yt_scase_pro_video_wall_set_shc($atts);
		$list.= "</div>";
	} else {
		$list = '<div class="alert alert-info not-authorized">You are not authorized to access this content.</div>';
	}
	return $list;
}
add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode', 11);
