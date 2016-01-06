<?php
/**
 * Install and Deactivate Plugin Functions
 * @package YT_SCASE_PRO
 * @version 1.1.0
 * @since WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
if (!class_exists('Yt_Scase_Pro_Install_Deactivate')):
	/**
	 * Yt_Scase_Pro_Install_Deactivate Class
	 * @since WPAS 4.0
	 */
	class Yt_Scase_Pro_Install_Deactivate {
		private $option_name;
		/**
		 * Hooks for install and deactivation and create options
		 * @since WPAS 4.0
		 */
		public function __construct() {
			$this->option_name = 'yt_scase_pro';
			$curr_version = get_option($this->option_name . '_version', 1);
			$new_version = constant(strtoupper($this->option_name) . '_VERSION');
			if (version_compare($curr_version, $new_version, '<')) {
				$this->set_options();
				update_option($this->option_name . '_version', $new_version);
			}
			register_activation_hook(YT_SCASE_PRO_PLUGIN_FILE, array(
				$this,
				'install'
			));
			register_deactivation_hook(YT_SCASE_PRO_PLUGIN_FILE, array(
				$this,
				'deactivate'
			));
			add_action('admin_init', array(
				$this,
				'setup_pages'
			));
			add_action('admin_notices', array(
				$this,
				'install_notice'
			));
			add_action('generate_rewrite_rules', 'emd_create_rewrite_rules');
			add_filter('query_vars', 'emd_query_vars');
			add_action('admin_init', array(
				$this,
				'register_settings'
			));
			$this->comment = new Emd_Comments('yt-scase-pro');
			add_action('before_delete_post', array(
				$this,
				'delete_post_file_att'
			));
			add_filter('tiny_mce_before_init', array(
				$this,
				'tinymce_fix'
			));
		}
		/**
		 * Runs on plugin install to setup custom post types and taxonomies
		 * flushing rewrite rules, populates settings and options
		 * creates roles and assign capabilities
		 * @since WPAS 4.0
		 *
		 */
		public function install() {
			P2P_Storage::install();
			Emd_Video::register();
			flush_rewrite_rules();
			$this->set_roles_caps();
			$this->set_options();
		}
		/**
		 * Runs on plugin deactivate to remove options, caps and roles
		 * flushing rewrite rules
		 * @since WPAS 4.0
		 *
		 */
		public function deactivate() {
			flush_rewrite_rules();
			$this->remove_caps_roles();
			$this->reset_options();
		}
		/**
		 * Register notification and/or license settings
		 * @since WPAS 4.0
		 *
		 */
		public function register_settings() {
			$shc_list = get_option($this->option_name . '_shc_list');
			$license = new Emd_License($this->option_name, 'app', $shc_list['app']);
			$license->license_updater();
			emd_youtube_register_settings($this->option_name);
		}
		/**
		 * Sets caps and roles
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function set_roles_caps() {
			global $wp_roles;
			if (class_exists('WP_Roles')) {
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}
			}
			if (is_object($wp_roles)) {
				$myvideo_manager = get_role('video_manager');
				if (empty($myvideo_manager)) {
					$myvideo_manager = add_role('video_manager', __('Video Manager', 'yt-scase-pro'));
				}
				$this->set_reset_caps($wp_roles, 'add');
			}
		}
		/**
		 * Removes caps and roles
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function remove_caps_roles() {
			global $wp_roles;
			if (class_exists('WP_Roles')) {
				if (!isset($wp_roles)) {
					$wp_roles = new WP_Roles();
				}
			}
			if (is_object($wp_roles)) {
				$this->set_reset_caps($wp_roles, 'remove');
				remove_role('video_manager');
			}
		}
		/**
		 * Set , reset capabilities
		 *
		 * @since WPAS 4.0
		 * @param object $wp_roles
		 * @param string $type
		 *
		 */
		public function set_reset_caps($wp_roles, $type) {
			$caps['enable'] = Array(
				'edit_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'edit_video_tag' => Array(
					'administrator',
					'video_manager'
				) ,
				'delete_video_category' => Array(
					'administrator',
					'video_manager'
				) ,
				'edit_video_category' => Array(
					'administrator',
					'video_manager'
				) ,
				'read' => Array(
					'video_manager'
				) ,
				'manage_video_category' => Array(
					'administrator',
					'video_manager'
				) ,
				'edit_private_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'edit_others_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'delete_video_tag' => Array(
					'administrator',
					'video_manager'
				) ,
				'manage_operations_emd_videos' => Array(
					'administrator'
				) ,
				'delete_private_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'read_private_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'delete_others_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'delete_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'manage_video_comments_emd_videos' => Array(
					'administrator',
					'administrator',
					'editor',
					'video_manager'
				) ,
				'delete_published_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'manage_video_tag' => Array(
					'administrator',
					'video_manager'
				) ,
				'edit_published_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
				'assign_video_tag' => Array(
					'administrator',
					'video_manager'
				) ,
				'assign_video_category' => Array(
					'administrator',
					'video_manager'
				) ,
				'publish_emd_videos' => Array(
					'administrator',
					'video_manager'
				) ,
			);
			foreach ($caps as $stat => $role_caps) {
				foreach ($role_caps as $mycap => $roles) {
					foreach ($roles as $myrole) {
						if (($type == 'add' && $stat == 'enable') || ($stat == 'disable' && $type == 'remove')) {
							$wp_roles->add_cap($myrole, $mycap);
						} else if (($type == 'remove' && $stat == 'enable') || ($type == 'add' && $stat == 'disable')) {
							$wp_roles->remove_cap($myrole, $mycap);
						}
					}
				}
			}
		}
		/**
		 * Set app specific options
		 *
		 * @since WPAS 4.0
		 *
		 */
		private function set_options() {
			update_option($this->option_name . '_setup_pages', 1);
			update_option($this->option_name . '_comment_list', Array(
				'video_comments'
			));
			update_option($this->option_name . '_youtube_api_key', Array(
				'emd_video' => 'AIzaSyAS8b2FzWVW4NTAVfALnUxf1CjDylPmvCs'
			));
			update_option($this->option_name . '_youtube_api_attr', Array(
				'emd_video' => Array(
					'video_attr' => 'emd_video_key',
					'username_attr' => 'emd_video_user_uploads'
				)
			));
			$ent_list = Array(
				'emd_video' => Array(
					'label' => __('Videos', 'yt-scase-pro') ,
					'unique_keys' => Array(
						'emd_blt_title'
					) ,
					'req_blt' => Array(
						'blt_title' => Array(
							'msg' => __('Title', 'yt-scase-pro')
						) ,
					) ,
				) ,
			);
			update_option($this->option_name . '_ent_list', $ent_list);
			$shc_list['app'] = 'YouTube Showcase Professional';
			$shc_list['shcs']['video_grid'] = Array(
				"class_name" => "emd_video",
				"type" => "std",
				'page_title' => __('Video Grid Gallery', 'yt-scase-pro') ,
			);
			$shc_list['shcs']['video_wall'] = Array(
				"class_name" => "emd_video",
				"type" => "std",
				'page_title' => __('Video Wall Gallery', 'yt-scase-pro') ,
			);
			$shc_list['integrations']['video_gallery'] = Array(
				'type' => 'integration',
				'app_dash' => 0,
				'page_title' => __('Video Gallery', 'yt-scase-pro')
			);
			if (!empty($shc_list)) {
				update_option($this->option_name . '_shc_list', $shc_list);
			}
			$attr_list['emd_video']['emd_video_featured'] = Array(
				'visible' => 1,
				'label' => __('Featured', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Adds the video to featured video list.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_list_type'] = Array(
				'visible' => 1,
				'label' => __('Video Type', 'yt-scase-pro') ,
				'display_type' => 'radio',
				'required' => 1,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Identifies the content that will load in the player.', 'yt-scase-pro') ,
				'type' => 'char',
				'options' => array(
					'single' => __('Single', 'yt-scase-pro') ,
					'playlist' => __('Playlist', 'yt-scase-pro') ,
					'search' => __('Search', 'yt-scase-pro') ,
					'user_uploads' => __('User Uploads', 'yt-scase-pro') ,
					'custom' => __('Custom Playlist', 'yt-scase-pro')
				) ,
				'conditional' => Array(
					'rules' => Array(
						'emd_video_key' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_playlist' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'custom'
						) ,
						'emd_video_list_playlist' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'playlist'
						) ,
						'emd_video_list_search' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'search'
						) ,
						'emd_video_user_uploads' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'user_uploads'
						) ,
						'emd_video_duration' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_like_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_favorite_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_comment_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_view_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_channel_view_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'user_uploads'
						) ,
						'emd_video_channel_comment_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'user_uploads'
						) ,
						'emd_video_channel_subscriber_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'user_uploads'
						) ,
						'emd_video_channel_video_count' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'user_uploads'
						) ,
						'emd_video_published' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'single'
						) ,
						'emd_video_channel_published' => Array(
							'view' => 'show',
							'depend_check' => 'is',
							'depend_value' => 'user_uploads'
						) ,
					) ,
					'start_hide' => Array(
						'emd_video_key',
						'emd_video_playlist',
						'emd_video_list_playlist',
						'emd_video_list_search',
						'emd_video_user_uploads',
						'emd_video_duration',
						'emd_video_like_count',
						'emd_video_favorite_count',
						'emd_video_comment_count',
						'emd_video_view_count',
						'emd_video_channel_view_count',
						'emd_video_channel_comment_count',
						'emd_video_channel_subscriber_count',
						'emd_video_channel_video_count',
						'emd_video_published',
						'emd_video_channel_published'
					)
				) ,
			);
			$attr_list['emd_video']['emd_video_key'] = Array(
				'visible' => 1,
				'label' => __('Video ID', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 1,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>The unique 11 digit alphanumeric video id found on the YouTube video. For example; in https://www.youtube.com/watch?v=uVgWZd7oGOk. uVgWZd7oGOk is the video id.</p>', 'yt-scase-pro') ,
				'type' => 'char',
				'minlength' => 11,
				'maxlength' => 11,
			);
			$attr_list['emd_video']['emd_video_playlist'] = Array(
				'visible' => 1,
				'label' => __('Custom Playlist', 'yt-scase-pro') ,
				'display_type' => 'textarea',
				'required' => 1,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Enter a comma-separated list of video IDs to play. The first video that plays will be the video specified in the video id field and the videos specified in here will play thereafter.', 'yt-scase-pro') ,
				'type' => 'char',
			);
			$attr_list['emd_video']['emd_video_list_playlist'] = Array(
				'visible' => 1,
				'label' => __('PlayList ID', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 1,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Enter a YouTube playlist ID. Make sure the parameter value begins with the letters <code>PL</code>.', 'yt-scase-pro') ,
				'type' => 'char',
			);
			$attr_list['emd_video']['emd_video_list_search'] = Array(
				'visible' => 1,
				'label' => __('Query Terms', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 1,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Enter the search terms without space. You can use <b>+</b> operator to force the search results to include or <b>-</b> operator to force the search results to omit the term. For example; Movies+2015-2010', 'yt-scase-pro') ,
				'type' => 'char',
				'minlength' => 2,
			);
			$attr_list['emd_video']['emd_video_user_uploads'] = Array(
				'visible' => 1,
				'label' => __('User Uploads', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 1,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Enter the name of the YouTube channel retrieve a list of videos uploaded to the channel.', 'yt-scase-pro') ,
				'type' => 'char',
			);
			$attr_list['emd_video']['emd_video_thumbnail_image'] = Array(
				'visible' => 1,
				'label' => __('Video Thumbnail Image', 'yt-scase-pro') ,
				'display_type' => 'thickbox_image',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Sets the video thumbnail image. Displayed best at 16:9 ratio. For small images 320x180px looks good.', 'yt-scase-pro') ,
				'type' => 'char',
				'max_file_uploads' => 1,
			);
			$attr_list['emd_video']['emd_video_display_order'] = Array(
				'visible' => 1,
				'label' => __('Display Order', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 1,
				'desc' => __('Sets the order of video display in gallery views. Exp. the video with display order of 1 precedes another video with 2', 'yt-scase-pro') ,
				'type' => 'signed',
				'std' => '1',
				'integer' => true,
			);
			$attr_list['emd_video']['emd_video_duration'] = Array(
				'visible' => 1,
				'label' => __('Duration', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays video duration stat when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_like_count'] = Array(
				'visible' => 1,
				'label' => __('Like Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays video like count stat when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_favorite_count'] = Array(
				'visible' => 1,
				'label' => __('Favorite Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays video_favorite_count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_comment_count'] = Array(
				'visible' => 1,
				'label' => __('Comment Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays video comment count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_view_count'] = Array(
				'visible' => 1,
				'label' => __('View Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays video view count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_channel_view_count'] = Array(
				'visible' => 1,
				'label' => __('View Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays channel view count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_channel_comment_count'] = Array(
				'visible' => 1,
				'label' => __('Comment Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays channel comment count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_channel_subscriber_count'] = Array(
				'visible' => 1,
				'label' => __('Subscriber Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays channel subscriber count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_channel_video_count'] = Array(
				'visible' => 1,
				'label' => __('Video Count', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('Displays channel video count when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_published'] = Array(
				'visible' => 1,
				'label' => __('Published At', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Displays the date that the video was published at when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_channel_published'] = Array(
				'visible' => 1,
				'label' => __('Channel Published At', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Displays the date that the channel published at when checked.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_autohide'] = Array(
				'visible' => 1,
				'label' => __('Autohide', 'yt-scase-pro') ,
				'display_type' => 'select',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>Indicates whether the video controls will automatically hide after a video begins playing. <strong>Fade out:</strong> The default behavior. The video progress bar will fade out while the player controls remain visible. <strong>Slide out:</strong> The video progress bar and the player controls will slide out of view a couple of seconds after the "video starts" playing. They will only \'reappear\' if the user moves her mouse over the video player or presses a key on her keyboard. <strong>Visible:</strong> The video progress bar and the video player controls will be visible throughout the video and in fullscreen.</p>', 'yt-scase-pro') ,
				'type' => 'char',
				'options' => array(
					'' => __('Please Select', 'yt-scase-pro') ,
					'2' => __('Fade out', 'yt-scase-pro') ,
					'1' => __('Slide out', 'yt-scase-pro') ,
					'0' => __('Visible', 'yt-scase-pro')
				) ,
				'std' => '2',
			);
			$attr_list['emd_video']['emd_video_autoplay'] = Array(
				'visible' => 1,
				'label' => __('Autoplay', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>The video will autoplay when checked.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
				'std' => '1',
			);
			$attr_list['emd_video']['emd_video_cc_load'] = Array(
				'visible' => 1,
				'label' => __('CC Load Policy', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('If checked closed captions will be shown by default, even if the user has turned captions off.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_theme'] = Array(
				'visible' => 1,
				'label' => __('Control Bar Theme', 'yt-scase-pro') ,
				'display_type' => 'radio',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>Sets a dark or light control bar.</p>', 'yt-scase-pro') ,
				'type' => 'char',
				'options' => array(
					'dark' => __('Dark', 'yt-scase-pro') ,
					'light' => __('Light', 'yt-scase-pro')
				) ,
				'std' => 'dark',
			);
			$attr_list['emd_video']['emd_video_player_controls'] = Array(
				'visible' => 1,
				'label' => __('Display Controls', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Sets whether the video player controls will display.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_disablekb'] = Array(
				'visible' => 1,
				'label' => __('Disable Keyboard Controls', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>When checked, it disables the player keyboard controls. Keyboard controls are as follows: <strong>Spacebar:</strong> Play / Pause. <strong>Arrow Left:</strong> Jump back 10% in the current video. <strong>Arrow Right:</strong> Jump ahead 10% in the current video. <strong>Arrow Up:</strong> Volume up. <strong>Arrow Down:</strong> Volume Down.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_fullscreen'] = Array(
				'visible' => 1,
				'label' => __('Display Fullscreen', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>When unchecked, the player does not display the fullscreen button.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
				'std' => '1',
			);
			$attr_list['emd_video']['emd_video_iv_load_policy'] = Array(
				'visible' => 1,
				'label' => __('Display Annotations', 'yt-scase-pro') ,
				'display_type' => 'radio',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>Sets if video annotations will be displayed by default or not.</p>', 'yt-scase-pro') ,
				'type' => 'char',
				'options' => array(
					'1' => __('Display', 'yt-scase-pro') ,
					'3' => __('Do not display', 'yt-scase-pro')
				) ,
			);
			$attr_list['emd_video']['emd_video_rel'] = Array(
				'visible' => 1,
				'label' => __('Display Related Videos', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>When unchecked, the player does not show related videos when playback of the initial video ends.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
				'std' => '1',
			);
			$attr_list['emd_video']['emd_video_language'] = Array(
				'visible' => 1,
				'label' => __('Interface Language', 'yt-scase-pro') ,
				'display_type' => 'select',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>Sets the player\'s interface language.</p>', 'yt-scase-pro') ,
				'type' => 'char',
				'options' => array(
					'' => __('Please Select', 'yt-scase-pro') ,
					'aa' => __('Afar', 'yt-scase-pro') ,
					'ab' => __('Abkhazian', 'yt-scase-pro') ,
					'af' => __('Afrikaans', 'yt-scase-pro') ,
					'am' => __('Amharic', 'yt-scase-pro') ,
					'ar' => __('Arabic', 'yt-scase-pro') ,
					'as' => __('Assamese', 'yt-scase-pro') ,
					'ay' => __('Aymara', 'yt-scase-pro') ,
					'az' => __('Azerbaijani', 'yt-scase-pro') ,
					'ba' => __('Bashkir', 'yt-scase-pro') ,
					'be' => __('Byelorussian', 'yt-scase-pro') ,
					'bg' => __('Bulgarian', 'yt-scase-pro') ,
					'bh' => __('Bihari', 'yt-scase-pro') ,
					'bi' => __('Bislama', 'yt-scase-pro') ,
					'bn' => __('Bengali', 'yt-scase-pro') ,
					'bo' => __('Tibetan', 'yt-scase-pro') ,
					'br' => __('Breton', 'yt-scase-pro') ,
					'ca' => __('Catalan', 'yt-scase-pro') ,
					'co' => __('Corsican', 'yt-scase-pro') ,
					'cs' => __('Czech', 'yt-scase-pro') ,
					'cy' => __('Welch', 'yt-scase-pro') ,
					'da' => __('Danish', 'yt-scase-pro') ,
					'de' => __('German', 'yt-scase-pro') ,
					'dz' => __('Bhutani', 'yt-scase-pro') ,
					'el' => __('Greek', 'yt-scase-pro') ,
					'en' => __('English', 'yt-scase-pro') ,
					'eo' => __('Esperanto', 'yt-scase-pro') ,
					'es' => __('Spanish', 'yt-scase-pro') ,
					'et' => __('Estonian', 'yt-scase-pro') ,
					'eu' => __('Basque', 'yt-scase-pro') ,
					'fa' => __('Persian', 'yt-scase-pro') ,
					'fi' => __('Finnish', 'yt-scase-pro') ,
					'fj' => __('Fiji', 'yt-scase-pro') ,
					'fo' => __('Faeroese', 'yt-scase-pro') ,
					'fr' => __('French', 'yt-scase-pro') ,
					'fy' => __('Frisian', 'yt-scase-pro') ,
					'ga' => __('Irish', 'yt-scase-pro') ,
					'gd' => __('ScotsGaelic', 'yt-scase-pro') ,
					'gl' => __('Galician', 'yt-scase-pro') ,
					'gn' => __('Guarani', 'yt-scase-pro') ,
					'gu' => __('Gujarati', 'yt-scase-pro') ,
					'ha' => __('Hausa', 'yt-scase-pro') ,
					'he' => __('Hebrew', 'yt-scase-pro') ,
					'hi' => __('Hindi', 'yt-scase-pro') ,
					'hr' => __('Croatian', 'yt-scase-pro') ,
					'hu' => __('Hungarian', 'yt-scase-pro') ,
					'hy' => __('Armenian', 'yt-scase-pro') ,
					'ia' => __('Interlingua', 'yt-scase-pro') ,
					'id' => __('Indonesian', 'yt-scase-pro') ,
					'ie' => __('Interlingue', 'yt-scase-pro') ,
					'ik' => __('Inupiak', 'yt-scase-pro') ,
					'is' => __('Icelandic', 'yt-scase-pro') ,
					'it' => __('Italian', 'yt-scase-pro') ,
					'iu' => __('Inuktitut', 'yt-scase-pro') ,
					'ja' => __('Japanese', 'yt-scase-pro') ,
					'jw' => __('Javanese', 'yt-scase-pro') ,
					'ka' => __('Georgian', 'yt-scase-pro') ,
					'kk' => __('Kazakh', 'yt-scase-pro') ,
					'kl' => __('Greenlandic', 'yt-scase-pro') ,
					'km' => __('Cambodian', 'yt-scase-pro') ,
					'kn' => __('Kannada', 'yt-scase-pro') ,
					'ko' => __('Korean', 'yt-scase-pro') ,
					'ks' => __('Kashmiri', 'yt-scase-pro') ,
					'ku' => __('Kurdish', 'yt-scase-pro') ,
					'ky' => __('Kirghiz', 'yt-scase-pro') ,
					'la' => __('Latin', 'yt-scase-pro') ,
					'ln' => __('Lingala', 'yt-scase-pro') ,
					'lo' => __('Laothian', 'yt-scase-pro') ,
					'lt' => __('Lithuanian', 'yt-scase-pro') ,
					'lv' => __('LatvianLettish', 'yt-scase-pro') ,
					'mg' => __('Malagasy', 'yt-scase-pro') ,
					'mi' => __('Maori', 'yt-scase-pro') ,
					'mk' => __('Macedonian', 'yt-scase-pro') ,
					'ml' => __('Malayalam', 'yt-scase-pro') ,
					'mn' => __('Mongolian', 'yt-scase-pro') ,
					'mo' => __('Moldavian', 'yt-scase-pro') ,
					'mr' => __('Marathi', 'yt-scase-pro') ,
					'ms' => __('Malay', 'yt-scase-pro') ,
					'mt' => __('Maltese', 'yt-scase-pro') ,
					'my' => __('Burmese', 'yt-scase-pro') ,
					'na' => __('Nauru', 'yt-scase-pro') ,
					'ne' => __('Nepali', 'yt-scase-pro') ,
					'nl' => __('Dutch', 'yt-scase-pro') ,
					'no' => __('Norwegian', 'yt-scase-pro') ,
					'oc' => __('Occitan', 'yt-scase-pro') ,
					'om' => __('Oromo', 'yt-scase-pro') ,
					'or' => __('Oriya', 'yt-scase-pro') ,
					'pa' => __('Punjabi', 'yt-scase-pro') ,
					'pl' => __('Polish', 'yt-scase-pro') ,
					'ps' => __('Pashto-Pushto', 'yt-scase-pro') ,
					'pt' => __('Portuguese', 'yt-scase-pro') ,
					'qu' => __('Quechua', 'yt-scase-pro') ,
					'rm' => __('Rhaeto-Romance', 'yt-scase-pro') ,
					'rn' => __('Kirundi', 'yt-scase-pro') ,
					'ro' => __('Romanian', 'yt-scase-pro') ,
					'ru' => __('Russian', 'yt-scase-pro') ,
					'rw' => __('Kinyarwanda', 'yt-scase-pro') ,
					'sa' => __('Sanskrit', 'yt-scase-pro') ,
					'sd' => __('Sindhi', 'yt-scase-pro') ,
					'sg' => __('Sangro', 'yt-scase-pro') ,
					'sh' => __('Serbo-Croatian', 'yt-scase-pro') ,
					'si' => __('Singhalese', 'yt-scase-pro') ,
					'sk' => __('Slovak', 'yt-scase-pro') ,
					'sl' => __('Slovenian', 'yt-scase-pro') ,
					'sm' => __('Samoan', 'yt-scase-pro') ,
					'sn' => __('Shona', 'yt-scase-pro') ,
					'so' => __('Somali', 'yt-scase-pro') ,
					'sq' => __('Albanian', 'yt-scase-pro') ,
					'sr' => __('Serbian', 'yt-scase-pro') ,
					'ss' => __('Siswati', 'yt-scase-pro') ,
					'st' => __('Sesotho', 'yt-scase-pro') ,
					'su' => __('Sudanese', 'yt-scase-pro') ,
					'sv' => __('Swedish', 'yt-scase-pro') ,
					'sw' => __('Swahili', 'yt-scase-pro') ,
					'ta' => __('Tamil', 'yt-scase-pro') ,
					'te' => __('Tegulu', 'yt-scase-pro') ,
					'tg' => __('Tajik', 'yt-scase-pro') ,
					'th' => __('Thai', 'yt-scase-pro') ,
					'ti' => __('Tigrinya', 'yt-scase-pro') ,
					'tk' => __('Turkmen', 'yt-scase-pro') ,
					'tl' => __('Tagalog', 'yt-scase-pro') ,
					'tn' => __('Setswana', 'yt-scase-pro') ,
					'to' => __('Tonga', 'yt-scase-pro') ,
					'tr' => __('Turkish', 'yt-scase-pro') ,
					'ts' => __('Tsonga', 'yt-scase-pro') ,
					'tt' => __('Tatar', 'yt-scase-pro') ,
					'tw' => __('Twi', 'yt-scase-pro') ,
					'ug' => __('Uigur', 'yt-scase-pro') ,
					'uk' => __('Ukrainian', 'yt-scase-pro') ,
					'ur' => __('Urdu', 'yt-scase-pro') ,
					'uz' => __('Uzbek', 'yt-scase-pro') ,
					'vi' => __('Vietnamese', 'yt-scase-pro') ,
					'vo' => __('Volapuk', 'yt-scase-pro') ,
					'wo' => __('Wolof', 'yt-scase-pro') ,
					'xh' => __('Xhosa', 'yt-scase-pro') ,
					'yi' => __('Yiddish', 'yt-scase-pro') ,
					'yo' => __('Yoruba', 'yt-scase-pro') ,
					'za' => __('Zhuang', 'yt-scase-pro') ,
					'zh' => __('Chinese', 'yt-scase-pro') ,
					'zu' => __('Zulu', 'yt-scase-pro')
				) ,
				'std' => 'en',
			);
			$attr_list['emd_video']['emd_video_loop'] = Array(
				'visible' => 1,
				'label' => __('Loop', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('<p>When checked the player plays the initial video again and again.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_modestbranding'] = Array(
				'visible' => 1,
				'label' => __('Modesbranding', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>When checked, the player does not show a YouTube logo. Note that a small YouTube text label will still display in the upper-right corner of a paused video when the user\'s mouse pointer hovers over the player.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			$attr_list['emd_video']['emd_video_start'] = Array(
				'visible' => 1,
				'label' => __('Start Playing', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 0,
				'filterable' => 0,
				'list_visible' => 0,
				'desc' => __('<p>When set, the player begins playing the video at the given number of seconds from the start of the video.</p>', 'yt-scase-pro') ,
				'type' => 'signed',
				'integer' => true,
			);
			$attr_list['emd_video']['emd_video_end'] = Array(
				'visible' => 1,
				'label' => __('Stop Playing After', 'yt-scase-pro') ,
				'display_type' => 'text',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>Sets the time the player stops playing the video in seconds from the start of the video.</p>', 'yt-scase-pro') ,
				'type' => 'signed',
				'std' => '3600',
				'min' => 1,
				'integer' => true,
			);
			$attr_list['emd_video']['emd_video_showinfo'] = Array(
				'visible' => 1,
				'label' => __('Show info', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>When unchecked, the player will not display information like the video title and uploader before the video starts playing.</p>', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
				'std' => '1',
			);
			$attr_list['emd_video']['emd_video_color'] = Array(
				'visible' => 1,
				'label' => __('Theme', 'yt-scase-pro') ,
				'display_type' => 'radio',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('<p>Sets the color of the player\'s video progress bar to highlight the amount of the video that the viewer has already seen.</p>', 'yt-scase-pro') ,
				'type' => 'char',
				'options' => array(
					'red' => __('Red', 'yt-scase-pro') ,
					'white' => __('White', 'yt-scase-pro')
				) ,
				'std' => 'red',
			);
			$attr_list['emd_video']['emd_video_playsinline'] = Array(
				'visible' => 1,
				'label' => __('Plays inline', 'yt-scase-pro') ,
				'display_type' => 'checkbox',
				'required' => 0,
				'filterable' => 1,
				'list_visible' => 0,
				'desc' => __('Sets whether videos play inline or fullscreen in an HTML5 player on iOS. When checked, the player plays the video inline for UIWebViews created with the allowsInlineMediaPlayback property set to TRUE otherwise the video is played fullscreen.', 'yt-scase-pro') ,
				'type' => 'binary',
				'options' => array(
					1 => 1
				) ,
			);
			if (!empty($attr_list)) {
				update_option($this->option_name . '_attr_list', $attr_list);
			}
			$tax_list['emd_video']['video_category'] = Array(
				'label' => __('Categories', 'yt-scase-pro') ,
				'default' => Array(
					__('All videos', 'yt-scase-pro')
				) ,
				'type' => 'multi',
				'hier' => 1,
				'required' => 0
			);
			$tax_list['emd_video']['video_tag'] = Array(
				'label' => __('Tags', 'yt-scase-pro') ,
				'default' => '',
				'type' => 'multi',
				'hier' => 0,
				'required' => 0
			);
			if (!empty($tax_list)) {
				update_option($this->option_name . '_tax_list', $tax_list);
			}
			$rel_list['rel_related_videos'] = Array(
				'from' => 'emd_video',
				'to' => 'emd_video',
				'from_title' => __('Related Videos', 'yt-scase-pro') ,
				'to_title' => __('Related Videos', 'yt-scase-pro')
			);
			if (!empty($rel_list)) {
				update_option($this->option_name . '_rel_list', $rel_list);
			}
			$emd_activated_plugins = get_option('emd_activated_plugins');
			if (!$emd_activated_plugins) {
				update_option('emd_activated_plugins', Array(
					'yt-scase-pro'
				));
			} elseif (!in_array('yt-scase-pro', $emd_activated_plugins)) {
				array_push($emd_activated_plugins, 'yt-scase-pro');
				update_option('emd_activated_plugins', $emd_activated_plugins);
			}
			//conf parameters for incoming email
			//conf parameters for inline entity
			//action to configure different extension conf parameters for this plugin
			do_action('emd_extension_set_conf');
		}
		/**
		 * Reset app specific options
		 *
		 * @since WPAS 4.0
		 *
		 */
		private function reset_options() {
			delete_option($this->option_name . '_ent_list');
			delete_option($this->option_name . '_shc_list');
			delete_option($this->option_name . '_attr_list');
			delete_option($this->option_name . '_tax_list');
			delete_option($this->option_name . '_rel_list');
			delete_option($this->option_name . '_adm_notice1');
			delete_option($this->option_name . '_setup_pages');
			$settings = get_option('emd_license_settings', Array());
			unset($settings[$this->option_name]);
			update_option('emd_license_settings', $settings);
			delete_option($this->option_name . '_comment_list');
			$emd_activated_plugins = get_option('emd_activated_plugins');
			if (!empty($emd_activated_plugins)) {
				$emd_activated_plugins = array_diff($emd_activated_plugins, Array(
					'yt-scase-pro'
				));
				update_option('emd_activated_plugins', $emd_activated_plugins);
			}
		}
		/**
		 * Show install notices
		 *
		 * @since WPAS 4.0
		 *
		 * @return html
		 */
		public function install_notice() {
			if (isset($_GET[$this->option_name . '_adm_notice1'])) {
				update_option($this->option_name . '_adm_notice1', true);
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_adm_notice1') != 1) {
?>
<div class="updated">
<?php
				printf('<p><a href="%1s" target="_blank"> %2$s </a>%3$s<a style="float:right;" href="%4$s"><span class="dashicons dashicons-dismiss" style="font-size:15px;"></span>%5$s</a></p>', 'https://docs.emdplugins.com/docs/youtube-showcase-professional-documentation/?pk_campaign=youtubepro&pk_source=plugin&pk_medium=link&pk_content=notice', __('New To Youtube Showcase Professional? Review the documentation!', 'wpas') , __('&#187;', 'wpas') , esc_url(add_query_arg($this->option_name . '_adm_notice1', true)) , __('Dismiss', 'wpas'));
?>
</div>
<?php
			}
			if (current_user_can('manage_options') && get_option($this->option_name . '_setup_pages') == 1) {
				echo "<div id=\"message\" class=\"updated\"><p><strong>" . __('Welcome to YouTube Showcase Professional', 'yt-scase-pro') . "</strong></p>
           <p class=\"submit\"><a href=\"" . add_query_arg('setup_yt_scase_pro_pages', 'true', admin_url('index.php')) . "\" class=\"button-primary\">" . __('Setup YouTube Showcase Professional Pages', 'yt-scase-pro') . "</a> <a class=\"skip button-primary\" href=\"" . add_query_arg('skip_setup_yt_scase_pro_pages', 'true', admin_url('index.php')) . "\">" . __('Skip setup', 'yt-scase-pro') . "</a></p>
         </div>";
			}
		}
		/**
		 * Setup pages for components and redirect to dashboard
		 *
		 * @since WPAS 4.0
		 *
		 */
		public function setup_pages() {
			if (!is_admin()) {
				return;
			}
			global $wpdb;
			if (!empty($_GET['setup_' . $this->option_name . '_pages'])) {
				$shc_list = get_option($this->option_name . '_shc_list');
				$types = Array(
					'forms',
					'charts',
					'shcs',
					'datagrids',
					'integrations'
				);
				foreach ($types as $shc_type) {
					if (!empty($shc_list[$shc_type])) {
						foreach ($shc_list[$shc_type] as $keyshc => $myshc) {
							if (isset($myshc['page_title'])) {
								$pages[$keyshc] = $myshc;
							}
						}
					}
				}
				foreach ($pages as $key => $page) {
					$found = "";
					$page_content = "[" . $key . "]";
					$found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM " . $wpdb->posts . " WHERE post_type='page' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%"));
					if ($found != "") {
						continue;
					}
					$page_data = array(
						'post_status' => 'publish',
						'post_type' => 'page',
						'post_author' => get_current_user_id() ,
						'post_title' => $page['page_title'],
						'post_content' => $page_content,
						'comment_status' => 'closed'
					);
					$page_id = wp_insert_post($page_data);
				}
				delete_option($this->option_name . '_setup_pages');
				wp_redirect(admin_url('index.php?yt-scase-pro-installed=true'));
				exit;
			}
			if (!empty($_GET['skip_setup_' . $this->option_name . '_pages'])) {
				delete_option($this->option_name . '_setup_pages');
				wp_redirect(admin_url('index.php?'));
				exit;
			}
		}
		/**
		 * Delete file attachments when a post is deleted
		 *
		 * @since WPAS 4.0
		 * @param $pid
		 *
		 * @return bool
		 */
		public function delete_post_file_att($pid) {
			$entity_fields = get_option($this->option_name . '_attr_list');
			$post_type = get_post_type($pid);
			if (!empty($entity_fields[$post_type])) {
				//Delete fields
				foreach (array_keys($entity_fields[$post_type]) as $myfield) {
					if (in_array($entity_fields[$post_type][$myfield]['display_type'], Array(
						'file',
						'image',
						'plupload_image',
						'thickbox_image'
					))) {
						$pmeta = get_post_meta($pid, $myfield);
						if (!empty($pmeta)) {
							foreach ($pmeta as $file_id) {
								wp_delete_attachment($file_id);
							}
						}
					}
				}
			}
			return true;
		}
		public function tinymce_fix($init) {
			$init['wpautop'] = false;
			return $init;
		}
	}
endif;
return new Yt_Scase_Pro_Install_Deactivate();
