<?php
/*
Plugin Name: Scrolling down popup plugin
Plugin URI: http://www.gopiplus.com/work/2011/07/23/scrolling-down-popup-wordpress-plugin/
Description: Scrolling down popup plugin create the popup window with drop in scrolling effect. With this plugin we can confirm that particular content on your page gets attention to user. 
Author: Gopi Ramasamy
Version: 7.6
Author URI: http://www.gopiplus.com/work/2011/07/23/scrolling-down-popup-wordpress-plugin/
Donate link: http://www.gopiplus.com/work/2011/07/23/scrolling-down-popup-wordpress-plugin/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

global $wpdb, $wp_version;
define("WP_Scrolling_Down_Popup_TABLE", $wpdb->prefix . "scrolling_down_popup");
define('WP_SDP_FAV', 'http://www.gopiplus.com/work/2011/07/23/scrolling-down-popup-wordpress-plugin/');

if ( ! defined( 'WP_SDP_BASENAME' ) )
	define( 'WP_SDP_BASENAME', plugin_basename( __FILE__ ) );
	
if ( ! defined( 'WP_SDP_PLUGIN_NAME' ) )
	define( 'WP_SDP_PLUGIN_NAME', trim( dirname( WP_SDP_BASENAME ), '/' ) );
	
if ( ! defined( 'WP_SDP_PLUGIN_URL' ) )
	define( 'WP_SDP_PLUGIN_URL', WP_PLUGIN_URL . '/' . WP_SDP_PLUGIN_NAME );
	
if ( ! defined( 'WP_SDP_ADMIN_URL' ) )
	define( 'WP_SDP_ADMIN_URL', get_option('siteurl') . '/wp-admin/options-general.php?page=scrolling-down-popup-plugin' );

function SDPopup($id)
{
	if(is_home() && get_option('sdp_On_Homepage') == 'YES') {	$display = "show";	}
	if(is_single() && get_option('sdp_On_Posts') == 'YES') {	$display = "show";	}
	if(is_page() && get_option('sdp_On_Pages') == 'YES') {	$display = "show";	}
	if(is_archive() && get_option('sdp_On_Archives') == 'YES') {	$display = "show";	}
	if(is_search() && get_option('sdp_On_Search') == 'YES') {	$display = "show";	}
	
	if(is_numeric($id)) 
	{
		$value = $id;
	}
	else
	{
		$value = "RANDOM";
	}
	
	if($display == "show")
	{
		Scrolling_Down_Popup_Show($value);
	}
}

function Scrolling_Down_Popup_Show($value)
{
	global $wpdb;
	
	$sdp_cookies = get_option('sdp_cookies');
	//$sdp_widget = get_option('sdp_widget');
	
	if($sdp_cookies == "showalways")
	{
		$sdp_cookies = "showalways";
	}
	else
	{
		$sdp_cookies = "oncepersession";
	}
	
	$sSql = "select * from ".WP_Scrolling_Down_Popup_TABLE." where 1=1";
	
	if($value == "RANDOM")
	{
		$sSql = $sSql . " Order by rand()";
	}
	else
	{
		if(is_numeric(@$value)) 
		{
			$sSql = $sSql . " and sdp_id=$value";
		}
	}
	
	$sSql = $sSql . " LIMIT 0,1";
	
	$sdp_width = 300;
	$sdp_left_space = 500;
	$sdp_top_space = 200;
	$sdp_speed = 15;
	$sdp_border = "2px solid #CCC";
	$sdp_background = "#FFFFFF";
	$sdp_closebutton = "right-top";
	$sdp_font = "";
	$sdp_font_size = "";
	$data = $wpdb->get_results($sSql);
	if ( ! empty($data) ) 
	{
		$data = $data[0];
		$sdp_temp = stripslashes($data->sdp_text);
		$sdp_temp = do_shortcode($sdp_temp);
		$sdp_text = $sdp_temp;	
		$sdp_width = $data->sdp_width;
		$sdp_left_space = $data->sdp_left_space;
		$sdp_top_space = $data->sdp_top_space;
		$sdp_speed = $data->sdp_speed;
		$sdp_border = $data->sdp_border;
		$sdp_background = $data->sdp_background;
		$sdp_closebutton = $data->sdp_closebutton;
		$sdp_font = $data->sdp_font;
		$sdp_font_size = $data->sdp_font_size;
	}
	else
	{
		$sdp_text = __('Check your short code, May be invalid ID in the shortcode.', 'scrolling-down');
	}
	
	if($sdp_font <> "") { $sdp_font = "font-family: ".$sdp_font.";"; }
	if($sdp_border <> "") { $sdp_border = "border: ".$sdp_border.";"; }
	if($sdp_background <> "") { $sdp_background = "background-color: ".$sdp_background.";"; }
	//left-top/right-top/left-bottom/right-bottom
	if($sdp_closebutton == "left-top")
	{
		$sdp_closebutton = "left: 5px;top:5px;";
	}
	else if($sdp_closebutton == "right-top")
	{
		$sdp_closebutton = "right: 5px;top:5px;";
	}
	else if($sdp_closebutton == "left-bottom")
	{
		$sdp_closebutton = "left: 5px;bottom:5px;";
	}
	else if($sdp_closebutton == "right-bottom")
	{
		$sdp_closebutton = "right: 5px;bottom:5px;";
	}
	else
	{
		$sdp_closebutton = "right: 5px;bottom:5px;";
	}
	
	if(!is_numeric($sdp_width)) { $sdp_width = 300 ;}
	if(!is_numeric($sdp_left_space)) { $sdp_left_space = 500 ;}
	if(!is_numeric($sdp_top_space)) { $sdp_top_space = 200 ;}
	if(!is_numeric($sdp_speed)) { $sdp_speed = 15 ;}
	if(!is_numeric($sdp_font_size)) { $sdp_font_size = 11 ;}
	
	$sdp_width_new = $sdp_width - 20;
	?>
    <style type="text/css">
	#scrolling-down-popup-plugin-top{
	width: <?php echo $sdp_width; ?>px;
	position:absolute;
	z-index: 100;
	overflow:hidden;
	visibility: hidden;
	}

	#scrolling-down-popup-plugin{
	width: <?php echo $sdp_width_new; ?>px; 
	<?php echo $sdp_border; ?>
	margin: 0 auto;
	<?php echo $sdp_background; ?>
	<?php echo $sdp_font; ?>
	font-size: <?php echo sdp_font_size; ?>px;
	padding: 4px;
	position:absolute;
	left: 0;
	top: 0;
	}
	</style>
    <script type="text/javascript">
		var scrolling_down_popup_plugin_left_space = <?php echo $sdp_left_space; ?>;
		var scrolling_down_popup_plugin_top_space = <?php echo $sdp_top_space; ?>;
		var scrolling_down_popup_plugin_speed = <?php echo $sdp_speed; ?>;
		var scrolling_down_popup_plugin_cookies = "<?php echo $sdp_cookies; ?>";
	</script>
    <div id="scrolling-down-popup-plugin-top">
        <div id="scrolling-down-popup-plugin"> 
            <div style="position: absolute;<?php echo $sdp_closebutton; ?>"><a href="#" onClick="dismissboxv2();return false"><img src="<?php echo WP_SDP_PLUGIN_URL; ?>/close.jpg" /></a></div>
			<?php echo $sdp_text; ?>
        </div>
    </div>
    <script type="text/javascript" src="<?php echo WP_SDP_PLUGIN_URL; ?>/scrolling-down-popup-plugin.js"></script>
    <?php
}

function Scrolling_Down_Popup_activation()
{
	global $wpdb;
	$c1 = "";
	$c2 = "";
	if($wpdb->get_var("show tables like '". strtoupper(WP_Scrolling_Down_Popup_TABLE) . "'") != strtoupper(WP_Scrolling_Down_Popup_TABLE)) 
	{
		$wpdb->query("
			CREATE TABLE IF NOT EXISTS `". WP_Scrolling_Down_Popup_TABLE . "` (
			  `sdp_id` int(11) NOT NULL auto_increment,
			  `sdp_text` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
			  `sdp_width` int(11) NOT NULL,
			  `sdp_left_space` int(11) NOT NULL,
			  `sdp_top_space` int(11) NOT NULL,
			  `sdp_speed` int(11) NOT NULL,
			  `sdp_border` VARCHAR(30) NOT NULL,
			  `sdp_background` VARCHAR(10) NOT NULL,
			  `sdp_closebutton` VARCHAR(20) NOT NULL,
			  `sdp_font` VARCHAR(100) NOT NULL,
			  `sdp_font_size` VARCHAR(10) NOT NULL,
			  `sdp_date` datetime NOT NULL default '0000-00-00 00:00:00',
			  PRIMARY KEY  (`sdp_id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
			");
		
		$c1 = $c1.'<p align="left"><img style="margin: 5px;text-align:left;float:left;" title="Gopi" src="'.get_option('siteurl').'/wp-content/plugins/scrolling-down-popup-plugin/gopiplus.com-popup.png" alt="Gopi" />This is the demo for cool fade popup plugin. using this plugin you can add this cool popup window into your wordpress website. using this unblockable popup window  you can add your ads, special information, offers and announcements. Close this popup and read the article you can easily configure this plugin in your wordpress website. its very simple. please feel free to post you comments and feedback.</p>';
		$c2 = $c2.'<p align="left"><img style="margin: 5px;text-align:left;float:right;" title="Gopi" src="'.get_option('siteurl').'/wp-content/plugins/scrolling-down-popup-plugin/gopiplus.com-popup.png" alt="Gopi" />This is the demo for cool fade popup plugin. using this plugin you can add this cool popup window into your wordpress website. using this unblockable popup window  you can add your ads, special information, offers and announcements. Close this popup and read the article you can easily configure this plugin in your wordpress website. its very simple. please feel free to post you comments and feedback.</p>';
		
		$iIns = "INSERT INTO `". WP_Scrolling_Down_Popup_TABLE . "` (`sdp_text`, `sdp_width`, `sdp_left_space`, `sdp_top_space`, `sdp_speed`, `sdp_border`, `sdp_background`, `sdp_closebutton`, `sdp_font`, `sdp_font_size`)"; 
		$sSql = $iIns . " VALUES ('$c1', 320, 500, 200, 15, '2px solid #666', '#FFFFFF' , 'right-bottom', 'Verdana, Geneva, sans-serif', '11');";
		$wpdb->query($sSql);
		$sSql = $iIns . " VALUES ('$c2', 420, 0, 0, 15, '2px solid #000000', '#DFDFFF' , 'right-bottom', 'Comic Sans MS, cursive', '11');";
		$wpdb->query($sSql);
		$sSql = $iIns . " VALUES ('$c1', 520, 500, 200, 15, '2px solid #oeoeoe', '#FFFFFF' , 'right-bottom', 'Verdana, Geneva, sans-serif', '11');";
		$wpdb->query($sSql);
	}
	add_option('sdp_cookies', "showalways");
	//add_option('sdp_widget', "RANDOM");
	add_option('sdp_On_Homepage', "YES");
	add_option('sdp_On_Posts', "YES");
	add_option('sdp_On_Pages', "YES");
	add_option('sdp_On_Archives', "NO");
	add_option('sdp_On_Search', "NO");
}

function Scrolling_Down_Popup_deactivate()
{
	// No action required.
}

function Scrolling_Down_Popup_add_to_menu()
{
	if (is_admin()) 
	{
		add_options_page(__('Scrolling down popup', 'scrolling-down'),
					__('Scrolling down popup', 'scrolling-down'),'manage_options', 'scrolling-down-popup-plugin','Scrolling_Down_Popup_admin_options');  
	}
}

function Scrolling_Down_Popup_admin_options()
{
	global $wpdb;
	$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
	switch($current_page)
	{
		case 'edit':
			include('pages/content-management-edit.php');
			break;
		case 'add':
			include('pages/content-management-add.php');
			break;
		case 'set':
			include('pages/content-setting.php');
			break;
		default:
			include('pages/content-management-show.php');
			break;
	} 
}

add_shortcode( 'scroll-down-popup', 'Scrolling_Down_Popup_shortcode' );

function Scrolling_Down_Popup_shortcode($atts) 
{
	global $wpdb;
	
	//$scode = $matches[1];
	$sdp = "";
	
	//[scroll-down-popup id="1"]
	if ( ! is_array( $atts ) )
	{
		return '';
	}
	$scode = $atts['id'];
	
	$sdp_cookies = get_option('sdp_cookies');
	
	if($sdp_cookies == "showalways")
	{
		$sdp_cookies = "showalways";
	}
	else
	{
		$sdp_cookies = "oncepersession";
	}
	
	$sSql = "select * from ".WP_Scrolling_Down_Popup_TABLE." where 1=1";
	
	if(is_numeric(@$scode)) 
	{
		$sSql = $sSql . " and sdp_id=$scode";
	}
	
	$sSql = $sSql . " LIMIT 0,1";
	
	$sdp_width = 300;
	$sdp_left_space = 500;
	$sdp_top_space = 200;
	$sdp_speed = 15;
	$sdp_border = "2px solid #CCC";
	$sdp_background = "#FFFFFF";
	$sdp_closebutton = "right-top";
	$sdp_font = "";
	$sdp_font_size = "";

	$data = $wpdb->get_results($sSql);
	if ( ! empty($data) ) 
	{
		$data = $data[0];
		$sdp_temp = stripslashes($data->sdp_text);
		$sdp_temp = do_shortcode($sdp_temp);
		$sdp_text = $sdp_temp;
		$sdp_width = $data->sdp_width;
		$sdp_left_space = $data->sdp_left_space;
		$sdp_top_space = $data->sdp_top_space;
		$sdp_speed = $data->sdp_speed;
		$sdp_border = $data->sdp_border;
		$sdp_background = $data->sdp_background;
		$sdp_closebutton = $data->sdp_closebutton;
		$sdp_font = $data->sdp_font;
		$sdp_font_size = $data->sdp_font_size;
	}
	else
	{
		$sdp_text = __('Check your short code, May be invalid ID in the shortcode.', 'scrolling-down');
	}
	
	if($sdp_font <> "") { $sdp_font = "font-family: ".$sdp_font.";"; }
	if($sdp_border <> "") { $sdp_border = "border: ".$sdp_border.";"; }
	if($sdp_background <> "") { $sdp_background = "background-color: ".$sdp_background.";"; }
	//left-top/right-top/left-bottom/right-bottom
	if($sdp_closebutton == "left-top")
	{
		$sdp_closebutton = "left: 5px;top:5px;";
	}
	else if($sdp_closebutton == "right-top")
	{
		$sdp_closebutton = "right: 5px;top:5px;";
	}
	else if($sdp_closebutton == "left-bottom")
	{
		$sdp_closebutton = "left: 5px;bottom:5px;";
	}
	else if($sdp_closebutton == "right-bottom")
	{
		$sdp_closebutton = "right: 5px;bottom:5px;";
	}
	else
	{
		$sdp_closebutton = "right: 5px;bottom:5px;";
	}
	
	if(!is_numeric($sdp_width)) { $sdp_width = 300 ;}
	if(!is_numeric($sdp_left_space)) { $sdp_left_space = 500 ;}
	if(!is_numeric($sdp_top_space)) { $sdp_top_space = 200 ;}
	if(!is_numeric($sdp_speed)) { $sdp_speed = 15 ;}
	if(!is_numeric($sdp_font_size)) { $sdp_font_size = 11 ;}
	
	$sdp_width_new = $sdp_width - 20;

    $sdp = $sdp . '<style type="text/css">';
	$sdp = $sdp . '#scrolling-down-popup-plugin-top{';
	$sdp = $sdp . 'width: '.$sdp_width.'px;';
	$sdp = $sdp . 'position:absolute;';
	$sdp = $sdp . 'z-index: 100;';
	$sdp = $sdp . 'overflow:hidden;';
	$sdp = $sdp . 'visibility: hidden;';
	$sdp = $sdp . '}';
	
	$sdp = $sdp . '#scrolling-down-popup-plugin{';
	$sdp = $sdp . 'width: '.$sdp_width_new.'px; ';
	$sdp = $sdp . $sdp_border;
	$sdp = $sdp . 'margin: 0 auto;';
	$sdp = $sdp . $sdp_background;
	$sdp = $sdp . $sdp_font;
	$sdp = $sdp . 'padding: 4px;';
	$sdp = $sdp . 'font-size: '.$sdp_font_size.'px; ';
	$sdp = $sdp . 'position:absolute;';
	$sdp = $sdp . 'left: 0;';
	$sdp = $sdp . 'top: 0;';
	$sdp = $sdp . '}';
	$sdp = $sdp . '</style>';
	
    $sdp = $sdp . '<script type="text/javascript">';
		$sdp = $sdp . 'var scrolling_down_popup_plugin_left_space = '.$sdp_left_space.';';
		$sdp = $sdp . 'var scrolling_down_popup_plugin_top_space = '.$sdp_top_space.';';
		$sdp = $sdp . 'var scrolling_down_popup_plugin_speed = '.$sdp_speed.';';
		$sdp = $sdp . 'var scrolling_down_popup_plugin_cookies = "'.$sdp_cookies.'";';
	$sdp = $sdp . '</script>';
	
    $sdp = $sdp . '<div id="scrolling-down-popup-plugin-top">';
        $sdp = $sdp . '<div id="scrolling-down-popup-plugin">';
            $sdp = $sdp . '<div style="position: absolute;'.$sdp_closebutton.'"><a href="#" onClick="dismissboxv2();return false"><img src="'.WP_SDP_PLUGIN_URL.'/close.jpg" style="border-radius:0"/></a></div>';
			$sdp = $sdp . $sdp_text;
        $sdp = $sdp . '</div>';
    $sdp = $sdp . '</div>';
	
    $sdp = $sdp . '<script type="text/javascript" src="'.WP_SDP_PLUGIN_URL.'/scrolling-down-popup-plugin.js"></script>';
	return $sdp;
}

function Scrolling_Down_Popup_textdomain() 
{
	  load_plugin_textdomain( 'scrolling-down', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action('plugins_loaded', 'Scrolling_Down_Popup_textdomain');
register_activation_hook(__FILE__, 'Scrolling_Down_Popup_activation');
add_action('admin_menu', 'Scrolling_Down_Popup_add_to_menu');
register_deactivation_hook( __FILE__, 'Scrolling_Down_Popup_deactivate');
?>