<?php
/**
 * Entity Class
 *
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
/**
 * Emd_Video Class
 * @since WPAS 4.0
 */
class Emd_Video extends Emd_Entity {
	protected $post_type = 'emd_video';
	protected $textdomain = 'yt-scase-pro';
	protected $sing_label;
	protected $plural_label;
	protected $menu_entity;
	/**
	 * Initialize entity class
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function __construct() {
		add_action('init', array(
			$this,
			'set_filters'
		));
		add_action('admin_init', array(
			$this,
			'include_tabs_acc'
		));
		add_filter('post_updated_messages', array(
			$this,
			'updated_messages'
		));
		add_action('admin_menu', array(
			$this,
			'add_menu_link'
		));
		add_action('admin_head-edit.php', array(
			$this,
			'add_opt_button'
		));
	}
	/**
	 * Register post type and taxonomies and set initial values for taxs
	 *
	 * @since WPAS 4.0
	 *
	 */
	public static function register() {
		$labels = array(
			'name' => __('Videos', 'yt-scase-pro') ,
			'singular_name' => __('Video', 'yt-scase-pro') ,
			'add_new' => __('Add New', 'yt-scase-pro') ,
			'add_new_item' => __('Add New Video', 'yt-scase-pro') ,
			'edit_item' => __('Edit Video', 'yt-scase-pro') ,
			'new_item' => __('New Video', 'yt-scase-pro') ,
			'all_items' => __('All Videos', 'yt-scase-pro') ,
			'view_item' => __('View Video', 'yt-scase-pro') ,
			'search_items' => __('Search Videos', 'yt-scase-pro') ,
			'not_found' => __('No Videos Found', 'yt-scase-pro') ,
			'not_found_in_trash' => __('No Videos Found In Trash', 'yt-scase-pro') ,
			'menu_name' => __('Videos', 'yt-scase-pro') ,
		);
		register_post_type('emd_video', array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'description' => __('YouTube video which may be displayed as single video, a collection of videos based on a video channel , custom playlist, or videos based on user defined search terms.', 'yt-scase-pro') ,
			'show_in_menu' => true,
			'menu_position' => 6,
			'has_archive' => true,
			'exclude_from_search' => false,
			'rewrite' => array(
				'slug' => 'videos'
			) ,
			'can_export' => true,
			'hierarchical' => false,
			'menu_icon' => 'dashicons-format-video',
			'map_meta_cap' => 'true',
			'taxonomies' => array() ,
			'capability_type' => 'emd_video',
			'supports' => array(
				'title',
				'editor',
				'excerpt',
				'comments'
			)
		));
		$video_category_hr_labels = array(
			'name' => __('Categories', 'yt-scase-pro') ,
			'singular_name' => __('Category', 'yt-scase-pro') ,
			'search_items' => __('Search Categories', 'yt-scase-pro') ,
			'all_items' => __('All', 'yt-scase-pro') ,
			'parent_item' => __('Parent Category', 'yt-scase-pro') ,
			'parent_item_colon' => __('Parent Category-COLON', 'yt-scase-pro') ,
			'edit_item' => __('Edit Category', 'yt-scase-pro') ,
			'update_item' => __('Update Category', 'yt-scase-pro') ,
			'add_new_item' => __('Add New Category', 'yt-scase-pro') ,
			'new_item_name' => __('Add New Category Name', 'yt-scase-pro') ,
			'menu_name' => __('Categories', 'yt-scase-pro') ,
		);
		register_taxonomy('video_category', array(
			'emd_video'
		) , array(
			'hierarchical' => true,
			'labels' => $video_category_hr_labels,
			'public' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_tagcloud' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'video_category'
			) ,
			'capabilities' => array(
				'manage_terms' => 'manage_video_category',
				'edit_terms' => 'edit_video_category',
				'delete_terms' => 'delete_video_category',
				'assign_terms' => 'assign_video_category'
			) ,
		));
		$video_tag_nohr_labels = array(
			'name' => __('Tags', 'yt-scase-pro') ,
			'singular_name' => __('Tag', 'yt-scase-pro') ,
			'search_items' => __('Search Tags', 'yt-scase-pro') ,
			'popular_items' => __('Popular Tags', 'yt-scase-pro') ,
			'all_items' => __('All', 'yt-scase-pro') ,
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __('Edit Tag', 'yt-scase-pro') ,
			'update_item' => __('Update Tag', 'yt-scase-pro') ,
			'add_new_item' => __('Add New Tag', 'yt-scase-pro') ,
			'new_item_name' => __('Add New Tag Name', 'yt-scase-pro') ,
			'separate_items_with_commas' => __('Seperate Tags with commas', 'yt-scase-pro') ,
			'add_or_remove_items' => __('Add or Remove Tags', 'yt-scase-pro') ,
			'choose_from_most_used' => __('Choose from the most used Tags', 'yt-scase-pro') ,
			'menu_name' => __('Tags', 'yt-scase-pro') ,
		);
		register_taxonomy('video_tag', array(
			'emd_video'
		) , array(
			'hierarchical' => false,
			'labels' => $video_tag_nohr_labels,
			'public' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_tagcloud' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array(
				'slug' => 'video_tag'
			) ,
			'capabilities' => array(
				'manage_terms' => 'manage_video_tag',
				'edit_terms' => 'edit_video_tag',
				'delete_terms' => 'delete_video_tag',
				'assign_terms' => 'assign_video_tag'
			) ,
		));
		if (!get_option('yt_scase_pro_emd_video_terms_init')) {
			$set_tax_terms = Array(
				Array(
					'name' => __('All videos', 'yt-scase-pro') ,
					'slug' => sanitize_title('All videos') ,
					'desc' => __('Parent category for all videos', 'yt-scase-pro')
				)
			);
			self::set_taxonomy_init($set_tax_terms, 'video_category');
			update_option('yt_scase_pro_emd_video_terms_init', true);
		}
	}
	/**
	 * Set metabox fields,labels,filters, comments, relationships if exists
	 *
	 * @since WPAS 4.0
	 *
	 */
	public function set_filters() {
		$this->sing_label = __('Video', 'yt-scase-pro');
		$this->plural_label = __('Videos', 'yt-scase-pro');
		$this->menu_entity = 'emd_video';
		$this->boxes[] = array(
			'id' => 'acc_emd_video_0',
			'title' => __('Video', 'yt-scase-pro') ,
			'pages' => array(
				'emd_video'
			) ,
			'context' => 'normal',
		);
		list($search_args, $filter_args) = $this->set_args_boxes();
		if (!post_type_exists($this->post_type) || in_array($this->post_type, Array(
			'post',
			'page'
		))) {
			self::register();
		}
		global $cpt_filters;
		$cpt_filters = tribe_setup_apm($this->post_type, $search_args, $this->boxes, $filter_args);
		$cpt_filters->add_taxonomies = true;
		$cpt_filters->add_relationships = true;
		$cpt_filters->do_metaboxes = false;
		$cpt_filters->app_name = 'yt-scase-pro';
		$cpt_filters->add_comment_row = true;
		$cpt_filters->comment_type = 'video_comments';
		Emd_Comments::register_comment_type('video_comments', Array(
			'app' => 'yt_scase_pro',
			'parent_post_type' => 'emd_video',
			'capability' => 'manage_video_comments_emd_videos',
			'single_label' => __('Video Comment', 'yt-scase-pro') ,
			'plural_label' => __('Video Comments', 'yt-scase-pro') ,
			'unset_trash' => 0,
			'unset_spam' => 0,
			'display_type' => 'noshc'
		));
		if (!function_exists('p2p_register_connection_type')) {
			return;
		}
		p2p_register_connection_type(array(
			'name' => 'related_videos',
			'from' => 'emd_video',
			'to' => 'emd_video',
			'sortable' => 'any',
			'reciprocal' => true,
			'title' => array(
				'from' => __('Related Videos', 'yt-scase-pro') ,
				'to' => __('Related Videos', 'yt-scase-pro')
			) ,
			'from_labels' => array(
				'singular_name' => __('Video', 'yt-scase-pro') ,
				'search_items' => __('Search Videos', 'yt-scase-pro') ,
				'not_found' => __('No Videos found.', 'yt-scase-pro') ,
			) ,
			'to_labels' => array(
				'singular_name' => __('Video', 'yt-scase-pro') ,
				'search_items' => __('Search Videos', 'yt-scase-pro') ,
				'not_found' => __('No Videos found.', 'yt-scase-pro') ,
			) ,
			'admin_box' => array(
				'show' => 'from',
				'context' => 'advanced'
			) ,
		));
	}
	/**
	 * Change content for created frontend views
	 * @since WPAS 4.0
	 * @param string $content
	 *
	 * @return string $content
	 */
	public function change_content($content) {
		global $post;
		$layout = "";
		if (get_post_type() == $this->post_type && is_single()) {
			ob_start();
			emd_get_template_part($this->textdomain, 'single', 'emd-video');
			$layout = ob_get_clean();
		} elseif (is_post_type_archive('emd_video')) {
			ob_start();
			emd_get_template_part($this->textdomain, 'archive', 'emd-video');
			$layout = ob_get_clean();
		} elseif (is_tax('video_tag') && $post->post_type == $this->post_type) {
			ob_start();
			emd_get_template_part($this->textdomain, 'taxonomy', 'video-tag');
			$layout = ob_get_clean();
		} elseif (is_tax('video_category') && $post->post_type == $this->post_type) {
			ob_start();
			emd_get_template_part($this->textdomain, 'taxonomy', 'video-category');
			$layout = ob_get_clean();
		}
		if ($layout != "") {
			$content = $layout;
		}
		return $content;
	}
	/**
	 * Add operations and add new submenu hook
	 * @since WPAS 4.4
	 */
	public function add_menu_link() {
		add_submenu_page(null, __('Operations', 'yt-scase-pro') , __('Operations', 'yt-scase-pro') , 'manage_operations_emd_videos', 'operations_emd_video', array(
			$this,
			'get_operations'
		));
	}
	/**
	 * Display operations page
	 * @since WPAS 4.0
	 */
	public function get_operations() {
		if (current_user_can('manage_operations_emd_videos')) {
			$myapp = str_replace("-", "_", $this->textdomain);
			emd_operations_entity($this->post_type, $this->plural_label, $this->sing_label, $myapp, $this->menu_entity);
		}
	}
}
new Emd_Video;
