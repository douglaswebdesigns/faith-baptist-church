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
 * Emd_Comment_Data Class
 *
 *
 * @since WPAS 4.0
 */
class Emd_Comment_Data {
	var $app = '';
	var $comment_type = '';
	var $parent_post_type = '';
	var $capability = 'moderate_comments';
	var $single_label = '';
	var $plural_label = '';
	var $unset_edit = 1;
	var $unset_trash = 0;
	var $unset_spam = 0;
	var $display_type = 'shc';
	/**
	 * Set comment data and add filters and actions
	 * @since WPAS 4.0
	 *
	 * @param string $comment_type
	 * @param array $args
	 */
	function __construct($comment_type, $args = array()) {
		if (!empty($args)) {
			$args = (object)$args;
			$this->app = $args->app;
			$this->comment_type = $comment_type;
			$this->parent_post_type = $args->parent_post_type;
			$this->capability = $args->capability;
			$this->single_label = $args->single_label;
			$this->plural_label = $args->plural_label;
			$this->unset_trash = $args->unset_trash;
			$this->unset_spam = $args->unset_spam;
			$this->display_type = $args->display_type;
		}
		add_action('admin_init',array($this,'allow_comment_tags'));
		add_filter('preprocess_comment', array(&$this,
			'preprocess_comment_types'
		));
		add_filter('views_' . $this->parent_post_type . '_page_' . $this->comment_type . '-page', array(
			$this,
			'modify_view_comments'
		));
		if ($this->display_type == 'backend') {
			add_filter('comments_template', array(
				$this,
				'empty_comments_template'
			));
			add_filter('comments_open', array(
				$this,
				'comment_types_open'
			) , 10, 2);
		} elseif ($this->display_type == 'shc') {
			add_filter('comments_template', array(
				$this,
				'empty_comments_template'
			));
		}
	}
	/**
	*  Add quicktags in allow comment tags for all users who can edit comments in backend
	* @since WPAS 4.2
	*
	*/
	public function allow_comment_tags(){
		global $allowedtags;
		$allowedtags['ul'] = Array();
		$allowedtags['ol'] = Array();
		$allowedtags['li'] = Array();
		$allowedtags['ins'] = Array("datetime"=> true);
		$allowedtags['img'] = Array("src"=> true, "alt" => true);
	}
	/**
	 * Update links on top (all, pending,approved, spam and trash) unset if requested
	 * @since WPAS 4.0
	 *
	 * @param array $views
	 *
	 * @return array $views
	 */
	function modify_view_comments($views) {
		foreach ($views as $kview => $view) {
			if ($kview == 'trash' && $this->unset_trash == 1) {
				unset($views[$kview]);
			} elseif ($kview == 'spam' && $this->unset_spam == 1) {
				unset($views[$kview]);
			} else {
				$view = str_replace('edit-comments.php?comment_type=' . $this->comment_type, 'edit.php?post_type=' . $this->parent_post_type . '&page=' . $this->comment_type . '-page', $view);
				$views[$kview] = $view;
			}
		}
		return $views;
	}
	/**
	 * Add comment type when saving
	 * @since WPAS 4.0
	 *
	 * @param array $commentdata
	 *
	 * @return array $commentdata
	 */
	function preprocess_comment_types($commentdata) {
		if ($this->parent_post_type != get_post_type($commentdata['comment_post_ID'])) {
			return $commentdata;
		}
		$commentdata['comment_type'] = $this->comment_type;
		return $commentdata;
	}
	/**
	 * Display comment type submenu page
	 * @since WPAS 4.0
	 *
	 *
	 * @return html
	 */
	function comment_page() {
		global $post_id;
		$emd_comments_table = new Emd_Comments_List_Table($this);
		$emd_comments_table->prepare_items();
		wp_enqueue_script('admin-comments');
		enqueue_comment_hotkeys_js();
		if ($post_id) {
			$title = sprintf($this->plural_label . ' on &#8220;%s&#8221;', wp_html_excerpt(_draft_or_post_title($post_id) , 50));
		} else {
			$title = $this->plural_label;
		}
?>
		<div class="wrap">
			<div id="icon-users" class="icon32"><br/></div>
			<h2><?php echo $title; ?></h2>
			<?php $emd_comments_table->views(); ?>
			<form id="emd-comments-form" method="get">
		<?php
		$emd_comments_table->search_box('Search ' . $this->plural_label, 'comment');
		$post_type = isset($_REQUEST['post_type']) ? $_REQUEST['post_type'] : '';
		if (!empty($post_type)) {
?>
			<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>" />
		<?php
		}
		if ($post_id): ?>
			<input type="hidden" name="p" value="<?php echo esc_attr(intval($post_id)); ?>" />
		<?php
		endif; ?>
			<input type="hidden" name="pagegen_timestamp" value="<?php echo esc_attr(current_time('mysql', 1)); ?>" />

			<input type="hidden" name="_total" value="<?php echo esc_attr($emd_comments_table->get_pagination_arg('total_items')); ?>" />
			<input type="hidden" name="_per_page" value="<?php echo esc_attr($emd_comments_table->get_pagination_arg('per_page')); ?>" />
			<input type="hidden" name="_page" value="<?php echo esc_attr($emd_comments_table->get_pagination_arg('page')); ?>" />

		<?php if (isset($_REQUEST['paged'])) { ?>
			<input type="hidden" name="paged" value="<?php echo esc_attr(absint($_REQUEST['paged'])); ?>" />
		<?php
		} ?>
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $emd_comments_table->display() ?>
		</form>
		</div>
		<div id="ajax-response"></div>
		<?php
		wp_comment_reply('-1', true, 'detail');
		wp_comment_trashnotice();
	}
	/**
	 * Process bulk action
	 * @since WPAS 4.0
	 *
	 *
	 */
	function process_bulk_action() {
		global $wpdb;
		$emd_comments_table = new Emd_Comments_List_Table($this);
		$doaction = $emd_comments_table->current_action();
		if ($doaction) {
			check_admin_referer('bulk-comments');
			$pagenum = $emd_comments_table->get_pagenum();
			if ('delete_all' == $doaction && !empty($_REQUEST['pagegen_timestamp'])) {
				$comment_status = wp_unslash($_REQUEST['comment_status']);
				$delete_time = wp_unslash($_REQUEST['pagegen_timestamp']);
				$comment_ids = $wpdb->get_col($wpdb->prepare("SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = %s AND %s > comment_date_gmt", $comment_status, $delete_time));
				$doaction = 'delete';
			} elseif (isset($_REQUEST['delete_comments'])) {
				$comment_ids = $_REQUEST['delete_comments'];
				$doaction = ($_REQUEST['action'] != - 1) ? $_REQUEST['action'] : $_REQUEST['action2'];
			} elseif (isset($_REQUEST['ids'])) {
				$comment_ids = array_map('absint', explode(',', $_REQUEST['ids']));
			} elseif (wp_get_referer()) {
				wp_safe_redirect(wp_get_referer());
				exit;
			}
			$approved = $unapproved = $spammed = $unspammed = $trashed = $untrashed = $deleted = 0;
			$redirect_to = remove_query_arg(array(
				'trashed',
				'untrashed',
				'deleted',
				'spammed',
				'unspammed',
				'approved',
				'unapproved',
				'ids'
			) , wp_get_referer());
			$redirect_to = esc_url(add_query_arg('paged', $pagenum, $redirect_to));
			foreach ($comment_ids as $comment_id) { // Check the permissions on each
				if (!current_user_can('edit_comment', $comment_id)) continue;
				switch ($doaction) {
					case 'approve':
						wp_set_comment_status($comment_id, 'approve');
						$approved++;
					break;
					case 'unapprove':
						wp_set_comment_status($comment_id, 'hold');
						$unapproved++;
					break;
					case 'spam':
						wp_spam_comment($comment_id);
						$spammed++;
					break;
					case 'unspam':
						wp_unspam_comment($comment_id);
						$unspammed++;
					break;
					case 'trash':
						wp_trash_comment($comment_id);
						$trashed++;
					break;
					case 'untrash':
						wp_untrash_comment($comment_id);
						$untrashed++;
					break;
					case 'delete':
						wp_delete_comment($comment_id);
						$deleted++;
					break;
				}
			}
			if ($approved) $redirect_to = esc_url(add_query_arg('approved', $approved, $redirect_to));
			if ($unapproved) $redirect_to = esc_url(add_query_arg('unapproved', $unapproved, $redirect_to));
			if ($spammed) $redirect_to = esc_url(add_query_arg('spammed', $spammed, $redirect_to));
			if ($unspammed) $redirect_to = esc_url(add_query_arg('unspammed', $unspammed, $redirect_to));
			if ($trashed) $redirect_to = esc_url(add_query_arg('trashed', $trashed, $redirect_to));
			if ($untrashed) $redirect_to = esc_url(add_query_arg('untrashed', $untrashed, $redirect_to));
			if ($deleted) $redirect_to = esc_url(add_query_arg('deleted', $deleted, $redirect_to));
			if ($trashed || $spammed) $redirect_to = esc_url(add_query_arg('ids', join(',', $comment_ids) , $redirect_to));
			wp_safe_redirect($redirect_to);
			exit;
		} elseif (!empty($_GET['_wp_http_referer'])) {
			wp_redirect(esc_url(remove_query_arg(array(
				'_wp_http_referer',
				'_wpnonce'
			) , wp_unslash($_SERVER['REQUEST_URI']))));
			exit;
		}
	}
	/**
	 * Displays comment meta box in entity page
	 * @since WPAS 4.0
	 *
	 * @param object $post
	 *
	 */
	function display_comment_meta_box($post) {
		global $wpdb;
		wp_nonce_field('get-comments', 'add_comment_nonce', false);
?>
		<p class="hide-if-no-js" id="add-new-comment">
		<a class="button" href="#commentstatusdiv" onclick="commentReply.addcomment(<?php echo $post->ID; ?>);return false;"><?php _e('Add ' . $this->single_label); ?></a>
		</p>
		<?php
		$total = get_comments(array(
			'post_id' => $post->ID,
			'count' => true,
			'type' => $this->comment_type
		));
		$emd_list_table = _get_list_table('WP_Post_Comments_List_Table');
		$emd_list_table->display(true);
		if (1 > $total) {
			echo '<p id="no-comments">No ' . $this->plural_label . ' yet.</p>';
		} else {
			$hidden = get_hidden_meta_boxes(get_current_screen());
			if (!in_array('commentsdiv', $hidden)) {
?>
		<script type="text/javascript">jQuery(document).ready(function(){commentsBox.get(<?php echo $total; ?>, 10);});</script>
		<?php
			}
?>
		<p class="hide-if-no-js" id="show-comments"><a href="#commentstatusdiv" onclick="commentsBox.get(<?php echo $total; ?>);return false;"><?php echo 'Show ' . $this->plural_label; ?></a> <span class="spinner"></span></p>
		<?php
		}
		wp_comment_trashnotice();
	}
	/**
	 * Empty comments template
	 * @since WPAS 4.0
	 *
	 * @param string $file
	 *
	 * @return string $file
	 */
	function empty_comments_template($file) {
		global $post;
		if ($post->post_type == $this->parent_post_type) {
			$file = dirname(__FILE__) . '/emd-no-comments.php';
		}
		return $file;
	}
	/**
	 * Close regular comments for post types with custom comments
	 * @since WPAS 4.0
	 *
	 * @param boolean $open
	 * @param int $post_id
	 *
	 * @return boolean $open
	 */
	function comment_types_open($open, $post_id) {
		if (get_post_type($post_id) == $this->parent_post_type) {
			return false;
		}
		return $open;
	}
}
