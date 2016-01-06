<?php
if ( ! class_exists('Tribe_APM') ) {
define( 'TRIBE_APM_PATH', plugin_dir_path(__FILE__) );
define( 'TRIBE_APM_LIB_PATH', TRIBE_APM_PATH . 'lib/' );

class Tribe_APM {

	protected $textdomain = 'emd-plugins';
	protected $args; //columns
	protected $argfilts; //filters
	protected $metaboxes;
	protected $url;
	
	public $columns; // holds a Tribe_Columns object
	public $filters; // holds a Tribe_Filters object
	
	public $post_type;
	public $add_taxonomies = true; // Automatically add filters/cols for registered taxonomies?
	public $add_relationships = false; // add filters/cols for registered relationships?
	public $add_comment_row = false; // add column for lastest comment author
	public $comment_type; // add column for lastest comment author
	public $do_metaboxes = true;
	public $export = false; // Show export button? (Currently does nothing)
	public $add_tabs = false; // Show tabs
        public $app_name = ''; // Show tabs
	
	// CONSTRUCTOR
	
	/**
	 * Kicks things off
	 * @param $post_type What post_type to enable filters for
	 * @param $args array multidimensional array of filter/column arrays. See documentation
	 */
	public function __construct($post_type, $args, $metaboxes = array(),$argfilts) {
		$this->post_type = $post_type;
		$this->args = $args;
		$this->argfilts = $argfilts; //filters
                if($argfilts == "") {
                        $this->argfilts = $args;
                }

		$this->metaboxes = $metaboxes;

		$this->textdomain = apply_filters( 'tribe_apm_textdomain', $this->textdomain );
		$this->url = apply_filters( 'tribe_apm_url', plugins_url('', __FILE__), __FILE__ );

		add_action( 'admin_init', array($this, 'init'), 0 );
		add_action( 'admin_init', array($this, 'init_meta_box') );
		add_action( 'tribe_cpt_filters_init', array($this, 'maybe_add_taxonomies_rels'), 10, 1 );
		add_filter( 'tribe_apm_resources_url', array($this, 'resources_url') );
	}
	
	// PUBLIC METHODS
	
	
	/**
	 * Add some additional filters/columns
	 * 
	 * @param $filters multidimensional array of filter/column arrays
	 */
	public function add_filters($filters = array(), $type='both' ) {
		if ( is_array($filters) && ! empty($filters) ) {
			if(is_array($this->args) && ($type == 'both' || $type == 'column')){
				$this->args = array_merge($this->args, $filters);
			}
			if(is_array($this->argfilts) && ($type == 'both' || $type == 'filter')){
				$this->argfilts = array_merge($this->argfilts, $filters);
			}
		}
	}
	
	// CALLBACKS

	public function init() {
		if ( ! $this->is_active() ) {
			return;
		}

		do_action( 'tribe_cpt_filters_init', $this );

		require_once TRIBE_APM_LIB_PATH . 'tribe-filters.class.php';
		require_once TRIBE_APM_LIB_PATH . 'tribe-columns.class.php';
		$this->filters = new Tribe_Filters( $this->post_type, $this->app_name, $this->get_filter_args() );
		$this->columns = new Tribe_Columns( $this->post_type, $this->get_column_args() );

		do_action( 'tribe_cpt_filters_after_init', $this);

		add_action( 'admin_notices', array($this, 'maybe_show_filters') );
		add_action( 'admin_enqueue_scripts', array($this, 'maybe_enqueue') );
	}
	
	public function resources_url($resource_url) {
		return trailingslashit( $this->url ) . 'resources/';
	} 

	/*public function init_meta_box() {
                if ( ! $this->do_metaboxes )
                        return;
                require_once TRIBE_APM_LIB_PATH . 'tribe-meta-box-helper.php';
                $for_meta_box = $this->only_meta_filters($this->args, 'metabox');
                new Tribe_Meta_Box_Helper($this->post_type, $for_meta_box, $this->metaboxes);
        }*/

        public function init_meta_box() { 
                if ( ! $this->do_metaboxes )
                {
			/*if($this->add_tabs)
                        {
				if($this->maybe_show_tabs())
                                {
					if(in_array($this->post_type,Array('post','page')))
                                        {
                                                $mypost_type = "emd_" . $this->post_type;
                                        }
                                        else
                                        {
                                                $mypost_type =$this->post_type;
                                        }
                                        $app_name = str_replace("_","-",$this->app_name);
					$inc_file = plugin_dir_path(__FILE__) . '../../../../'. $app_name . '/classes/' . $mypost_type . '/' . $mypost_type . '_tabs.php';
					if(file_exists($inc_file))
					{
						require_once $inc_file;
					}
                                }
			}*/
                        if ( !class_exists( 'EMD_Meta_Box' ) )
                                return;
			if(is_array($this->metaboxes))
			{
				foreach ( $this->metaboxes as $meta_box )
				{
					new EMD_Meta_Box( $meta_box );
				}
			}
                }
                else
                {
                        require_once TRIBE_APM_LIB_PATH . 'tribe-meta-box-helper.php';
                        $for_meta_box = $this->only_meta_filters($this->args, 'metabox');
                        new Tribe_Meta_Box_Helper($this->post_type, $for_meta_box, $this->metaboxes);
                }
        }



	// Dogfooding a bit! We're hooked into the tribe_cpt_filters_init action hook
	public function maybe_add_taxonomies_rels($tribe_cpt_filters) {
		if ( ! $tribe_cpt_filters->add_taxonomies ) return;
		$args = array();
		foreach ( get_taxonomies( array(), 'objects' ) as $tax ) {
			if ( $tax->show_ui && in_array($tribe_cpt_filters->post_type, (array) $tax->object_type, true) ) {
				$args['tax-'.$tax->name] = array(
					'name' => $tax->labels->name,
					'taxonomy' => $tax->name,
					'query_type' => 'taxonomy'
				);
			}
		}
		$tribe_cpt_filters->add_filters($args,'both');
		$args_cust = array();
		$show_author = 0;
		if(is_multisite() && is_super_admin()) {
			$show_author = 1;
		}
		elseif(!current_user_can('limitby_author_' . $tribe_cpt_filters->post_type . "s")){
			$show_author = 1;
		}
		if(post_type_supports($tribe_cpt_filters->post_type,'author') && $show_author == 1) {
			$args_cust['author'] = array(
					'name' => 'Author',
					'author' => true,
			);
		}
		
		if( $tribe_cpt_filters->add_relationships && class_exists('P2P_Connection_Type_Factory') &&
			$this->post_type == $tribe_cpt_filters->post_type )
		{
			$connections = P2P_Connection_Type_Factory::get_all_instances();
			if (!empty($connections)) {
				foreach ($connections as $type => $conn) {
					if($conn->side['from']->query_vars['post_type'][0] == $this->post_type &&
						current_user_can('edit_' . $conn->side['to']->query_vars['post_type'][0] . "s")) {
						$args_cust['rel-'.$type] = array(
							'name' => $conn->get_field('title', 'from'),	
							'custom' => 'relationship',
							'custom_type' => 'rel',
							'rel_type' => $type,
							'conn_ptype' => $conn->side['to']->query_vars['post_type'][0],
							'app_name' => $this->app_name,
						);
					}
					else if ($conn->side['to']->query_vars['post_type'][0] == $this->post_type &&
						current_user_can('edit_' . $conn->side['from']->query_vars['post_type'][0] . "s")) {
						$args_cust['rel-'.$type] = array(
							'name' => $conn->get_field('title', 'to'),	
							'custom' => 'relationship',
							'custom_type' => 'rel',
							'rel_type' => $type,
							'conn_ptype' => $conn->side['from']->query_vars['post_type'][0],
							'app_name' => $this->app_name,
						);
					}
				}
			}
		}
		if($tribe_cpt_filters->add_comment_row){
			$args_cust['latest_comment'] = array(
					'name' => __('Latest Comment','emd-plugins'),	
					'custom' => 'comment',
					'custom_type' => 'comment',
					'comment_type' => $tribe_cpt_filters->comment_type,
					'app_name' => $this->app_name,
				);
		}
		if(!empty($args_cust)) {
			$tribe_cpt_filters->add_filters($args_cust,'both');
		}
	}

	public function maybe_enqueue($blah) {
		if ( $this->is_active() ) {
			wp_enqueue_script( 'tribe-fac', $this->url . '/resources/tribe-apm.js', array('jquery') );
			wp_enqueue_style( 'tribe-fac', $this->url . '/resources/tribe-apm.css' );
		}
	}

	public function maybe_show_filters() {
		if ( $this->is_active() ) {
			include 'views/edit-filters.php';
		}
	}
	
	// UTLITIES AND INTERNAL METHODS
	
	protected function get_filter_args() {
		return $this->filter_disabled($this->argfilts, 'filters');
	}
	
	protected function get_column_args() {
		return $this->filter_disabled($this->args, 'columns');
	}
	
	/**
	 * Filter out an array of args where children arrays have a disable key set to $type
	 *
	 * @param $args array Multidimensional array of arrays
	 * @param $type string|array Value(s) of filter key to remove
	 * @return array Filtered array
	 */
	protected function filter_disabled($args, $type) {
		return $this->filter_on_key_value($args, $type, 'disable');
	}
	
	protected function filter_on_key_value($args, $type, $filterkey) {
		if(is_array($args)) {
			foreach ( $args as $key => $value ) {
				if ( isset($value[$filterkey]) && in_array($type, (array) $value[$filterkey]) ) {
					unset($args[$key]);
				}
			}
		}
		return $args;
	}
	
	protected function only_meta_filters($args) {
		foreach ( $args as $k => $v ) {
			if ( ! isset($v['meta']) ) {
				unset($args[$k]);
			}
		}
		return $this->filter_disabled($args, 'metabox');
	}
	
	protected function is_active() {
		$desired_screen = 'edit-'.$this->post_type;
		
		// Exit early on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		
		// Inline save?
		if ( defined( 'DOING_AJAX') && DOING_AJAX && isset($_POST['screen']) && $desired_screen === $_POST['screen'] ) {
			return true;
		}

		if ( ! $screen = get_current_screen() ) {
			global $pagenow;
			if ( 'edit.php' === $pagenow ) {
				if ( isset($_GET['post_type']) && $this->post_type === $_GET['post_type'] ) {
					return true;
				}
				else if ( 'post' === $this->post_type ) {
					return true;
				}
				return false;
			}
		}
		if (is_object($screen) && isset($screen->id)) {
			return $desired_screen === $screen->id;
		} else {
			return false;
		}
	}
	/*protected function maybe_show_tabs() {
		$desired_screen = 'edit-'.$this->post_type;
		
		// Exit early on autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		
		// Inline save?
		if ( defined( 'DOING_AJAX') && DOING_AJAX && isset($_POST['screen']) && $desired_screen === $_POST['screen'] ) {
			return true;
		}

		if ( ! $screen = get_current_screen() ) {
			global $pagenow;
			if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow) {
				if ( isset($_GET['post_type']) && $this->post_type === $_GET['post_type'] ) {
					return true;
				}
				elseif(isset($_GET['post']) && get_post_type($_GET['post']) === $this->post_type)
                                {
                                        return true;
                                }
				else if ( 'post' === $this->post_type ) {
					return true;
				}
				return false;
			}
		}
		if (is_object($screen) && isset($screen->id)) {
			return $desired_screen === $screen->id;
		} else {
			return false;
		}
	}*/

	public function log($data) {
		error_log(print_r($data,1));
	}
}

include 'lib/template-tags.php';

} // end if class_exists()
