<?php
/**
 * Entity Widget Classes
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class yt_scase_pro_recent_videos_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'yt-scase-pro';
	public $class_label;
	public $class = 'emd_video';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'recent-videos';
	public $id = 'yt_scase_pro_recent_videos_widget';
	public $query_args = array(
		'post_type' => 'emd_video',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
	);
	public $filter = '';
	/**
	 * Instantiate entity widget class with params
	 *
	 * @since WPAS 4.0
	 */
	function yt_scase_pro_recent_videos_widget() {
		$this->Emd_Widget(__('Recent Videos', 'yt-scase-pro') , __('Videos', 'yt-scase-pro') , __('The most recent videos', 'yt-scase-pro'));
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		global $post;
		if (get_post_meta(get_the_ID() , 'emd_video_thumbnail_image')) {
			$src_url = wp_get_attachment_url(get_post_meta(get_the_ID() , 'emd_video_thumbnail_image') [0]);
		} else {
			$src_url = "//img.youtube.com/vi/" . emd_mb_meta('emd_video_key') . "/mqdefault.jpg";
		}
		$layout = "
 <a href=\"" . get_permalink() . "\" title=\"" . get_the_title() . "\"><img src=\"" . $src_url . "\" alt=\"" . get_the_title() . "\" style=\"width:320px;height:180px;padding-bottom:5px\"></a>
   ";
		return $layout;
	}
}
/**
 * Entity widget class extends Emd_Widget class
 *
 * @since WPAS 4.0
 */
class yt_scase_pro_featured_videos_widget extends Emd_Widget {
	public $title;
	public $text_domain = 'yt-scase-pro';
	public $class_label;
	public $class = 'emd_video';
	public $type = 'entity';
	public $has_pages = false;
	public $css_label = 'featured-videos';
	public $id = 'yt_scase_pro_featured_videos_widget';
	public $query_args = array(
		'post_type' => 'emd_video',
		'post_status' => 'publish',
		'orderby' => 'date',
		'order' => 'DESC'
	);
	public $filter = 'attr::emd_video_featured::is::1';
	/**
	 * Instantiate entity widget class with params
	 *
	 * @since WPAS 4.0
	 */
	function yt_scase_pro_featured_videos_widget() {
		$this->Emd_Widget(__('Featured Videos', 'yt-scase-pro') , __('Videos', 'yt-scase-pro') , __('The most recent videos', 'yt-scase-pro'));
	}
	/**
	 * Returns widget layout
	 *
	 * @since WPAS 4.0
	 */
	public static function layout() {
		global $post;
		if (get_post_meta(get_the_ID() , 'emd_video_thumbnail_image')) {
			$src_url = wp_get_attachment_url(get_post_meta(get_the_ID() , 'emd_video_thumbnail_image') [0]);
		} else {
			$src_url = "//img.youtube.com/vi/" . emd_mb_meta('emd_video_key') . "/mqdefault.jpg";
		}
		$layout = "
 <a href=\"" . get_permalink() . "\" title=\"" . get_the_title() . "\"><img src=\"" . $src_url . "\" alt=\"" . get_the_title() . "\" style=\"width:320px;height:180px;padding-bottom:5px\"></a>";
		return $layout;
	}
}
$access_views = get_option('yt_scase_pro_access_views', Array());
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('recent_videos', $access_views['widgets']) && current_user_can('view_recent_videos'))) {
	register_widget('yt_scase_pro_recent_videos_widget');
}
if (empty($access_views['widgets']) || (!empty($access_views['widgets']) && in_array('featured_videos', $access_views['widgets']) && current_user_can('view_featured_videos'))) {
	register_widget('yt_scase_pro_featured_videos_widget');
}
