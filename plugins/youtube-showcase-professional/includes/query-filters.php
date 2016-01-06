<?php
/**
 * Query Filter Functions
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Change query parameters before wp_query is processed
 *
 * @since WPAS 4.0
 * @param object $query
 *
 * @return object $query
 */
function yt_scase_pro_query_filters($query) {
	$has_limitby = get_option("yt_scase_pro_has_limitby_cap");
	if (!is_admin() && $query->is_main_query()) {
		if ($query->is_author || $query->is_search) {
			$query = emd_limit_author_search('yt_scase_pro', $query, $has_limitby);
		}
	}
	return $query;
}
add_action('pre_get_posts', 'yt_scase_pro_query_filters');
