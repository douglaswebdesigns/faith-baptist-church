<?php
/**
 * Tab /Accordion functions for entity admin edit/add new
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('admin_enqueue_scripts', 'yt_scase_pro_load_tabs_scripts');
add_action('emd_mb_before_acc_emd_video_0', 'yt_scase_pro_show_acc_emd_video_0');
add_filter('emd_mb_emd_video_featured_begin_html', 'yt_scase_pro_begin_acc_emd_video_0_0');
add_filter('emd_mb_emd_video_duration_begin_html', 'yt_scase_pro_begin_acc_emd_video_0_1');
add_filter('emd_mb_emd_video_autohide_begin_html', 'yt_scase_pro_begin_acc_emd_video_0_2');
add_action('emd_mb_after_acc_emd_video_0', 'yt_scase_pro_show_end_acc_emd_video');
/**
 * Accordion show func
 * @since WPAS 4.0
 * @param string $begin
 *
 * @return html
 */
function yt_scase_pro_show_acc_emd_video_0() {
?>
<div id="acc_emd_video_0" class="accordion-container">
<ul class="outer-border">
<?php
}
/**
 * Accordion begin func
 * @since WPAS 4.0
 * @param string $begin
 *
 * @return html
 */
function yt_scase_pro_begin_acc_emd_video_0_0($begin) {
	$be0 = '<li id="acc_emd_video_0_0" class="control-section accordion-section open">
                  <h3 class="accordion-section-title">' . __('Identification', 'yt-scase-pro') . '</h3><div id="0" class="accordion-section-content">';
	return $be0 . $begin;
}
/**
 * Accordion begin func
 * @since WPAS 4.0
 * @param string $begin
 *
 * @return html
 */
function yt_scase_pro_begin_acc_emd_video_0_1($begin) {
	$be1 = '</div></li><li id="acc_emd_video_0_1" class="control-section accordion-section ">
                  <h3 class="accordion-section-title">' . __('Stats', 'yt-scase-pro') . '</h3><div id="0" class="accordion-section-content">';
	return $be1 . $begin;
}
/**
 * Accordion begin func
 * @since WPAS 4.0
 * @param string $begin
 *
 * @return html
 */
function yt_scase_pro_begin_acc_emd_video_0_2($begin) {
	$be2 = '</div></li><li id="acc_emd_video_0_2" class="control-section accordion-section ">
                  <h3 class="accordion-section-title">' . __('Player', 'yt-scase-pro') . '</h3><div id="0" class="accordion-section-content">';
	return $be2 . $begin;
}
/**
 * Accordion/tab end func
 * @since WPAS 4.0
 *
 * @return html
 */
function yt_scase_pro_show_end_acc_emd_video() {
?>
</div>
</li></ul></div>
<?php
}
