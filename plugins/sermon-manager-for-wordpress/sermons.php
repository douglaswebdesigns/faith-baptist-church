<?php
/*
Plugin Name: Sermon Manager for WordPress
Plugin URI: http://www.wpforchurch.com/products/sermon-manager-for-wordpress/
Description: Add audio and video sermons, manage speakers, series, and more. Visit <a href="http://wpforchurch.com" target="_blank">Wordpress for Church</a> for tutorials and support.
Version: 1.9.6
Author: Jack Lamb
Author URI: http://www.wpforchurch.com/
License: GPL2
Text Domain: sermon-manager
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class SermonManager{

	/**
	* Construct.
	*/
	function __construct() {
		// Define the plugin URL
		define( 'WPFC_SERMONS', plugin_dir_path(__FILE__) );

		// Plugin Folder Path
		if ( ! defined( 'SM_PLUGIN_DIR' ) )
			define( 'SM_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) . '/' );

		// Plugin Folder URL
		if ( ! defined( 'SM_PLUGIN_URL' ) )
			define( 'SM_PLUGIN_URL', plugin_dir_url( SM_PLUGIN_DIR ) . basename( dirname( __FILE__ ) ) . '/' );

		// Plugin Root File
		if ( ! defined( 'SM_PLUGIN_FILE' ) )
			define( 'SM_PLUGIN_FILE', __FILE__ );

		// include required items
		$this->includes();

		// Add defaults on activation
		register_activation_hook(__FILE__, array($this,'wpfc_add_defaults'));

		// add actions
		add_action( 'plugins_loaded', array( $this,'wpfc_sermon_translations' ) );
		add_action( 'wp_enqueue_scripts', array( $this,'add_wpfc_js' ) );
		add_filter( 'post_class', array( $this,'wpfc_sermon_post_class' ), 10, 3 );
		add_action( 'admin_init', array( $this,'wpfc_sermon_images' ) );
		add_action( 'pre_get_posts', array( $this,'wpfc_sermon_order_query' ), 9999);
	}

	// Define default option settings
	function wpfc_add_defaults() {
		$tmp = get_option('wpfc_options');
		$default = isset($tmp['chk_default_options_db']) ? $tmp['chk_default_options_db'] : '';
	    if(($default == '1') || ( !is_array($tmp ))) {
			delete_option('wpfc_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
			$arr = array(	"bibly" => "0",
							"bibly_version" => "KJV",
							"archive_slug" => "sermons",
							"archive_title" => "Sermons",
							"common_base_slug" => "0"
			);
			update_option('wpfc_options', $arr);
		}
	}

	function includes(){
		// Load Post Types and Taxonomies
		require_once plugin_dir_path( __FILE__ ) . '/includes/types-taxonomies.php';

		// Add Images for Custom Taxonomies
		require_once plugin_dir_path( __FILE__ ) . 'includes/taxonomy-images/taxonomy-images.php';

		// Add Options Page
		require_once plugin_dir_path( __FILE__ ) . '/includes/options.php';

		// Add Entry Views Tracking
		require_once plugin_dir_path( __FILE__ ) . '/includes/entry-views.php';

		// Add Upgrade Functions
		require_once plugin_dir_path( __FILE__ ) . '/includes/upgrade.php';

		// Load Shortcodes 
		require_once plugin_dir_path( __FILE__ ) . '/includes/shortcodes.php';

		// Load Widgets
		require_once plugin_dir_path( __FILE__ ) . '/includes/widgets.php';

		// Load Template Tags
		require_once plugin_dir_path( __FILE__ ) . '/includes/template-tags.php';

		// Load Podcast Functions
		require_once plugin_dir_path( __FILE__ ) . '/includes/podcast-functions.php';

		// Load Admin Functions
		if ( is_admin() ){
			require_once plugin_dir_path( __FILE__ ) . '/includes/admin-functions.php';
		}
	}

	// Translations
	function wpfc_sermon_translations() {
		load_plugin_textdomain( 'sermon-manager', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	// Add filter for custom search: includes bible_passage, sermon_description in WordPress search
	// not used???
	function wpfc_sermon_search_query( $query ) {
		if ( !is_admin() && $query->is_search ) {
			$query->set('meta_query', array(
				array(
					'key' => 'bible_passage',
					'value' => $query->query_vars['s'],
					'compare' => 'LIKE'
				),
				array(
					'key' => 'sermon_description',
					'value' => $query->query_vars['s'],
					'compare' => 'LIKE'
				)
			)); 
	        //$query->set('post_type', 'wpfc_sermon'); 
		};
	}
	//add_filter( 'pre_get_posts', 'wpfc_sermon_search_query');



	// Add scripts only to single sermon pages
	function add_wpfc_js() {

		// Register them all!
		wp_register_style('sermon-styles', plugins_url('/css/sermon.css', __FILE__));
		wp_register_script('bibly-script', 'http://code.bib.ly/bibly.min.js', false, null );
		wp_register_style('bibly-style', 'http://code.bib.ly/bibly.min.css', false, null );
		
		$sermonoptions = get_option('wpfc_options');
		if (is_single() && 'wpfc_sermon' == get_post_type() && !isset($sermonoptions['bibly']) == '1') { 
			wp_enqueue_script('bibly-script');
			wp_enqueue_style('bibly-style');
			
			// get options for JS
			$Bibleversion = $sermonoptions['bibly_version'];
			wp_localize_script( 'bibly-script', 'bibly', array( // pass WP data into JS from this point on
				'linkVersion' 				=> $Bibleversion,
				'enablePopups' 				=> true,
				'popupVersion'				=> $Bibleversion,
			));
		}
		if ( !isset($sermonoptions['css']) == '1') { 
			wp_enqueue_style('sermon-styles');
		}
		
	}

	/**
	 * Append the terms of taxonomies to the list
	 * of classes generated by post_class().
	 *
	 * @since 2013-03-01
	 */
	function wpfc_sermon_post_class( $classes, $class, $ID ) {
	 
	    $taxonomies = array(
	        'wpfc_preacher',
	        'wpfc_sermon_series',
	        'wpfc_bible_book',
	        'wpfc_sermon_topics',
	    );
	 
	    $terms = get_the_terms( (int) $ID, $taxonomies );
	 
	    if ( is_wp_error( $terms ) || empty( $terms ) )
	        return $classes;
	 
	    foreach ( (array) $terms as $term ) {
	        if ( ! in_array( $term->slug, $classes ) )
	            $classes[] = $term->slug;
	    }
	 
	    return $classes;
	}

	/**
	 * Images Sizes for Series and Speakers
	 */
	function wpfc_sermon_images() {
		if ( function_exists( 'add_image_size' ) ) { 
			add_image_size( 'sermon_small', 75, 75, true ); 
			add_image_size( 'sermon_medium', 300, 200, true ); 
			add_image_size( 'sermon_wide', 940, 350, true ); 
		}
	}

	// Make all queries for sermons order by the sermon date
	function wpfc_sermon_order_query( $query ) {
		if ( !is_admin() && $query->is_main_query() ) :
		if( is_post_type_archive('wpfc_sermon') || is_tax( 'wpfc_preacher' ) || is_tax( 'wpfc_sermon_topics' ) || is_tax( 'wpfc_sermon_series' ) || is_tax( 'wpfc_bible_book' ) ) {
			$query->set('meta_key', 'sermon_date');
			$query->set('meta_value', date("m/d/Y"));
			$query->set('meta_compare', '>=');
			$query->set('orderby', 'meta_value');
			$query->set('order', 'DESC');
		}
		endif;
	}

}
$SermonManager = new SermonManager();

/* Not sure if this is necessary */
// WordPress debug _log function
if(!function_exists('_log')){
    function _log( $message ) {
        if( WP_DEBUG === true ){
            if( is_array( $message ) || is_object( $message ) ){
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }
}