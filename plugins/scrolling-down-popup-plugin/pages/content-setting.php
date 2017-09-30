<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <div class="form-wrap">
    <div id="icon-edit" class="icon32 icon32-posts-post"><br>
    </div>
    <h2><?php _e('Scrolling down popup plugin', 'scrolling-down'); ?></h2>
    <?php
	$sdp_On_Homepage = get_option('sdp_On_Homepage');
	$sdp_On_Posts = get_option('sdp_On_Posts');
	$sdp_On_Pages = get_option('sdp_On_Pages');
	$sdp_On_Archives = get_option('sdp_On_Archives');
	$sdp_On_Search = get_option('sdp_On_Search');
	$sdp_cookies = get_option('sdp_cookies');
	//$sdp_widget = get_option('sdp_widget');
	//$sdp_close = get_option('sdp_close');
	
	if (isset($_POST['sdp_form_submit']) && $_POST['sdp_form_submit'] == 'yes')
	{
		//	Just security thingy that wordpress offers us
		check_admin_referer('sdp_form_setting');
			
		$sdp_On_Homepage = stripslashes(trim($_POST['sdp_On_Homepage']));
		$sdp_On_Posts = stripslashes(trim($_POST['sdp_On_Posts']));
		$sdp_On_Pages = stripslashes(trim($_POST['sdp_On_Pages']));
		$sdp_On_Archives = stripslashes(trim($_POST['sdp_On_Archives']));
		$sdp_On_Search = stripslashes(trim($_POST['sdp_On_Search']));
		$sdp_cookies = stripslashes(trim($_POST['sdp_cookies']));
		//$sdp_widget = stripslashes(trim($_POST['sdp_widget']));
		//$sdp_close = stripslashes(trim($_POST['sdp_close']));
		
		update_option('sdp_On_Homepage', $sdp_On_Homepage );
		update_option('sdp_On_Posts', $sdp_On_Posts );
		update_option('sdp_On_Pages', $sdp_On_Pages );
		update_option('sdp_On_Archives', $sdp_On_Archives );
		update_option('sdp_On_Search', $sdp_On_Search );
		update_option('sdp_cookies', $sdp_cookies );
		//update_option('sdp_widget', $sdp_widget );
		//update_option('sdp_close', $sdp_close );
		
		?>
		<div class="updated fade">
			<p><strong><?php _e('Details successfully updated.', 'scrolling-down'); ?></strong></p>
		</div>
		<?php
	}
	?>
	<script language="JavaScript" src="<?php echo WP_SDP_PLUGIN_URL; ?>/pages/scrolling-down-popup-plugin-setting.js"></script>
	<h3>Popup setting</h3>
	<form name="sdp_form" method="post" action="">
	
		<label for="tag-title"><?php _e('Display Mode (Global setting)', 'scrolling-down'); ?></label>
		<select name="sdp_cookies" id="sdp_cookies">
			<option value='showalways' <?php if($sdp_cookies=='showalways') { echo 'selected' ; } ?>><?php _e('Show always', 'scrolling-down'); ?></option>
			<option value='oncepersession' <?php if($sdp_cookies=='oncepersession') { echo 'selected' ; } ?>><?php _e('Once per session', 'scrolling-down'); ?></option>
		</select>
		<p></p>
		
		<h3><?php _e('Popup window display setting', 'scrolling-down'); ?></h3>
		
		<label for="tag-image"><?php _e('Display on home page', 'scrolling-down'); ?></label>
		<select name="sdp_On_Homepage" id="sdp_On_Homepage">
			<option value='YES' <?php if($sdp_On_Homepage == 'YES') { echo 'selected' ; } ?>>YES</option>
			<option value='NO' <?php if($sdp_On_Homepage == 'NO') { echo 'selected' ; } ?>>NO</option>
		</select>
		<p></p>
		
		<label for="tag-image"><?php _e('Display on posts', 'scrolling-down'); ?></label>
		<select name="sdp_On_Posts" id="sdp_On_Posts">
			<option value='YES' <?php if($sdp_On_Posts == 'YES') { echo 'selected' ; } ?>>YES</option>
			<option value='NO' <?php if($sdp_On_Posts == 'NO') { echo 'selected' ; } ?>>NO</option>
		</select>
		<p></p>
		
		<label for="tag-image"><?php _e('Display on pages', 'scrolling-down'); ?></label>
		<select name="sdp_On_Pages" id="sdp_On_Pages">
			<option value='YES' <?php if($sdp_On_Pages == 'YES') { echo 'selected' ; } ?>>YES</option>
			<option value='NO' <?php if($sdp_On_Pages == 'NO') { echo 'selected' ; } ?>>NO</option>
		</select>
		<p></p>
		
		<label for="tag-image"><?php _e('Display on archives pages', 'scrolling-down'); ?></label>
		<select name="sdp_On_Archives" id="sdp_On_Archives">
			<option value='YES' <?php if($sdp_On_Archives == 'YES') { echo 'selected' ; } ?>>YES</option>
			<option value='NO' <?php if($sdp_On_Archives == 'NO') { echo 'selected' ; } ?>>NO</option>
		</select>
		<p></p>
		
		<label for="tag-image"><?php _e('Display on search pages', 'scrolling-down'); ?></label>
		<select name="sdp_On_Search" id="sdp_On_Search">
			<option value='YES' <?php if($sdp_On_Search == 'YES') { echo 'selected' ; } ?>>YES</option>
			<option value='NO' <?php if($sdp_On_Search == 'NO') { echo 'selected' ; } ?>>NO</option>
		</select>
		<p></p>
		<br />		
		<input type="hidden" name="sdp_form_submit" value="yes"/>
		<input name="sdp_submit" id="sdp_submit" class="button add-new-h2" value="<?php _e('Submit', 'scrolling-down'); ?>" type="submit" />
		<input name="publish" lang="publish" class="button add-new-h2" onclick="_sdp_redirect()" value="<?php _e('Cancel', 'scrolling-down'); ?>" type="button" />
		<input name="Help" lang="publish" class="button add-new-h2" onclick="_sdp_help()" value="<?php _e('Help', 'scrolling-down'); ?>" type="button" />
		<?php wp_nonce_field('sdp_form_setting'); ?>
	</form>
  </div>
  <br />
  	<p class="description">
		<?php _e('Check official website for more information', 'scrolling-down'); ?>
		<a target="_blank" href="<?php echo WP_SDP_FAV; ?>"><?php _e('click here', 'scrolling-down'); ?></a>
	</p>
</div>