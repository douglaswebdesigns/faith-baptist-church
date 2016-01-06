<?php
/**
 * Enqueue Scripts Functions
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Enqueue js for admin edit/add new entity pages
 * @since WPAS 4.0
 */
function yt_scase_pro_load_tabs_scripts() {
	wp_enqueue_script('accordion');
}
add_action('admin_enqueue_scripts', 'yt_scase_pro_load_admin_enq');
/**
 * Enqueue style and js for each admin entity pages and settings
 *
 * @since WPAS 4.0
 * @param string $hook
 *
 */
function yt_scase_pro_load_admin_enq($hook) {
	global $typenow;
	if ($hook == 'edit-tags.php') {
		return;
	}
	if ($hook == 'toplevel_page_yt_scase_pro' || $hook == 'video-settings_page_yt_scase_pro_notify') {
		wp_enqueue_script('accordion');
		return;
	} else if (in_array($hook, Array(
		'video-settings_page_yt_scase_pro_store',
		'video-settings_page_yt_scase_pro_designs',
		'video-settings_page_yt_scase_pro_support'
	))) {
		wp_enqueue_style('admin-tabs', YT_SCASE_PRO_PLUGIN_URL . 'assets/css/admin-store.css');
		return;
	}
	if (isset($_GET['post_type']) && $hook == $_GET['post_type'] . '_page_operations_' . $_GET['post_type']) {
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script("jquery-ui-dialog");
		wp_enqueue_style('jq-css', YT_SCASE_PRO_PLUGIN_URL . 'assets/css/smoothness-jquery-ui.css');
		wp_enqueue_script('jquery-ui-timepicker', YT_SCASE_PRO_PLUGIN_URL . 'assets/ext/emd-meta-box/js/jqueryui/jquery-ui-timepicker-addon.js', array(
			'jquery-ui-datepicker',
			'jquery-ui-slider'
		) , YT_SCASE_PRO_VERSION, true);
		$oper_vars['dialog_title'] = __('Delete selected data?', 'yt-scase-pro');
		$oper_vars['btnTxt'] = __('Reset Data', 'yt-scase-pro');
		$oper_vars['cancel'] = __('Cancel', 'yt-scase-pro');
		$oper_vars['dialog'] = __('These items will be permanently deleted and cannot be recovered. Are you sure?', 'yt-scase-pro');
		wp_enqueue_script("operations-js", YT_SCASE_PRO_PLUGIN_URL . 'includes/admin/operations/operations.js');
		wp_localize_script("operations-js", 'oper_vars', $oper_vars);
		return;
	}
	if (in_array($typenow, Array(
		'emd_video'
	))) {
		$theme_changer_enq = 1;
		$datetime_enq = 0;
		$date_enq = 0;
		$sing_enq = 0;
		$tab_enq = 0;
		if ($hook == 'post.php' || $hook == 'post-new.php') {
			$unique_vars['msg'] = __('Please enter a unique value.', 'yt-scase-pro');
			$unique_vars['reqtxt'] = __('required', 'yt-scase-pro');
			$unique_vars['app_name'] = 'yt_scase_pro';
			$ent_list = get_option('yt_scase_pro_ent_list');
			if (!empty($ent_list[$typenow])) {
				$unique_vars['keys'] = $ent_list[$typenow]['unique_keys'];
				if (!empty($ent_list[$typenow]['req_blt'])) {
					$unique_vars['req_blt_tax'] = $ent_list[$typenow]['req_blt'];
				}
			}
			$tax_list = get_option('yt_scase_pro_tax_list');
			if (!empty($tax_list[$typenow])) {
				foreach ($tax_list[$typenow] as $txn_name => $txn_val) {
					if ($txn_val['required'] == 1) {
						$unique_vars['req_blt_tax'][$txn_name] = Array(
							'hier' => $txn_val['hier'],
							'type' => $txn_val['type'],
							'label' => $txn_val['label'] . ' ' . __('Taxonomy', 'yt-scase-pro')
						);
					}
				}
			}
			wp_enqueue_script('unique_validate-js', YT_SCASE_PRO_PLUGIN_URL . 'assets/js/unique_validate.js', array(
				'jquery',
				'jquery-validate'
			) , YT_SCASE_PRO_VERSION, true);
			wp_localize_script("unique_validate-js", 'unique_vars', $unique_vars);
		} elseif ($hook == 'edit.php') {
			wp_enqueue_style('allview-css', YT_SCASE_PRO_PLUGIN_URL . '/assets/css/allview.css');
		}
		if ($datetime_enq == 1) {
			wp_enqueue_script("jquery-ui-timepicker", YT_SCASE_PRO_PLUGIN_URL . 'assets/ext/emd-meta-box/js/jqueryui/jquery-ui-timepicker-addon.js', array(
				'jquery-ui-datepicker',
				'jquery-ui-slider'
			) , YT_SCASE_PRO_VERSION, true);
			$tab_enq = 1;
		} elseif ($date_enq == 1) {
			wp_enqueue_script("jquery-ui-datepicker");
			$tab_enq = 1;
		}
	}
}
add_action('wp_enqueue_scripts', 'yt_scase_pro_frontend_scripts');
/**
 * Enqueue style and js for each frontend entity pages and components
 *
 * @since WPAS 4.0
 *
 */
function yt_scase_pro_frontend_scripts() {
	$dir_url = YT_SCASE_PRO_PLUGIN_URL;
	if (is_page()) {
		$grid_vars = Array();
		$local_vars['ajax_url'] = admin_url('admin-ajax.php');
		$wpas_shc_list = get_option('yt_scase_pro_shc_list');
		$check_content = "";
		if (!is_author() && !is_tax()) {
			$check_content = get_post(get_the_ID())->post_content;
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_items')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
			wp_enqueue_style('allview-css', YT_SCASE_PRO_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_indicators')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
			wp_enqueue_style('allview-css', YT_SCASE_PRO_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_grid')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
			wp_enqueue_script('video-grid-js', $dir_url . 'assets/js/video-grid.js');
			wp_enqueue_style('allview-css', YT_SCASE_PRO_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_wall')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
			wp_enqueue_script('video-wall-js', $dir_url . 'assets/js/video-wall.js');
			wp_enqueue_style('allview-css', YT_SCASE_PRO_PLUGIN_URL . '/assets/css/allview.css');
		}
		if (!empty($check_content) && has_shortcode($check_content, 'video_gallery')) {
			wp_enqueue_script('jquery');
			wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
			wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
			wp_enqueue_script('video-gallery-js', $dir_url . 'assets/js/video-gallery.js');
			if (!empty($wpas_shc_list['integrations']['video_gallery']['datagrids'])) {
				$datagrids = $wpas_shc_list['integrations']['video_gallery']['datagrids'];
				foreach ($datagrids as $myint_dgrid) {
					$grid_vars[] = Emd_Datagrid::emd_get_gridvars('yt_scase_pro', $myint_dgrid);
				}
			}
			wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		}
		return;
	}
	if (is_post_type_archive('emd_video')) {
		wp_enqueue_script('jquery');
		wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
		wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
		wp_enqueue_script('video-archives-js', $dir_url . 'assets/js/video-archives.js');
		wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		return;
	}
	if (is_tax('video_tag')) {
		wp_enqueue_script('jquery');
		wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
		wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
		wp_enqueue_script('video-tags-js', $dir_url . 'assets/js/video-tags.js');
		wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		return;
	}
	if (is_tax('video_category')) {
		wp_enqueue_script('jquery');
		wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
		wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
		wp_enqueue_script('video-categories-js', $dir_url . 'assets/js/video-categories.js');
		wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		return;
	}
	if (is_single() && get_post_type() == 'emd_video') {
		wp_enqueue_script('jquery');
		wp_enqueue_style('boot', $dir_url . 'assets/ext/wpas/wpas-bootstrap.min.css');
		wp_enqueue_script('boot-js', $dir_url . 'assets/ext/wpas/bootstrap.min.js');
		wp_enqueue_script('single-video-js', $dir_url . 'assets/js/single-video.js');
		wp_enqueue_style('allview-css', $dir_url . '/assets/css/allview.css');
		return;
	}
}
