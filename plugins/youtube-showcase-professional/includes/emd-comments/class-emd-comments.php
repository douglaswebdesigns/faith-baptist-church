<?php
/**
 * Emd Comments
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
require_once (ABSPATH . 'wp-admin/includes/class-wp-comments-list-table.php');
require_once (plugin_dir_path(__FILE__) . 'class-emd-comments-list-table.php');
require_once (plugin_dir_path(__FILE__) . 'class-emd-comment-data.php');
/**
 * Emd_Comments Class
 * Adds a custom comment type and page to any custom post type by registering it to the post type
 *
 * @since WPAS 4.0
 */
class Emd_Comments {
	public static $emd_types = array();
	public $app;
	/**
	 * Initialize class
	 * Adds filters and actions
	 * @since WPAS 4.0
	 * @param string $app
	 */
	public function __construct($app = '') {
		$this->app = str_replace("-", "_", $app);
		add_action('admin_menu', array(
			$this,
			'change_comment_menus'
		),8);
		add_action('add_meta_boxes', array(
			$this,
			'emd_rename_meta_boxes'
		));
		add_filter('comment_row_actions', array(
			$this,
			'emd_comment_actions'
		) , 15, 2);
		add_filter('pre_get_comments', array(
			$this,
			'emd_pre_get_comments'
		) , 10, 2);
		add_filter('wp_count_comments', array(
			$this,
			'count_comments'
		));
		add_filter('wp_comment_reply', array(
			$this,
			'emd_comment_reply'
		) , 10, 2);
		add_filter('comment_form_field_comment', array(
			$this,
			'comment_form_field_comment_type'
		));
		add_action('wp_enqueue_scripts', array(
			$this,
			'enqueue_emd_comments'
		));
		add_action('wp_ajax_get_comment_type_page', array(
			$this,
			'get_comment_type_page'
		));
		add_action('wp_ajax_nopriv_get_comment_type_page', array(
			$this,
			'get_comment_type_page'
		));
		add_filter('get_avatar_comment_types', array(
			$this,
			'get_comment_list'
		));
		add_shortcode('emd_comments', array(
			$this,
			'comments_template'
		));
		add_shortcode('emd_comments_count', array(
			$this,
			'comments_count_shc'
		));
		add_action('comment_post', array(
			$this,
			'add_last_comment_filter'
		) , 10, 2);
		add_action('comment_form_logged_in_after', array(
			$this,
			'add_private_fields'
		));
                add_action('comment_form_after_fields', array(
			$this,
			'add_private_fields'
		));
                add_filter('comments_array', array(
			$this,
			'restrict_comments'
		),10,2);
	}
	/**
	 * Hide private comments to nonauthor of ticket, admins see all hidden
	 * @since WPAS 4.4
	 *
	 * @param array $comments
	 * @param int $post_id
	 *
	 * @return array $comments
	 */
	public function restrict_comments($comments , $post_id){
		global $post;
		$new_comments = Array();
		$user = wp_get_current_user();
		foreach($comments as $comm){
			if(!get_comment_meta($comm->comment_ID, 'private') == 'yes'){
				$new_comments[] = $comm;
				continue;
			}
			$comm->private = 1;
			if(($user->ID != 0 && ($user->ID == $comm->user_id || $post->post_author == $user->ID)) || is_super_admin())
			{
				$new_comments[] = $comm;
			}
			else {
				$slabel = self::$emd_types[$comm->comment_type]->single_label;
				$comm->comment_content = sprintf(__('This %s is marked as private','wpas'),strtolower($slabel));
				$new_comments[] = $comm;
			}
		}
		return $new_comments;
	}
	/**
	 * Add private checkbox to reply form
	 * @since WPAS 4.4
	 *
	 */
	public function add_private_fields(){
		global $post;
		foreach (self::$emd_types as $ktype => $mytype) {
			if ($mytype->parent_post_type == $post->post_type) {
				$slabel = $mytype->single_label;
				break;
			}
		}
		echo '<p class="comment-form-private">'.
		  '<label for="private"><input id="private" name="private" type="checkbox" value="yes"  style="transform: scale(1.5);-webkit-transform: scale(1.5);margin:10px;"/>' . sprintf(__( 'Make this %s private','emd-plugins'),strtolower($slabel)) . '</label>'.
		  '</p>';
	}
	/**
	 * Add last comment metakey to use for filters in admin each time a comment is saved
	 * @since WPAS 4.3
	 *
	 * @param string $comment_id
	 * @param string $status
	 *
	 */
	public function add_last_comment_filter($comment_id,$status){
		$comment = get_comment($comment_id);
		$post = get_post($comment->comment_post_ID);
		$published_cap = get_post_type_object($post->post_type)->cap->edit_published_posts;

		if(current_user_can($published_cap)){
			$cur_user = wp_get_current_user();
			$myrole = implode(",",$cur_user->roles);
			//add last_comment metakey with value role name
			if ( ! update_post_meta ($comment->comment_post_ID, 'wpas_last_comment', $myrole) ) { 
				add_post_meta($comment->comment_post_ID, 'wpas_last_comment', $myrole, true );	
			}; 
		}
		else {
			//add last comment metakey with value other
			if ( ! update_post_meta ($comment->comment_post_ID, 'wpas_last_comment', 'other') ) { 
				add_post_meta($comment->comment_post_ID, 'wpas_last_comment', 'other', true );	
			}; 
		}
		if((isset($_POST['private'])) && ($_POST['private'] != '')){
			$private = wp_filter_nohtml_kses($_POST['private']);
			add_comment_meta($comment_id, 'private', $private);
		}
	}
	/**
	 * Get comment list from options
	 * @since WPAS 4.0
	 *
	 * @param object $comment
	 *
	 * @return array $comment_list
	 */
	public function get_comment_list($comment) {
		$comment_list = get_option($this->app . '_comment_list');
		return $comment_list;
	}
	/**
	 * Sets each comment type
	 * @since WPAS 4.0
	 *
	 * @param string $comment_type
	 * @param array $args
	 *
	 */
	public static function register_comment_type($comment_type, $args) {
		if (!in_array($comment_type, array_keys(self::$emd_types))) {
			$emd_defaults = array(
				'app' => '',
				'capability' => 'moderate_comments',
				'parent_post_type' => '',
				'single_label' => '',
				'plural_label' => '',
				'unset_edit' => 1,
				'unset_trash' => 0,
				'unset_spam' => 0,
				'display_type' => 'shc',
			);
			$args = wp_parse_args($args, $emd_defaults);
			self::$emd_types[$comment_type] = new Emd_Comment_Data($comment_type, $args);
		}
	}
	/**
	 * Changes comment link and add submenu page for custom comments
	 * @since WPAS 4.0
	 *
	 */
	public function change_comment_menus() {
		global $menu;
		$menu[25][2] = 'edit-comments.php?comment_type=comment';
		foreach (self::$emd_types as $ktype => $mytype) {
			if($mytype->app == $this->app){
				$awaiting_mod = $this->count_comments(Array(),$ktype);
				$awaiting_mod = $awaiting_mod->moderated;
				$new_page = add_submenu_page('edit.php?post_type=' . $mytype->parent_post_type, $mytype->plural_label, sprintf($mytype->plural_label . '%s', "<span class='awaiting-mod count-$awaiting_mod'><span class='pending-count'>" . number_format_i18n($awaiting_mod) . "</span></span>") , $mytype->capability, $ktype . '-page', array(
					$mytype,
					'comment_page'
				));
				add_action('load-' . $new_page, array(
					$mytype,
					'process_bulk_action'
				) , 99);
			}
		}
	}
	/**
	 * For custom comment types, remove comment metabox and display new one
	 * @since WPAS 4.0
	 *
	 */
	public function emd_rename_meta_boxes() {
		global $post;
		foreach (self::$emd_types as $ktype => $mytype) {
			if (isset($post) && ('publish' == $post->post_status || 'private' == $post->post_status) && $post->post_type == $mytype->parent_post_type) {
				remove_meta_box('commentsdiv', $mytype->parent_post_type, 'normal');
				add_meta_box('commentsdiv', $mytype->plural_label, array(
					$mytype,
					'display_comment_meta_box'
				) , $mytype->parent_post_type, 'normal');
			}
		}
	}
	/**
	 * Reply custom comment type
	 * @since WPAS 4.0
	 *
	 * @param string $str
	 * @param array $input
	 *
	 * @return string $content html
	 */
	public function emd_comment_reply($str, $input) {
		extract($input);
		$content = "";
		$table_row = true;
		$screen = get_current_screen();
		foreach (self::$emd_types as $ktype => $mytype) {
			$id = $mytype->parent_post_type . "_page_" . $mytype->comment_type . "-page";
			$label = $mytype->single_label;
			if ($screen->id == $id) {
				$mode = 'detail';
				break;
			} elseif ($screen->id == $mytype->parent_post_type) {
				$mode = 'single';
				break;
			} else {
				return;
			}
		}
		if ($mode == 'single') {
			$emd_list_table = _get_list_table('WP_Post_Comments_List_Table');
		} else {
			$emd_list_table = _get_list_table('WP_Comments_List_Table');
		}
		ob_start();
		$quicktags_settings = array(
			'buttons' => 'strong,em,link,block,del,ins,img,ul,ol,li,code,close'
		);
		wp_editor('', 'replycontent', array(
			'media_buttons' => false,
			'tinymce' => false,
			'quicktags' => $quicktags_settings,
			'tabindex' => 104
		));
		$editorStr = ob_get_contents();
		ob_end_clean();
		ob_start();
		wp_nonce_field('replyto-comment', '_ajax_nonce-replyto-comment', false);
		if (current_user_can('unfiltered_html')) {
			wp_nonce_field('unfiltered-html-comment', '_wp_unfiltered_html_comment', false);
		}
		$nonceStr = ob_get_contents();
		ob_end_clean();
		$content.= '<form method="get" action="">';
		if ($table_row) {
			$content.= '<table style="display:none;"><tbody id="com-reply"><tr id="replyrow" style="display:none;"><td colspan="' . $emd_list_table->get_column_count() . '" class="colspanchange">';
		} else {
			$content.= '<div id="com-reply" style="display:none;"><div id="replyrow" style="display:none;">';
		}
		$content.= '<div id="replyhead" style="display:none;"><h5>' . __('Reply to', 'emd-plugins') . ' ' . $label . '</h5></div>
			<div id="addhead" style="display:none;"><h5>' . __('Add new', 'emd-plugins') . ' ' . $label . '</h5></div>
			<div id="edithead" style="display:none;">
			<div class="inside">
			<label for="author">' . __('Name', 'emd-plugins') . '</label>
			<input type="text" name="newcomment_author" size="50" value="" id="author" />
			</div>

			<div class="inside">
			<label for="author-email">' . __('E-mail', 'emd-plugins') . '</label>
			<input type="text" name="newcomment_author_email" size="50" value="" id="author-email" />
			</div>

			<div class="inside">
			<label for="author-url">' . __('URL', 'emd-plugins') . '</label>
			<input type="text" id="author-url" name="newcomment_author_url" size="103" value="" />
			</div>
			<div style="clear:both;"></div>
			</div>
			<div id="replycontainer">';
		$content.= $editorStr;
		$content.= '</div>
			<p id="replysubmit" class="submit">
			<a href="#comments-form" class="save button-primary alignright">
			<span id="addbtn" style="display:none;">' . __('Add', 'emd-plugins') . ' ' . $label . '</span>
			<span id="savebtn" style="display:none;">' . __('Update', 'emd-plugins') . ' ' . $label . '</span>
			<span id="replybtn" style="display:none;">' . __('Submit', 'emd-plugins') . ' ' . $label . '</span></a>
			<a href="#comments-form" class="cancel button-secondary alignleft">' . __('Cancel', 'emd-plugins') . '</a>
			<span class="waiting spinner"></span>
			<span class="error" style="display:none;"></span>
			<br class="clear" />
			</p>
			<input type="hidden" name="user_ID" id="user_ID" value="' . get_current_user_id() . '" />
			<input type="hidden" name="action" id="action" value="replyto-comment" />
			<input type="hidden" name="comment_ID" id="comment_ID" value="" />
			<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="" />
			<input type="hidden" name="status" id="status" value="" />
			<input type="hidden" name="position" id="position" value="' . $position . '" />
			<input type="hidden" name="checkbox" id="checkbox" value="';
		if ($checkbox) $content.= '1';
		else $content.= '0';
		$content.= '\"><input type="hidden" name="mode" id="mode" value="' . esc_attr($mode) . '" />';
		$content.= $nonceStr;
		if ($table_row) {
			$content.= '</td></tr></tbody></table>';
		} else {
			$content.= '</div></div>';
		}
		$content.= '</form>';
		return $content;
	}
	/**
	 * Custom comment type actions
	 * @since WPAS 4.0
	 *
	 * @param array $actions
	 * @param object $comment
	 *
	 * @return array $actions
	 */
	public function emd_comment_actions($actions, $comment) {
		foreach (self::$emd_types as $ktype => $mytype) {
			if ($comment->comment_type == $mytype->comment_type) {
				if ($mytype->unset_trash == 1) {
					unset($actions['trash']);
				}
				if ($mytype->unset_spam == 1) {
					unset($actions['spam']);
				}
				if ($mytype->unset_edit == 1) {
					unset($actions['edit']);
				}
				$check_cap = 'manage_' . $comment->comment_type . '_' . get_post_type($comment->comment_post_ID) . 's';
				if ($comment->user_id != get_current_user_id()) {
					unset($actions['quickedit']);
					if (!current_user_can($check_cap)) {
						unset($actions['trash']);
					}
				}
				if ($comment->user_id == get_current_user_id()) {
					unset($actions['reply']);
				}
				if (!current_user_can($check_cap)) {
					unset($actions['approve']);
					unset($actions['unapprove']);
				}
			}
		}
		return $actions;
	}
	/**
	 * Add type comment to wp query args
	 * @since WPAS 4.0
	 *
	 * @param object $query
	 *
	 * @return object $query
	 */
	public function emd_pre_get_comments($query) {
		global $pagenow, $post;
		if ($pagenow != 'edit.php' && empty($query->query_vars['type']) && !defined('DOING_AJAX')) {
			foreach (self::$emd_types as $ktype => $mytype) {
				if ($mytype->parent_post_type == $post->post_type) {
					$query->query_vars['type'] = $ktype;
					break;
				}
			}
		}
		return $query;
	}
	/**
	 * Count comments
	 * @since WPAS 4.0
	 *
	 * @param array $stats
	 * @param int $mypage
	 *
	 * @return array $stats
	 */
	public function count_comments($stats, $mypage = 0) {
		if (is_admin()) {
			global $wpdb;
			$screen = get_current_screen();
			if (empty($screen) && $mypage != 0) {
				$mypage = 0;
			} else {
				$ctype = $mypage;
			}
			if (empty($mypage)) {
				$mypage = 0;
			}
			foreach (self::$emd_types as $ktype => $mytype) {
				$ids[$ktype] = $mytype->parent_post_type . "_page_" . $ktype . "-page";
			}
			if (!empty($screen)) {
				if ($screen->id == 'edit-comments') {
					$mypage = '0';
					$ctype = "";
				} elseif (in_array($screen->id, $ids)) {
					$mypage = array_search($screen->id, $ids);
					$ctype = $mypage;
				}
				$count = wp_cache_get("comments-{$mypage}", 'counts');
				if (false !== $count) {
					return $count;
				}
			}
			$count = $wpdb->get_results("SELECT comment_approved, comment_type, COUNT( * ) AS num_comments FROM {$wpdb->comments} GROUP BY comment_approved, comment_type", ARRAY_A);
			$total = 0;
			$approved = array(
				'0' => 'moderated',
				'1' => 'approved',
				'spam' => 'spam',
				'trash' => 'trash',
				'post-trashed' => 'post-trashed'
			);
			foreach ((array)$count as $row) {
				// Don't count post-trashed toward totals
				if ('post-trashed' != $row['comment_approved'] && 'trash' != $row['comment_approved']) $total+= $row['num_comments'];
				if (isset($approved[$row['comment_approved']])) {
					if ($row['comment_type'] == "") {
						$new_stats['0'][$approved[$row['comment_approved']]] = $row['num_comments'];
						$new_stats['0']['total_comments'] = $total;
					} else {
						$new_stats[$row['comment_type']][$approved[$row['comment_approved']]] = $row['num_comments'];
						$new_stats[$row['comment_type']]['total_comments'] = $total;
					}
				}
			}
			$new_stats[$mypage]['total_comments'] = $total;
			foreach ($approved as $key) {
				if (empty($new_stats[$mypage][$key])) $new_stats[$mypage][$key] = 0;
			}
			$stats = (object)$new_stats[$mypage];
			wp_cache_set("comments-{$mypage}", $stats, 'counts');
			return $stats;
		}
	}
	/**
	 * Comments template on frontend
	 * @since WPAS 4.0
	 *
	 * @param array $atts
	 *
	 * @return $com html
	 */
	function comments_template($atts) {
		global $post, $emd_com_type;
		$theme = $atts['theme'] ? "Bootstrap" : "";
		//maybe pass in shortcode attr
		foreach (self::$emd_types as $ktype => $mytype) {
			if ($mytype->parent_post_type == $post->post_type) {
				$emd_com_type = $ktype;
				$slabel = $mytype->single_label;
				$plabel = $mytype->plural_label;
				break;
			}
		}
		$mycomments = get_comments(array(
			'post_id' => $post->ID,
			'type' => $emd_com_type,
			'status'=> 'approve'
		));
		$mycomments = apply_filters('comments_array',$mycomments,$post->ID);
		$comments_per_page = get_option('comments_per_page');
		$page = intval(get_query_var('cpage'));
		if (empty($page) && get_option('page_comments')) {
			$page = 'newest' == get_option('default_comments_page') ? get_comment_pages_count($mycomments) : 1;
			set_query_var('cpage', $page);
		}
		ob_start();
		echo '<section id="comments" class="comments-area">';
		echo '<input type="hidden" id="emd_comment_type" name="emd_comment_type" value="' . $emd_com_type . '" />';
		echo '<input type="hidden" id="emd_comment_theme" name="emd_comment_theme" value="' . $theme . '" />';
		$comment_count = get_comments(array(
			'post_id' => $post->ID,
			'type' => $emd_com_type,
			'status'=> 'approve',
			'count' => true
		));
		if ($comment_count > 0) {
			$mycomments[0]->latest = 1;
			echo '<h3 class="comments-title">';
			printf(_n('One %3$s on &ldquo;%2$s&rdquo;', '%1$s %4$s on &ldquo;%2$s&rdquo;', $comment_count, 'emd-plugins') , number_format_i18n($comment_count) , '<span>' . get_the_title() . '</span>', $slabel, $plabel);
			echo '</h3>';
			echo '<div id="emd-comment-list">';
			echo '<ol class="commentlist">';
			wp_list_comments(array(
				'callback' => Array(
					$this,
					'comment_callback'
				) ,
				'per_page' => $comments_per_page,
				'page' => $page,
				'style' => 'ol'
			) , $mycomments);
			echo '</ol>';
		}
		if (get_comment_pages_count($mycomments) > 1 && get_option('page_comments')) {
			// are there comments to navigate through
			echo "<div class='pagination-bar'>";
			$this->comment_paginate($theme, get_comment_pages_count($mycomments) , $page);
			echo '</div>';
		} elseif (!comments_open() && post_type_supports(get_post_type() , 'comments')) {
			echo '<p class="nocomments label label-danger">' . $plabel . __(' are closed.', 'emd-plugins') . '</p>';
		}
		if ($comment_count > 0) {
			echo '</div>';
		}
		$args['comment_notes_after'] = '';
		$args['label_submit'] = __('Post', 'emd-plugins') . ' ' . $slabel;
		$args['id_submit'] = 'comment-submit';
		comment_form($args);
		echo '</section> <!-- /#comments.comments-area -->';
		$comm = ob_get_contents();
		ob_end_clean();
		return $comm;
	}
	/**
	 * Comments template comment field
	 * @since WPAS 4.0
	 *
	 * @param string $comment_field
	 *
	 * @return string $comment_field
	 */
	function comment_form_field_comment_type($comment_field) {
		global $emd_com_type, $post;
		if (!empty($emd_com_type) && $post->post_type == self::$emd_types[$emd_com_type]->parent_post_type) {
			$comment_field = '<p class="comment-form-comment"><label for="comment">' . self::$emd_types[$emd_com_type]->plural_label . '<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>';
		}
		return $comment_field;
	}
	/**
	 * Comments template pagination
	 * @since WPAS 4.0
	 *
	 * @param string $theme
	 * @param int $page_count
	 * @param int $page
	 *
	 * @return string $paging_html
	 */
	function comment_paginate($theme = 'Bootstrap', $page_count, $page) {
		$paging = paginate_links(array(
			'base' => esc_url(add_query_arg('cpage', '%#%')) ,
			'total' => $page_count,
			'current' => $page,
			'format' => '%#%',
			'type' => 'array',
			'echo' => false,
			'add_fragment' => '#comments'
		));
		if ($theme == 'jQuery') {
			$paging_html = "<div class='nav-pages'>";
			foreach ($paging as $key_paging => $my_paging) {
				$paging_html.= "<div class='nav-item ui-state-default ui-corner-all";
				if (($page == 1 && $key_paging == 0) || ($page > 1 && $page == $key_paging)) {
					$paging_html.= " ui-state-highlight";
				}
				$paging_html.= ">" . $my_paging . "</div>";
			}
			$paging_html.= "</div>";
		} else {
			$paging_html = "<ul class='pagination'>";
			foreach ($paging as $key_paging => $my_paging) {
				$paging_html.= "<li";
				if (($page == 1 && $key_paging == 0) || ($page > 1 && $page == $key_paging)) {
					$paging_html.= " class='active'";
				}
				$paging_html.= ">" . $my_paging . "</li>";
			}
			$paging_html.= "</ul>";
		}
		echo $paging_html;
	}
	/**
	 * Comments template callback
	 * @since WPAS 4.0
	 *
	 * @param object $comment
	 * @param array $args
	 * @param int $depth
	 *
	 * @return string $paging_html
	 */
	function comment_callback($comment, $args, $depth) {
		switch ($comment->comment_type) {
			case 'pingback':
			case 'trackback':
				// Display trackbacks differently than normal comments
				
?>
					<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
					<article id="comment-<?php comment_ID(); ?>" class="pingback">
					<p><?php _e('Pingback:', 'emd-plugins'); ?> <?php comment_author_link(); ?> 
					<?php if (get_current_user_id() != 0 && $comment->user_id == get_current_user_id()) {
					edit_comment_link(__('Edit <span>&rarr;</span>', 'emd-plugins') , '<span class="edit-link pull-right btn btn-default">', '</span>');
				} ?>
				</p>
					</article> <!-- #comment-##.pingback -->
					<?php
			break;
			default:
				// Proceed with normal comments.
				global $post;
				$latest = "";
				$latest_div = "";
				$private_span = "";
				$private = "";
				if(isset($comment->private) && $comment->private == 1){
					$private = "private";
					$private_span = '<span class="private-comment"></span>';
				}
				if(isset($comment->latest) && $comment->latest == 1){
					$latest = "latest";
					$latest_div = "<div class='latest-comment-text badge'>" . __('Latest','emd-plugins') . "</div>";
				}
				?>
					<li <?php comment_class($latest); ?> id="li-comment-<?php comment_ID(); ?>">
					<article id="comment-<?php comment_ID(); ?>" class="comment well clearfix <?php echo $private;?>">
					<div class="edit-reply-links">
					<?php 
					echo $private_span;
					echo $latest_div;
					if (get_current_user_id() != 0 && $comment->user_id == get_current_user_id()) {
					edit_comment_link(__('Edit <span>&rarr;</span>', 'emd-plugins') , '<span class="edit-link pull-right btn btn-default">', '</span>');
				} ?>
				<div class="comment-reply pull-right">
					<?php if (get_current_user_id() == 0 || $comment->user_id != get_current_user_id()) {
					comment_reply_link(array_merge($args, array(
						'reply_text' => __('Reply <span>&darr;</span>', 'emd-plugins') ,
						'before' => '<span class="btn btn-default">',
						'after' => '</span>',
						'depth' => $depth,
						'max_depth' => $args['max_depth']
					)));
				} ?>
				</div> 
					</div> 
					<header class="comment-meta comment-author vcard">
					<?php
				echo get_avatar($comment, 44);
				printf('<cite class="fn">%1$s %2$s</cite>', get_comment_author_link() ,
				// If current post author is also comment author, make it known visually.
				($comment->user_id === $post->post_author) ? '<span class="label label-info"> ' . __('Author', 'emd-plugins') . '</span>' : '');
				printf('<a href="%1$s" title="Posted %2$s"><time itemprop="datePublished" datetime="%3$s">%4$s</time></a>', esc_url(get_comment_link($comment->comment_ID)) , sprintf(__('%1$s @ %2$s', 'emd-plugins') , get_comment_date() , get_comment_time()) , get_comment_time('c') ,
				/* Translators: 1: date, 2: time */
				sprintf(__('%1$s at %2$s', 'emd-plugins') , get_comment_date() , get_comment_time()));
?>
					</header> <!-- .comment-meta -->

					<?php if ('0' == $comment->comment_approved) { ?>
						<p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'emd-plugins'); ?></p>
							<?php
				} ?>
				<section class="comment-content comment">
					<?php comment_text(); ?>
					</section> <!-- .comment-content -->
					</article> <!-- #comment-## -->
					<?php
				break;
			} // end comment_type check
			
	}
	/**
	 * Enqueue css and js for comment
	 * @since WPAS 4.0
	 *
	 */
	function enqueue_emd_comments() {
		global $post;
		$show = 0;
		foreach (self::$emd_types as $ktype => $mytype) {
			if (is_single() && $mytype->parent_post_type == $post->post_type) {
				if ($mytype->display_type == 'shc') {
					$show = 1;
				}
				break;
			}
		}
		if ($show == 1) {
			wp_enqueue_style('emd-comment-css', plugin_dir_url(__FILE__) . 'emd-comments.min.css');
			wp_enqueue_script('jquery');
			wp_enqueue_script('emd-comment-js', plugin_dir_url(__FILE__) . 'emd-comments.js');
			$comment_vars['ajax_url'] = admin_url('admin-ajax.php');
			wp_localize_script('emd-comment-js', 'comment_vars', $comment_vars);
		}
	}
	/**
	 * Comment list on frontend
	 * @since WPAS 4.0
	 *
	 * @return html
	 */
	function get_comment_type_page() {
		$pageno = isset($_POST['pageno']) ? $_POST['pageno'] : 1;
		$post_id = isset($_POST['post_id']) ? $_POST['post_id'] : 0;
		$com_type = isset($_POST['com_type']) ? $_POST['com_type'] : '';
		$theme = isset($_POST['theme']) ? $_POST['theme'] : '';
		$comments_per_page = get_option('comments_per_page');
		if ($post_id == 0 || empty($com_type)) {
			die();
		}
		global $post;
		$post = get_post($post_id);
		$mycomments = get_comments(array(
			'post_id' => $post_id,
			'type' => $com_type,
			'status'=> 'approve',
		));
		$mycomments = apply_filters('comments_array',$mycomments,$post->ID);
		echo '<ol class="commentlist">';
		wp_list_comments(array(
			'callback' => Array(
				$this,
				'comment_callback'
			) ,
			'per_page' => $comments_per_page,
			'page' => $pageno,
			'style' => 'ol'
		) , $mycomments);
		echo '</ol>';
		if (get_comment_pages_count($mycomments) > 1 && get_option('page_comments')) {
			// are there comments to navigate through
			echo "<div class='pagination-bar'>";
			$this->comment_paginate($theme, get_comment_pages_count($mycomments) , $pageno);
			echo '</div>';
		}
		die();
	}
	/**
	 * Comment type count shc
	 * @since WPAS 4.0
	 *
	 * @return int stats approved
	 */
	function comments_count_shc() {
		global $post;
		$stats = '';
		foreach (self::$emd_types as $ktype => $mytype) {
			if ($post->post_type == $mytype->parent_post_type) {
				$stats = get_comments(array(
					'post_id' => $post->ID,
					'type' => $ktype,
					'status'=> 'approve',
					'count' => true
				));
				return $stats;
			}
		}
		return $stats;
	}
}
