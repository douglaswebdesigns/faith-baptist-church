<?php
/**
 * Emd Comments
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Comments_List_Table Class
 *
 * Extends WP_Comments_List_Table
 *
 * @since WPAS 4.0
 */
class Emd_Comments_List_Table extends WP_Comments_List_Table {
	private $comment_type;
	private $comment;
	/**
	 * Instantiate emd comments list class
	 * Set comment object
	 * @since WPAS 4.0
	 *
	 * @param object $comment
	 *
	 */
	function __construct($comment) {
		global $post_id;
		$this->comment = $comment;
		$post_id = isset($_REQUEST['p']) ? absint($_REQUEST['p']) : 0;
		if (get_option('show_avatars')) add_filter('comment_author', 'floated_admin_avatar');
		parent::__construct(array(
			'plural' => $comment->plural_label,
			'singular' => $comment->single_label,
			'ajax' => true,
		));
	}
	/**
	 * Overwrite no items in wp comments list table class
	 * @since WPAS 4.0
	 *
	 *
	 */
	function no_items() {
		global $comment_status;
		if ('moderated' == $comment_status) _e('No ' . strtolower($this->comment->plural_label) . ' awaiting moderation.');
		else _e('No ' . strtolower($this->comment->plural_label) . ' found.');
	}
	/**
	 * Overwrite prepare_items in wp comments list table class
	 * @since WPAS 4.0
	 *
	 *
	 */
	function prepare_items() {
		global $post_id, $comment_status, $search, $comment_type;
		$comment_status = isset($_REQUEST['comment_status']) ? $_REQUEST['comment_status'] : 'all';
		if (!in_array($comment_status, array(
			'all',
			'moderated',
			'approved',
			'spam',
			'trash'
		))) $comment_status = 'all';
		$comment_type = $this->comment->comment_type;
		$search = (isset($_REQUEST['s'])) ? $_REQUEST['s'] : '';
		$user_id = (isset($_REQUEST['user_id'])) ? $_REQUEST['user_id'] : '';
		$orderby = (isset($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : '';
		$order = (isset($_REQUEST['order'])) ? $_REQUEST['order'] : '';
		$comments_per_page = 20;
		$doing_ajax = defined('DOING_AJAX') && DOING_AJAX;
		if (isset($_REQUEST['number'])) {
			$number = (int)$_REQUEST['number'];
		} else {
			$number = $comments_per_page + min(8, $comments_per_page); // Grab a few extra
			
		}
		$page = $this->get_pagenum();
		if (isset($_REQUEST['start'])) {
			$start = $_REQUEST['start'];
		} else {
			$start = ($page - 1) * $comments_per_page;
		}
		if ($doing_ajax && isset($_REQUEST['offset'])) {
			$start+= $_REQUEST['offset'];
		}
		$status_map = array(
			'moderated' => 'hold',
			'approved' => 'approve'
		);
		$args = array(
			'status' => isset($status_map[$comment_status]) ? $status_map[$comment_status] : $comment_status,
			'search' => $search,
			'user_id' => $user_id,
			'offset' => $start,
			'number' => $number,
			'post_id' => $post_id,
			'type' => $comment_type,
			'orderby' => $orderby,
			'order' => $order,
		);
		$_comments = get_comments($args);
		update_comment_cache($_comments);
		$this->items = array_slice($_comments, 0, $comments_per_page);
		$this->extra_items = array_slice($_comments, $comments_per_page);
		$total_comments = get_comments(array_merge($args, array(
			'count' => true,
			'offset' => 0,
			'number' => 0
		)));
		$_comment_post_ids = array();
		foreach ($_comments as $_c) {
			$_comment_post_ids[] = $_c->comment_post_ID;
		}
		$_comment_post_ids = array_unique($_comment_post_ids);
		$this->pending_count = get_pending_comments_num($_comment_post_ids);
		$this->set_pagination_args(array(
			'total_items' => $total_comments,
			'per_page' => $comments_per_page,
		));
	}
	/**
	 * Overwrite get_columns in wp comments list table class
	 * @since WPAS 4.0
	 *
	 * @return array $columns
	 */
	function get_columns() {
		global $post_id;
		$columns = array();
		if ($this->checkbox) $columns['cb'] = '<input type="checkbox" />';
		$columns['author'] = __('Author','emd-plugins');
		$columns['comment'] = $this->comment->single_label;
		if (!$post_id) $columns['response'] = _x('In Response To', 'column name', 'emd-plugins');
		return $columns;
	}
	/**
	 * Overwrite comments_bubble in wp comments list table class
	 * @since WPAS 4.0
	 * @param int $post_id
	 * @param bool $pending_comments
	 *
	 * @return html
	 */
	function comments_bubble($post_id, $pending_comments) {
		$pending_phrase = sprintf(__('%s pending', 'emd-plugins') , number_format($pending_comments));
		if ($pending_comments) echo '<strong>';
		echo "<a href='" . esc_url(add_query_arg('p', $post_id, admin_url('edit.php?post_type=' . $this->comment->parent_post_type . '&page=' . $this->comment->comment_type . '-page'))) . "' title='" . esc_attr($pending_phrase) . "' class='post-com-count'><span class='comment-count'>" . number_format_i18n(get_comments_number()) . "</span></a>";
		if ($pending_comments) echo '</strong>';
	}
	/**
	 * Overwrite extra_tablenav in wp comments list table class
	 * @since WPAS 4.0
	 * @param string $which
	 */
	function extra_tablenav($which) {
		if ($which == 'top') {
		}
	}
}
