<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$sdp_errors = array();
$sdp_success = '';
$sdp_error_found = FALSE;

// Preset the form fields
$form = array(
	'sdp_id' => '',
	'sdp_text' => '',
	'sdp_width' => '',
	'sdp_left_space' => '',
	'sdp_top_space' => '',
	'sdp_speed' => '',
	'sdp_border' => '',
	'sdp_background' => '',
	'sdp_closebutton' => '',
	'sdp_font' => '',
	'sdp_font_size' => '',
	'sdp_date' => ''
);

// Form submitted, check the data
if (isset($_POST['sdp_form_submit']) && $_POST['sdp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('sdp_form_add');
	
	$form['sdp_text'] = isset($_POST['sdp_text']) ? $_POST['sdp_text'] : '';
	if ($form['sdp_text'] == '')
	{
		$sdp_errors[] = __('Please enter popup text.', 'scrolling-down');
		$sdp_error_found = TRUE;
	}
	$form['sdp_width'] = isset($_POST['sdp_width']) ? $_POST['sdp_width'] : '';
	if ($form['sdp_width'] == '')
	{
		$sdp_errors[] = __('Please enter popup width.', 'scrolling-down');
		$sdp_error_found = TRUE;
	}
	$form['sdp_left_space'] = isset($_POST['sdp_left_space']) ? $_POST['sdp_left_space'] : '';
	if ($form['sdp_left_space'] == '')
	{
		$sdp_errors[] = __('Please enter popup left space.', 'scrolling-down');
		$sdp_error_found = TRUE;
	}
	$form['sdp_top_space'] = isset($_POST['sdp_top_space']) ? $_POST['sdp_top_space'] : '';
	if ($form['sdp_top_space'] == '')
	{
		$sdp_errors[] = __('Please enter popup top space.', 'scrolling-down');
		$sdp_error_found = TRUE;
	}
	
	$form['sdp_speed'] = isset($_POST['sdp_speed']) ? $_POST['sdp_speed'] : '';
	$form['sdp_border'] = isset($_POST['sdp_border']) ? $_POST['sdp_border'] : '';
	$form['sdp_background'] = isset($_POST['sdp_background']) ? $_POST['sdp_background'] : '';
	$form['sdp_closebutton'] = isset($_POST['sdp_closebutton']) ? $_POST['sdp_closebutton'] : '';
	$form['sdp_font'] = isset($_POST['sdp_font']) ? $_POST['sdp_font'] : '';
	$form['sdp_font_size'] = isset($_POST['sdp_font_size']) ? $_POST['sdp_font_size'] : '';

	//	No errors found, we can add this Group to the table
	if ($sdp_error_found == FALSE)
	{
		$cur_date = date('Y-m-d G:i:s'); 
		$sql = $wpdb->prepare(
			"INSERT INTO `".WP_Scrolling_Down_Popup_TABLE."`
			(`sdp_text`, `sdp_width`, `sdp_left_space`, `sdp_top_space`, `sdp_speed`, `sdp_border`, `sdp_background`, `sdp_closebutton`, `sdp_font`, `sdp_font_size`, `sdp_date`)
			VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			array($form['sdp_text'], $form['sdp_width'], $form['sdp_left_space'], $form['sdp_top_space'], $form['sdp_speed'], 
					$form['sdp_border'], $form['sdp_background'], $form['sdp_closebutton'], $form['sdp_font'], $form['sdp_font_size'], $cur_date)
		);
		$wpdb->query($sql);
		
		$sdp_success = __('Details was successfully added.', 'scrolling-down');
		
		// Reset the form fields
		$form = array(
			'sdp_id' => '',
			'sdp_text' => '',
			'sdp_width' => '',
			'sdp_left_space' => '',
			'sdp_top_space' => '',
			'sdp_speed' => '',
			'sdp_border' => '',
			'sdp_background' => '',
			'sdp_closebutton' => '',
			'sdp_font' => '',
			'sdp_font_size' => '',
			'sdp_date' => ''
		);
	}
}

if ($sdp_error_found == TRUE && isset($sdp_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $sdp_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($sdp_error_found == FALSE && strlen($sdp_success) > 0)
{
	?>
	  <div class="updated fade">
		<p><strong><?php echo $sdp_success; ?> 
		<a href="<?php echo WP_SDP_ADMIN_URL; ?>"><?php _e('Click Here', 'scrolling-down'); ?></a> <?php _e('View the details', 'scrolling-down'); ?></strong></p>
	  </div>
	  <?php
	}
?>
<script language="JavaScript" src="<?php echo WP_SDP_PLUGIN_URL; ?>/pages/scrolling-down-popup-plugin-setting.js"></script>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('Scrolling down popup plugin', 'scrolling-down'); ?></h2>
	<form name="sdp_form" method="post" action="#" onsubmit="return _sdp_submit()"  >
      <h3><?php _e('Add popop', 'scrolling-down'); ?></h3>
      <label for="tag-image"><?php _e('Popup text', 'scrolling-down'); ?></label>
	  <textarea name="sdp_text" id="sdp_text" cols="130" rows="6"></textarea>
      <p><?php _e('Add popup text in the box, we can add HTML content', 'scrolling-down'); ?></p>
	  
      <label for="tag-link"><?php _e('Popup window width', 'scrolling-down'); ?></label>
      <input name="sdp_width" type="text" id="sdp_width" value="300" maxlength="4" />
      <p><?php _e('Enter width of the popup window, this is mandatory field.', 'scrolling-down'); ?> (Ex: 300)</p>
	  
	  <label for="tag-link"><?php _e('Position (left space)', 'scrolling-down'); ?></label>
      <input name="sdp_left_space" type="text" id="sdp_left_space" value="500" maxlength="4" />
      <p><?php _e('Enter window left position, this is mandatory field.', 'scrolling-down'); ?> (Ex: 500).</p>
	  
	  <label for="tag-link"><?php _e('Position (top space)', 'scrolling-down'); ?></label>
      <input name="sdp_top_space" type="text" id="sdp_top_space" value="200" maxlength="4" />
      <p><?php _e('Enter window top position, this is mandatory field.', 'scrolling-down'); ?> (Ex: 200).</p>
	  
	  <label for="tag-link"><?php _e('Scrolling speed', 'scrolling-down'); ?></label>
      <input name="sdp_speed" type="text" id="sdp_speed" value="15" maxlength="4" />
      <p><?php _e('Enter scrolling speed, this is mandatory field.', 'scrolling-down'); ?> (Ex: 15).</p>
	  
	  <label for="tag-link"><?php _e('Popup window border', 'scrolling-down'); ?></label>
      <input name="sdp_border" type="text" id="sdp_border" value="2px solid #666" maxlength="75" />
      <p><?php _e('Enter popup window border as per example.', 'scrolling-down'); ?> (Ex: 2px solid #666).</p>
	  
	  <label for="tag-link"><?php _e('Close button position', 'scrolling-down'); ?></label>
      <select name="sdp_closebutton" id="sdp_closebutton">
        <option value='left-top'>Left Top</option>
        <option value='right-top'>Right Top</option>
        <option value='left-bottom'>Left Bottom</option>
        <option value='right-bottom'>Right Bottom</option>
      </select>
      <p><?php _e('Select popup window close button position.', 'scrolling-down'); ?></p>
	  
	  <label for="tag-link"><?php _e('Background color', 'scrolling-down'); ?></label>
      <input name="sdp_background" type="text" id="sdp_background" value="#FFFFFF" maxlength="10" />
      <p><?php _e('Enter background color of the popup window as per example.', 'scrolling-down'); ?> (Ex: #FFFFFF)</p>
	  
	  <label for="tag-link"><?php _e('Font name', 'scrolling-down'); ?></label>
      <input name="sdp_font" type="text" id="sdp_font" value="" maxlength="75" />
      <p><?php _e('Enter popup window font as per example.', 'scrolling-down'); ?> (Ex: Verdana, Geneva, sans-serif)</p>
	  
	  <label for="tag-link"><?php _e('Font size', 'scrolling-down'); ?></label>
      <input name="sdp_font_size" type="text" id="sdp_font_size" value="" maxlength="3" />
      <p><?php _e('Enter the popup window font style as per example.', 'scrolling-down'); ?> (Ex: 11)</p>
      
      <input name="sdp_id" id="sdp_id" type="hidden" value="">
      <input type="hidden" name="sdp_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Insert Details', 'scrolling-down'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_sdp_redirect()" value="<?php _e('Cancel', 'scrolling-down'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_sdp_help()" value="<?php _e('Help', 'scrolling-down'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('sdp_form_add'); ?>
    </form>
</div>
<p class="description">
	<?php _e('Check official website for more information', 'scrolling-down'); ?>
	<a target="_blank" href="<?php echo WP_SDP_FAV; ?>"><?php _e('click here', 'scrolling-down'); ?></a>
</p>
</div>