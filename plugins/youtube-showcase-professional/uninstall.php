<?php
/**
 *  Uninstall YouTube Showcase Professional
 *
 * Uninstalling deletes notifications and terms initializations
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('WP_UNINSTALL_PLUGIN')) exit;
if (!current_user_can('activate_plugins')) return;
function yt_scase_pro_uninstall() {
	//delete options
	$options_to_delete = Array(
		'yt_scase_pro_notify_list',
		'yt_scase_pro_ent_list',
		'yt_scase_pro_attr_list',
		'yt_scase_pro_shc_list',
		'yt_scase_pro_tax_list',
		'yt_scase_pro_rel_list',
		'yt_scase_pro_license_key',
		'yt_scase_pro_license_status',
		'yt_scase_pro_comment_list',
		'yt_scase_pro_access_views',
		'yt_scase_pro_limitby_auth_caps',
		'yt_scase_pro_limitby_caps',
		'yt_scase_pro_has_limitby_cap',
		'yt_scase_pro_setup_pages',
		'yt_scase_pro_emd_video_terms_init'
	);
	if (!empty($options_to_delete)) {
		foreach ($options_to_delete as $option) {
			delete_option($option);
		}
	}
	$emd_activated_plugins = get_option('emd_activated_plugins');
	if (!empty($emd_activated_plugins)) {
		$emd_activated_plugins = array_diff($emd_activated_plugins, Array(
			'yt-scase-pro'
		));
		update_option('emd_activated_plugins', $emd_activated_plugins);
	}
}
if (is_multisite()) {
	global $wpdb;
	$blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);
	if ($blogs) {
		foreach ($blogs as $blog) {
			switch_to_blog($blog['blog_id']);
			yt_scase_pro_uninstall();
		}
		restore_current_blog();
	}
} else {
	yt_scase_pro_uninstall();
}
