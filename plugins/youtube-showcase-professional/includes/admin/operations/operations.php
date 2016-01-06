<?php
/**
 * Operations Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
add_action('admin_init', 'emd_entity_export');
/**
 * Export entity attributes and its taxonomies to a csv file
 * @since WPAS 4.0
 *
 * @return file export csv file
 */
function emd_entity_export() {
	if (isset($_POST['emd_operations_export']) && isset($_POST['post_type'])) {
		$post_type = $_POST['post_type'];
		if (check_admin_referer('emd_export_nonce', 'emd_export_nonce')) {
			$plural_label = $_POST['plural_label'];
			$entity_fields = get_option($_POST['myapp'] . '_attr_list');
			$postslist = get_posts(array(
				'post_type' => $post_type,
				'numberposts' => - 1,
				'post_status' => 'any'
			));
			if (!empty($postslist)) {
				$headers = Array(
					'WP ' . __('Author', 'emd-plugins') ,
					'WP ' . __('Status', 'emd-plugins') ,
					'WP ' . __('Date', 'emd-plugins') ,
					'WP ' . __('Title', 'emd-plugins') ,
					'WP ' . __('Excerpt', 'emd-plugins') ,
					'WP ' . __('Content', 'emd-plugins')
				);
				$rows[] = Array(
					'key' => 'author',
					'type' => 'builtin'
				);
				$rows[] = Array(
					'key' => 'status',
					'type' => 'builtin'
				);
				$rows[] = Array(
					'key' => 'post_date',
					'type' => 'builtin'
				);
				$rows[] = Array(
					'key' => 'title',
					'type' => 'builtin'
				);
				$rows[] = Array(
					'key' => 'excerpt',
					'type' => 'builtin'
				);
				$rows[] = Array(
					'key' => 'content',
					'type' => 'builtin'
				);
				$tax_list = get_taxonomies(array(
					'object_type' => array(
						$post_type
					)
				) , 'object');
				if (!empty($tax_list)) {
					foreach ($tax_list as $key_tax => $tax_val) {
						$rows[] = Array(
							'key' => $key_tax,
							'type' => 'tax'
						);
						$headers[] = $tax_val->labels->name;
					}
				}
				if(!empty($entity_fields[$post_type])){
					foreach ($entity_fields[$post_type] as $key_ent => $myentity_field) {
						$rows[] = Array(
							'key' => $key_ent,
							'type' => 'ent'
						);
						$headers[] = $myentity_field['label'];
					}
				}
				$date = date("d_m_Y");
				$fileName = sanitize_file_name($plural_label . '_' . $date . '.csv');
				$output = implode(" , ", $headers);
				$output = str_replace(',', '","', $output);
				$output = '"' . $output . '"' . "\n";
				$output_rows = Array();
				foreach ($postslist as $mypost) {
					$output_cols = Array();
					foreach ($rows as $myrow) {
						switch ($myrow['type']) {
							case 'builtin':
								$output_cols[] = emd_get_builtin_val($myrow['key'], $mypost);
							break;
							case 'ent':
								$output_cols[] = emd_get_ent_val($myrow['key'], $entity_fields[$post_type][$myrow['key']]['display_type'], $mypost);
							break;
							case 'tax':
								$output_cols[] = emd_get_tax_val($myrow['key'], $mypost);
							break;
						}
					}
					$output_rows[] = '"' . implode('","', $output_cols) . '"';
				}
				$output.= implode("\r\n", $output_rows);
				header("Expires: Mon, 21 Nov 1997 05:00:00 GMT"); // Date in the past
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-cache, must-revalidate");
				header("Pragma: no-cache");
				header('Content-type: application/csv;charset=UTF-8');
				header('Content-Disposition: attachment; filename=' . $fileName);
				header('Content-Description: File Transfer');
				print ($output);
				exit;
			} else {
				_e('There is no data to export', 'emd-plugins');
			}
		}
	}
}
/**
 * Display all operations tabs(import,reset,export) and call funcs to process them
 * @since WPAS 4.0
 * @param string $post_type
 * @param string $plural_label
 * @param string $label
 * @param string $myapp
 *
 */
function emd_operations_entity($post_type, $plural_label, $label, $myapp, $tab_ptype='') {
	if($tab_ptype == ''){
		$tab_ptype = $post_type;
	}
	if (isset($_REQUEST['tab']) && in_array($_REQUEST['tab'], Array(
		'import',
		'export',
		'reset',
		'map-user',
	))) {
		$tab = $_REQUEST['tab'];
	} else {
		$tab = "import";
	}

	$has_user = 0;
	$ent_list = get_option($myapp . '_ent_list');
	if(!empty($ent_list[$post_type]) && !empty($ent_list[$post_type]['user_key'])){
		$has_user = 1;
	}

	$tab_url = get_admin_url() . 'edit.php?post_type=' . $tab_ptype . '&page=operations_' . $post_type . '&tab=';
	switch ($tab) {
		case 'export':
			emd_show_opr_tabs($tab_url, $plural_label, $tab, $has_user);
?>
			</h3>
			<p> 
			<?php _e('When you click the button below a comma seperated file will be created for you to save to your computer.', 'emd-plugins');
			printf(__('This file will contain your %s taxonomies and attributes.', 'emd-plugins') , strtolower($label)); ?>
			</br>
			<?php printf(__('Once you have saved the download file, you can use the Import tab of the %s entity in another Wp App Studio application to import the %s content from this site.', 'emd-plugins') , strtolower($label) , strtolower($label)); ?>
			</p>
			<form action="" method="post" id="export_form">
			<fieldset class="submit">
			<?php wp_nonce_field('emd_export_nonce', 'emd_export_nonce'); ?>
			<input type="hidden" name="plural_label" value="<?php echo esc_attr($plural_label); ?>">
			<input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>">
			<input type="hidden" name="myapp" value="<?php echo esc_attr($myapp); ?>">
			<input type="submit" class="button-primary" id="emd_operations_export" name="emd_operations_export" value="<?php _e('Export Now', 'emd-plugins'); ?>">
			</fieldset></form>
			<?php
		break;
		case 'reset':
			emd_show_opr_tabs($tab_url, $plural_label, $tab, $has_user);
			echo '</h3>';
			if (isset($_POST['emd_operations_reset']) && check_admin_referer('emd_reset_nonce', 'emd_reset_nonce')) {
				emd_reset_vals($post_type, $plural_label, $myapp);
			} ?>
			<div class="wrap"><form action="" method="post" id="reset_form">
			<input type="checkbox" name="reset_all" id="reset_all" value=1>
			<?php printf(__('Delete all %s data.', 'emd-plugins') , $plural_label); ?>
			</input></br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="reset_tax" id="reset_tax" value=1>
			<?php printf(__('Delete all %s taxonomies.', 'emd-plugins') , $plural_label); ?>
			</input></br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="reset_meta" id="reset_meta" value=1>
			<?php printf(__('Delete all %s.', 'emd-plugins') , $plural_label); ?>
			</input></br>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="reset_rel" id="reset_rel" value=1>
			<?php printf(__('Delete all %s relationships.', 'emd-plugins') , $plural_label); ?>
			</input></br>
			<?php wp_nonce_field('emd_reset_nonce', 'emd_reset_nonce'); ?>
			<fieldset class="submit">
			<input type="submit" id="emd_operations_reset" name="emd_operations_reset" style="background-color:red; color:white;" value="<?php printf(__('Reset %s', 'emd-plugins') , $plural_label); ?>">
			</fieldset>
			</form></div>
			<?php
		break;
		case 'import':
			emd_show_opr_tabs($tab_url, $plural_label, $tab, $has_user);
			if (!isset($_POST['emd_operations_import']) && !isset($_POST['emd_import_step2'])) {
				$size = size_format(wp_max_upload_size());
				echo ' >> ' . __('Step 1: Upload Import File', 'emd-plugins'); ?>
				</h3>
				<p>
				<?php printf(__('Upload your comma separated (.csv) file containing %s taxonomies and attributes. First choose file to upload, then click Upload import File.', 'emd-plugins') , strtolower($label)); ?>
				<br>
				<?php printf(__('Choose a file from your computer: (Maximum size: %s)', 'emd-plugins') , $size); ?>
				</p>
				<form class="form-inline" name="import<?php echo esc_attr($post_type); ?>" enctype="multipart/form-data" 
				method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<input type="file" name="import<?php echo esc_attr($post_type); ?>" class="input-xlarge" id="import<?php echo $post_type; ?>">
				<?php wp_nonce_field('emd_import_nonce', 'emd_import_nonce'); ?>
				<button type="submit" class="button-primary" id="emd_operations_import" name="emd_operations_import">
				<?php _e('Upload Import File', 'emd-plugins'); ?>
				</button>
				</form>
				</div>
			<?php
			} elseif (isset($_POST['emd_operations_import']) && !empty($_FILES['import' . $post_type]['size']) && check_admin_referer('emd_import_nonce', 'emd_import_nonce')) {
				echo ' >> ' . __('Step 2: Confirm Fields and Import', 'emd-plugins'); ?>
				</h3>
				<p>
				<?php _e('In the list below, select the fields in the import file that should be imported into each field in the system. When you are finished, click Import Now:', 'emd-plugins'); ?>
				</p>
				<?php $overrides = array(
					'action' => 'import_' . $post_type,
					'mimes' => array(
						'csv' => 'text/csv'
					) ,
					'test_form' => false
				);
				$_POST['action'] = 'import_' . $post_type;
				$upload = wp_handle_upload($_FILES['import' . $post_type], $overrides);
				if (isset($upload['error'])) {
					echo '<b>' . __('Error:', 'emd-plugins') . $upload['error'] . '</b>';
					exit;
				}
				if (!empty($upload['file'])) {
					emd_import_step2($upload['file'], $myapp, $post_type, $label);
				}
			} elseif (isset($_POST['emd_import_step2']) && check_admin_referer('emd_import_nonce', 'emd_import_nonce')) {
				emd_import_step3($_POST['file'], $myapp, $post_type, $plural_label);
			}
			break;
		case 'map-user':
			emd_show_opr_tabs($tab_url, $plural_label, $tab, $has_user);
			echo '</h3>';
			emd_map_user_meta($myapp,$post_type,$label,$plural_label);
			break;
		} ?>
	</div>
<?php
}
/**
 * Import file and images from urls defined in import file
 * @since WPAS 4.0
 * @param string $uploaddir
 * @param string $meta_key
 * @param string $meta_value
 * @param string $id
 *
 */
function emd_import_file_image($uploaddir, $meta_key, $meta_value, $id) {
	$meta_value_arr = explode(";", $meta_value);
	foreach ($meta_value_arr as $mymeta_value) {
		$uploadfile = $uploaddir['path'] . '/' . basename($mymeta_value);
		if (file_exists($uploadfile)) {
			//save file
			$contents = file_get_contents($mymeta_value);
			$savefile = fopen($uploadfile, 'w');
			fwrite($savefile, $contents);
			fclose($savefile);
			$filetype = wp_check_filetype(basename($uploadfile));
			$attachment = array(
				'post_mime_type' => $filetype['type'],
				'guid' => $uploadfile,
				'post_title' => basename($uploadfile) ,
				'post_content' => '',
				'post_status' => 'inherit',
			);
			$insert_id = wp_insert_attachment($attachment, $uploadfile, $id);
			if (!is_wp_error($insert_id)) {
				wp_update_attachment_metadata($insert_id, wp_generate_attachment_metadata($insert_id, $uploadfile));
				// Save file ID in meta field
				add_post_meta($id, $meta_key, $insert_id, false);
			}
		}
	}
}
/**
 * Return builtin value from post data
 * @since WPAS 4.0
 * @param string $type
 * @param object $mypost
 *
 * @return string $ret value
 */
function emd_get_builtin_val($type, $mypost) {
	$ret = '';
	switch ($type) {
		case 'author':
			$author = get_userdata($mypost->post_author);
			$ret = $author->data->user_nicename;
		break;
		case 'status':
			$ret = $mypost->post_status;
		break;
		case 'post_date':
			$ret = $mypost->post_date;
		break;
		case 'title':
			$ret = $mypost->post_title;
		break;
		case 'excerpt':
			$ret = $mypost->post_excerpt;
		break;
		case 'content':
			$ret = $mypost->post_content;
		break;
	}
	if ($ret == '') {
		$ret = 'NULL';
	}
	return $ret;
}
/**
 * Return attribute value from post data
 * @since WPAS 4.0
 * @param string $key
 * @param string $type
 * @param object $mypost
 *
 * @return string $ret value
 */
function emd_get_ent_val($key, $type, $mypost) {
	$ret = '';
	switch ($type) {
		case 'file':
		case 'image':
		case 'plupload_image':
		case 'thickbox_image':
			$args = Array(
				'type' => $type
			);
			$meta = emd_mb_meta($key, $args, $mypost->ID);
			$meta_output = "";
			foreach ($meta as $mymeta) {
				$meta_output.= $mymeta['url'] . ";";
			}
			if (isset($meta_output) && $meta_output != "") {
				$ret = rtrim($meta_output, ';');
			}
		break;
		case 'checkbox_list':
			$args = Array(
				'type' => $type
			);
			$meta = emd_mb_meta($key, $args, $mypost->ID);
			$ret = implode(';', $meta);
		break;
		case 'wysiwyg':
			$wysiwyg = rtrim(get_post_meta($mypost->ID, $key, true) , "<br>");
			$ret = rtrim($wysiwyg);
		break;
		case 'text':
		case 'textarea':
			$ret = get_post_meta($mypost->ID, $key, true);
		break;
		case 'select':
			$select_list = get_post_meta($mypost->ID, $key, false);
			if (!empty($select_list)) {
				$ret = implode(';', $select_list);
			}
		break;
		default:
			$ret = get_post_meta($mypost->ID, $key, true);
		break;
	}
	if ($ret == '') {
		$ret = 'NULL';
	}
	$ret = str_replace(",", "\,", $ret);
	$ret = str_replace("\n", "\\n", $ret);
	$ret = str_replace("\r", "\\r", $ret);
	return $ret;
}
/**
 * Return taxonomy value from post data
 * @since WPAS 4.0
 * @param string $key
 * @param object $mypost
 *
 * @return string $term_output value
 */
function emd_get_tax_val($key, $mypost) {
	$term_output = '';
	$terms = wp_get_post_terms($mypost->ID, $key);
	foreach ($terms as $myterm) {
		$term_output.= $myterm->name . ";";
	}
	if (isset($term_output) && $term_output != "") {
		$term_output = rtrim($term_output, ";");
	}
	if ($term_output == '') {
		$term_output = 'NULL';
	}
	return $term_output;
}
/**
 * Show tabs in operations page
 * @since WPAS 4.0
 * @param string $tab_url
 * @param string $plural_label
 * @param string $mytab
 *
 */
function emd_show_opr_tabs($tab_url, $plural_label, $mytab,$has_user) {
	$tabs = Array(
		'import' => __('Import', 'emd-plugins') ,
		'reset' => __('Reset', 'emd-plugins') ,
		'export' => __('Export', 'emd-plugins'),
	); 
	if($has_user == 1){
		$tabs['map-user'] = __('Map Users','emd-plugins');
	}
	?>
	<div class="wrap" id="operations-wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2 id="operations-header" class="nav-tab-wrapper">
	<?php foreach ($tabs as $tab => $label) {
		echo '<a class="nav-tab ';
		if ($tab == $mytab) {
			echo 'nav-tab-active';
		}
		echo '" href="' . esc_url($tab_url) . $tab . '">' . $label . '</a>';
	} ?>
	</h2><div id="operations-content">
	<h3 style="color:#899CAD;">
	<?php echo $plural_label . ' >> ' . $tabs[$mytab];
}
/**
 * Reset relationship, taxonomy and attribute data
 * @since WPAS 4.0
 * @param string $post_type
 * @param string $plural_label
 * @param string $myapp
 *
 */
function emd_reset_vals($post_type, $plural_label, $myapp) {
	if (isset($_POST['reset_all'])) {
		emd_reset_rels($post_type, $plural_label, $myapp);
		emd_reset_taxs($post_type, $plural_label);
		emd_reset_attrs($post_type, $plural_label, $myapp);
	} else {
		if (isset($_POST['reset_tax'])) {
			emd_reset_taxs($post_type, $plural_label);
		}
		if (isset($_POST['reset_meta'])) {
			emd_reset_attrs($post_type, $plural_label, $myapp);
		}
		if (isset($_POST['reset_rel'])) {
			emd_reset_rels($post_type, $plural_label, $myapp);
		}
	}
}
/**
 * Reset relationship data
 * @since WPAS 4.0
 * @param string $post_type
 * @param string $plural_label
 * @param string $myapp
 *
 */
function emd_reset_rels($post_type, $plural_label, $myapp) {
	$rel_list = get_option($myapp . '_rel_list');
	$count_rels = 0;
	//delete all relationships
	if (!empty($rel_list)) {
		foreach ($rel_list as $krel => $vrel) {
			if ($vrel['from'] == $post_type || $vrel['to'] == $post_type) {
				$rel_type = str_replace("rel_", "", $krel);
				$count_rels+= p2p_delete_connections($rel_type);
			}
		}
	}
	printf(__('<br>%d %s relationships are deleted.', 'emd-plugins') , $count_rels, $plural_label);
}
/**
 * Reset taxonomy data
 * @since WPAS 4.0
 * @param string $post_type
 * @param string $plural_label
 *
 */
function emd_reset_taxs($post_type, $plural_label) {
	$count_terms = 0;
	//delete all taxonomies
	$taxlist = get_taxonomies(array(
		'object_type' => array(
			$post_type
		)
	));
	if (isset($taxlist) && !empty($taxlist)) {
		foreach ($taxlist as $mytax) {
			$terms = get_terms($mytax);
			$count_terms+= count($terms);
			foreach ($terms as $term) {
				wp_delete_term($term->term_id, $mytax);
			}
		}
	}
	printf(__('<br>%d Taxonomy Terms of %s are deleted.', 'emd-plugins') , $count_terms, $plural_label);
}
/**
 * Reset attribute and entity data
 * @since WPAS 4.0
 * @param string $post_type
 * @param string $plural_label
 * @param string $myapp
 *
 */
function emd_reset_attrs($post_type, $plural_label, $myapp) {
	$count_posts = 0;
	//delete all fields (post_meta)
	$postslist = get_posts(array(
		'post_type' => $post_type,
		'numberposts' => - 1,
		'post_status' => 'any'
	));
	if (!empty($postslist)) {
		$entity_fields = get_option($myapp . '_attr_list');
		$count_posts = count($postslist);
		foreach ($postslist as $mypost) {
			if (!empty($entity_fields[$post_type])) {
				//Delete fields
				foreach (array_keys($entity_fields[$post_type]) as $myfield) {
					if (in_array($entity_fields[$post_type][$myfield]['display_type'], Array(
						'file',
						'image',
						'plupload_image',
						'thickbox_image'
					))) {
						$pmeta = get_post_meta($mypost->ID, $myfield);
						if (!empty($pmeta)) {
							foreach ($pmeta as $file_id) {
								wp_delete_attachment($file_id);
							}
						}
					}
					delete_post_meta($mypost->ID, $myfield);
				}
				//delete post
				wp_delete_post($mypost->ID);
			}
		}
	}
	printf(__('<br>%d %s are deleted.', 'emd-plugins') , $count_posts, $plural_label);
}
/**
 * Process import file step2
 * @since WPAS 4.0
 * @param string $upload_file
 * @param string $myapp
 * @param string $post_type
 * @param string $label
 *
 */
function emd_import_step2($upload_file, $myapp, $post_type, $label) {
	$file = fopen($upload_file, 'r');
	$count = 0;
	while (($line = fgets($file)) !== false) {
		if (strstr($line, ',') === false) {
			echo '<b>' . __('Error: Verify your CSV file, all CSV files must have a correctly formatted header.', 'emd-plugins') . '</b>';
			exit;
		}
		if ($count == 0) {
			$header = explode(',', $line);
		}
		$count++;
		break;
	}
	fclose($file);
	$builtin_fields = Array(
		'post_author' => 'WP ' . __('Author', 'emd-plugins') ,
		'post_status' => 'WP ' . __('Status', 'emd-plugins') ,
		'post_date' => 'WP ' . __('Date', 'emd-plugins') ,
		'post_title' => 'WP ' . __('Title', 'emd-plugins') ,
		'post_excerpt' => 'WP ' . __('Excerpt', 'emd-plugins') ,
		'post_content' => 'WP ' . __('Content', 'emd-plugins')
	);
	$required_fields = Array();
	$field_labels = Array();
	$entity_fields = get_option($myapp . '_attr_list');
	if(!empty($entity_fields[$post_type])){
		foreach ($entity_fields[$post_type] as $key => $myentity_field) {
			$field_labels[$key] = $myentity_field['label'];
			if ($myentity_field['required'] == 1) {
				$required_fields[] = $key;
			}
		}
	}
	$tax_fields = Array();
	$taxlist = get_taxonomies(array(
		'object_type' => array(
			$post_type
		)
	) , 'object');
	if (!empty($taxlist)) {
		foreach ($taxlist as $tax_key => $tax_val) {
			$tax_fields[$tax_key] = $tax_val->labels->name;
		}
	}
	$fields = array_merge($builtin_fields, $field_labels, $tax_fields); ?>
	<div class='wrap'><p><?php _e('* Indicates required field', 'emd-plugins'); ?>
	</p>
	<form method='post'>
	<input type='hidden' name='file' value='<?php echo $upload_file; ?>'>
	<table class='widefat fixed' cellspacing='0'>
	<thead><tr>
	<th><?php echo $label . " " . __('Field', 'emd-plugins'); ?>
	<a href='#' style='cursor: help;' title='<?php printf(__('Select a field from list of all fields existing in the database for %s', 'emd-plugins') , lcfirst($label)); ?>.'> ?</a>
	</th><th>
	<?php _e('Header Field', 'emd-plugins'); ?>
	<a href='#' style='cursor: help;' title='<?php _e('This column shows the header field(s) of the import file.', 'emd-plugins'); ?>'> ?</a>
	</th></tr></thead>
	<?php $col_dropdown = "<option value=''>" . __('Do not map this field', 'emd-plugins') . "</option>";
	$user_dropdown = "<option value=''>" . __('Please select', 'emd-plugins') . "</option>";
	$status_dropdown = "<option value=''>" . __('Please select', 'emd-plugins') . "</option>
		<option value='publish'>" . __('Publish', 'emd-plugins') . "</option>
		<option value='pending'>" . __('Pending', 'emd-plugins') . "</option>
		<option value='draft'>" . __('Draft', 'emd-plugins') . "</option>
		<option value='future'>" . __('Future', 'emd-plugins') . "</option>
		<option value='private'>" . __('Private', 'emd-plugins') . "</option>";
	foreach ($header as $myheader) {
		$myheader = trim($myheader);
		$myheader = trim($myheader, '"');
		$col_dropdown.= "<option value='" . trim($myheader) . "'>" . trim($myheader) . "</option>";
	}
	foreach ($fields as $keyfield => $myfield) {
		if ($keyfield == 'post_author') {
			$users = get_users();
			foreach ($users as $myuser) {
				$user_name = $myuser->data->user_nicename;
				$user_id = $myuser->data->ID;
				$user_dropdown.= "<option value='" . $user_id . "'>" . $user_name . "</option>";
			}
			echo "<tr><td>" . $myfield . "</td><td><select name=\"" . $keyfield . "\" id=\"" . $keyfield . "\">" . $user_dropdown . "</select></td></tr>";
		} elseif ($keyfield == 'post_status') {
			echo "<tr><td>" . $myfield . "</td><td><select name=\"" . $keyfield . "\" id=\"" . $keyfield . "\">" . $status_dropdown . "</select></td></tr>";
		} elseif ($keyfield == 'post_date') {
			echo "<tr><td>" . $myfield . "</td><td><input type=\"text\" id=\"" . $keyfield . "\" name=\"" . $keyfield . "\"/></td></tr>";
		} else {
			if (in_array($keyfield, $required_fields)) {
				$myfield = "* " . $myfield;
			}
			echo "<tr><td>" . $myfield . "</td><td><select name=\"" . $keyfield . "\" id=\"" . $keyfield . "\">";
			echo $col_dropdown . "</select></td></tr>";
		}
	}
	wp_nonce_field('emd_import_nonce', 'emd_import_nonce'); ?>
	</table>
	<fieldset class='submit'>
	<input type='submit' class='button-primary' id='emd_import_step2' name='emd_import_step2' value='<?php _e('Import Now', 'emd-plugins'); ?>' name='submit'>
	</fieldset>
	</form>
	</div>
<?php
}
/**
 * Process import file step3
 * @since WPAS 4.0
 * @param string $upload_file
 * @param string $myapp
 * @param string $post_type
 * @param string $plural_label
 *
 */
function emd_import_step3($upload_file, $myapp, $post_type, $plural_label) {
	$header = "";
	$data = Array();
	$file = fopen($upload_file, 'r');
	$count = 0;
	while (($line = fgets($file)) !== false) {
		if (strstr($line, ',') === false) {
			echo '<b>' . __('<br>Error: Verify your CSV file, all CSV files must have a correctly formatted header.', 'emd-plugins') . '</b>';
			exit;
		}
		if ($count == 0) {
			$header = explode('","', $line);
		} else {
			//$line = str_replace("NULL","\"NULL\"",$line);
			$data[] = explode('","', $line);
		}
		$count++;
	}
	fclose($file);
	if (!empty($data)) {
		$taxlist = get_taxonomies(array(
			'object_type' => array(
				$post_type
			)
		));
		$entity_fields = get_option($myapp . '_attr_list');
		$uploaddir = wp_upload_dir();
		foreach ($data as $mydata) {
			$mypost = Array();
			$myfields = Array();
			$mytax = Array();
			$mypost['post_type'] = $post_type;
			if (isset($_POST['post_author'])) {
				$mypost['post_author'] = $_POST['post_author'];
			}
			if (isset($_POST['post_status'])) {
				$mypost['post_status'] = $_POST['post_status'];
			}
			if (isset($_POST['post_date'])) {
				$mypost['post_date'] = $_POST['post_date'];
			} else {
				$mypost['post_date'] = date("Y-m-d H:i:s");
			}
			foreach ($header as $header_key => $header_val) {
				$header_val = trim($header_val);
				$header_val = trim($header_val, '"');
				$new_header[$header_key] = $header_val;
			}
			foreach ($mydata as $key => $column) {
				$column = trim($column);
				$column = trim($column, '"');
				if ($column != "NULL") {
					$post_key_arr = array_keys($_POST, $new_header[$key]);
					foreach ($post_key_arr as $post_key) {
						if (preg_match("/^post_/i", $post_key)) {
							$mypost[$post_key] = $column;
						} else if (in_array($post_key, $taxlist)) {
							$mytax[$post_key] = $column;
						} elseif (!empty($post_key)) {
							$myfields[$post_key] = $column;
						}
					}
				}
			}
			$mypost['import'] = 1;
			$mypost['post_date_gmt'] = $mypost['post_date'];
			$mypost['post_modified_gmt'] = $mypost['post_date'];
			if ($id = wp_insert_post($mypost)) {
				foreach ($myfields as $meta_key => $meta_value) {
					$meta_value = str_replace('""', '"', $meta_value);
					if (in_array($entity_fields[$post_type][$meta_key]['display_type'], Array(
						'file',
						'image',
						'plupload_image',
						'thickbox_image'
					))) {
						emd_import_file_image($uploaddir, $meta_key, $meta_value, $id);
					} elseif (in_array($entity_fields[$post_type][$meta_key]['display_type'], Array(
						'checkbox_list',
						'select'
					))) {
						$meta_value_arr = explode(";", $meta_value);
						foreach ($meta_value_arr as $mymeta_value) {
							add_post_meta($id, $meta_key, $mymeta_value);
						}
					} else {
						add_post_meta($id, $meta_key, $meta_value);
					}
				}
				foreach ($mytax as $tax_key => $tax_value) {
					$tax_value_arr = explode(";",$tax_value);
					wp_set_object_terms($id, $tax_value_arr, $tax_key);
				}
			}
		}
	}
	$numrows = count($data);
	echo ' >> ' . __('Step 3: Finish', 'emd-plugins'); ?>
	</h3>
	<p><?php printf(__('%d rows have been imported to %s', 'emd-plugins') , $numrows, $plural_label); ?>
	</p>
<?php
}
/**
 * Process import user-meta
 * @since WPAS 4.4
 * @param string $myapp
 * @param string $post_type
 * @param string $label
 * @param string $plural_label
 *
 */
function emd_map_user_meta($myapp, $post_type, $label,$plural_label) {
	$required_fields = Array();
	$field_labels = Array();
	$entity_fields = get_option($myapp . '_attr_list');
	if(!empty($entity_fields[$post_type])){
		foreach ($entity_fields[$post_type] as $key => $myentity_field) {
			if($myentity_field['display_type'] == 'user'){
				$user_field = $key;
			}
			$field_labels[$key] = $myentity_field['label'];
			if ($myentity_field['required'] == 1) {
				$required_fields[] = $key;
			}
		}
	}
	//get users
	$users = Array();
	$roles = Array();

	$ent_list = get_option($myapp . '_ent_list');
	if (!empty($ent_list[$post_type]) && !empty($ent_list[$post_type]['limit_user_roles'])) {
		$roles = $ent_list[$post_type]['limit_user_roles'];
	}
	if(!empty($roles)){
		foreach($roles as $mrole){
			$users = array_merge($users,get_users(array('role' => $mrole)));
		}
	}
	else {
		$users = get_users(array('exclude' => array(1)));
	}


	if(isset($_POST['emd_user_map_clear'])){
		update_option($myapp . "_" . $post_type . "_map",Array());
		echo '<p style="color:#D54E21;">' . __('User Mapping is deleted.', 'emd-plugins') . '</p>';
	}	
	elseif (isset($_POST['emd_user_map'])) {
		//if save map clicked , save these in options to be used later
		foreach($_POST as $pkey => $pval){
			if(!empty($pval) && !in_array($pkey,Array('emd_user_map_nonce','_wp_http_referer','emd_user_map'))){
				$map[$pkey] = $pval;
			}
		}
		$req_error = 0;
		foreach($required_fields as $rfield){
			if(!isset($map[$rfield])){
				$req_error = 1;
				break;
			}
		}
		if($req_error == 1){
			echo '<p style="color:#D54E21;">' . __('All required attributes must be mapped. Please try again.','emd-plugins');
		}
		else {
			update_option($myapp . "_" . $post_type . "_map",$map);
			echo '<p>' . __('User Mapping is saved.', 'emd-plugins') . '</p>';
			//is the create exsiting users checked?
			if(isset($map['create_users']) && $map['create_users'] == 1){
				$count_created = 0;
				$count_updated = 0;
				foreach($users as $cuser){
					$args['post_type'] = $post_type;
					$args['post_status'] = $map['post_status'];
					$args['post_author'] = $map['post_author'];
					if(isset($map['post_date'])){
						$args['post_date'] = $map['post_date'];
					}
					else {
						$args['post_date'] = date('Y-m-d');
					}
					if(isset($map['post_title'])){
						if(in_array($map['post_title'],Array('display_name','user_email','user_url'))){
							$args['post_title'] = $cuser->data->$map['post_title'];
						}
						else {
							$args['post_title'] = get_user_meta($cuser->ID,$map['post_title'],true);
						}
					}
					else {
						$args['post_title'] = $cuser->user_login;
					}
					//check if this user inserted before
					$search_args = Array('meta_key' => $ent_list[$post_type]['user_key'],
                                        'meta_value' => $cuser->ID,
                                        'post_type' => $post_type,
                                        'post_status' => 'any',
                                        'posts_per_page' => -1);
                        		$ent_posts = get_posts($search_args);
					if(empty($ent_posts)){
						$pid = wp_insert_post($args);
						if (!is_wp_error($pid)) {
							emd_add_map_to_user_ent($pid,$map,$cuser);
							$count_created++;
						}
					}
					else{
						$ent_updated = 0;
						$pid = $ent_posts[0]->ID;
						if($ent_posts[0]->post_author != $args['post_author'] || 
						$ent_posts[0]->post_status != $args['post_status'] ) {
							$args['ID'] = $pid;
							wp_update_post($args);
							$ent_updated = 1;
						}
						foreach($map as $kmap => $vmap){
							if(preg_match('/^emd_./',$kmap)){
								$old_value = get_post_meta($pid, $kmap,true);
								if(in_array($vmap,Array('display_name','user_email','user_url'))){
									if($old_value != $cuser->data->$vmap){
										update_post_meta($pid, $kmap, $cuser->data->$vmap);
										$ent_updated = 1;
									}
								}
								elseif($vmap != 'user_id') {			
									$map_val = get_user_meta($cuser->ID,$vmap,true);
									if($old_value != $map_val){
										update_post_meta($pid, $kmap, $map_val);
										$ent_updated = 1;
									}
								}
							}
						}
						if($ent_updated == 1){
							$count_updated++;
						}
					}
				}
				if($count_created > 0){
					$dlabel = $label;
					if($count_created > 1){
						$dlabel = $plural_label;
					}
					echo "<p>";
					printf(__('%1s %2s created from existing users.', 'emd-plugins'),$count_created,$dlabel);
					echo "</p>";
				}
				if($count_updated > 0){
					$dlabel = $label;
					if($count_updated > 1){
						$dlabel = $plural_label;
					}
					echo "<p>";
					printf(__('%1s %2s updated from existing users.', 'emd-plugins'),$count_updated,$dlabel);
					echo "</p>";
				}
				
			}
		}
	}
	?>
	<p>
	<?php printf(__('You can use the form below to map your users to %s attributes. After the mapping is saved new user information will be automatically propagated to %s entity attributes based on the mapping below. You can also map your existing users by checking the checkbox below the form. If you clear the mapping you must assign users to %s manually.', 'emd-plugins'),$label,$label,$plural_label); ?>
	</p>
	<?php
	$map_ptype_user = get_option($myapp . "_" . $post_type . "_map");
	
	$builtin_fields = Array(
		'post_author' =>  __('WP Author', 'emd-plugins') ,
		'post_status' =>  __('WP Status', 'emd-plugins') ,
		'post_date' =>  __(' WP Post Date', 'emd-plugins'),
	);
	if(post_type_supports($post_type,'title')){
		$builtin_fields['post_title'] = 'WP ' . __('Title', 'emd-plugins');
	}
	if(post_type_supports($post_type,'excerpt')){
		$builtin_fields['post_excerpt'] = 'WP ' . __('Excerpt', 'emd-plugins');
	}
	if(post_type_supports($post_type,'content')){
		$builtin_fields['post_content'] = 'WP ' . __('Content', 'emd-plugins');
	}
	$tax_fields = Array();
	$taxlist = get_taxonomies(array(
		'object_type' => array(
			$post_type
		)
	) , 'object');
	if (!empty($taxlist)) {
		foreach ($taxlist as $tax_key => $tax_val) {
			$tax_fields[$tax_key] = $tax_val->labels->name;
		}
	}
	$fields = array_merge($builtin_fields, $field_labels, $tax_fields); 
	echo "<form method='post'>";
	if(!empty($map_ptype_user)){
		//clear button
		echo "<input type='submit' class='button-primary' style='background-color:#D54E21;border-color: #d54e21;' id='emd_user_map_clear' name='emd_user_map_clear' value='" .  __('Clear', 'emd-plugins') . "' name='submit'>";
	}
	?>
	<div class='wrap'><p><?php _e('* Indicates required field', 'emd-plugins'); ?>
	</p>
	<table class='widefat fixed' cellspacing='0'>
	<thead><tr>
	<th><?php echo $label . " " . __('Attribute', 'emd-plugins'); ?>
	<a href='#' style='cursor: help;' title='<?php printf(__('Select a field from list of all fields existing in the database for %s', 'emd-plugins') , lcfirst($label)); ?>.'> ?</a>
	</th><th>
	<?php _e('User Field', 'emd-plugins'); ?>
	<a href='#' style='cursor: help;' title='<?php _e('This column shows the user metadata field(s).', 'emd-plugins'); ?>'> ?</a>
	</th></tr></thead>
	<?php 
	$col_dropdown['user_email'] = 'user_email';
	$col_dropdown['display_name'] = 'display_name';
	$col_dropdown['user_url'] = 'user_url';

	$user_dropdown = "";
	$status_dropdown = "";
	$pstatuses = Array('publish' => __('Publish', 'emd-plugins'),
			   'pending' => __('Pending', 'emd-plugins'),
			   'draft' => __('Draft', 'emd-plugins'),
			   'future' => __('Future', 'emd-plugins'),
			   'private' => __('Private', 'emd-plugins')
			);

	foreach($pstatuses as $kstatus => $pstatus){
		$status_dropdown .= "<option value='" . $kstatus . "'";
		if(!empty($map_ptype_user['post_status']) && $kstatus == $map_ptype_user['post_status']){
			$status_dropdown .= " selected";
		}
		$status_dropdown .= ">" . $pstatus . "</option>";
	}


	foreach($users as $muser){
		$user_info = get_user_meta($muser->ID);
		foreach(array_keys($user_info) as $uinfo){
			if(!preg_match('/tribe_columns/',$uinfo) && !in_array($uinfo,$col_dropdown)){
				$col_dropdown[$uinfo] = $uinfo;
			}
		}
	}
	foreach ($fields as $keyfield => $myfield) {
		if ($keyfield == 'post_author') {
			$users = get_users();
			foreach ($users as $myuser) {
				$user_name = $myuser->data->user_nicename;
				$user_id = $myuser->data->ID;
				$user_dropdown.= "<option value='" . $user_id . "'";
				if(!empty($map_ptype_user['post_author']) && $user_id == $map_ptype_user['post_author']){
					$user_dropdown .= " selected";
				}
				$user_dropdown .= ">" . $user_name . "</option>";
			}
			echo "<tr><td>" . $myfield . "</td><td><select name=\"" . $keyfield . "\" id=\"" . $keyfield . "\">" . $user_dropdown . "</select></td></tr>";
		} elseif ($keyfield == 'post_status') {
			echo "<tr><td>" . $myfield . "</td><td><select name=\"" . $keyfield . "\" id=\"" . $keyfield . "\">" . $status_dropdown . "</select></td></tr>";
		} elseif ($keyfield == 'post_date') {
			echo "<tr><td>" . $myfield . "</td><td><input type=\"text\" id=\"" . $keyfield . "\" name=\"" . $keyfield . "\"";
			if(!empty($map_ptype_user['post_date'])){
				echo " value=\"" . $map_ptype_user['post_date'] . "\"";
			}
			echo "/></td></tr>";
		} elseif ($keyfield == $user_field) {
			if (in_array($keyfield, $required_fields)) {
				$myfield = "* " . $myfield;
			}
			echo "<tr><td>" . $myfield . "</td><td><input type=\"text\" id=\"" . $keyfield . "\" name=\"" . $keyfield . "\" value=\"user_id\"readonly></td></tr>";
		} else{
			if (in_array($keyfield, $required_fields)) {
				$myfield = "* " . $myfield;
			}
			echo "<tr><td>" . $myfield . "</td><td><select name=\"" . $keyfield . "\" id=\"" . $keyfield . "\">";
			echo '<option value="">' .  __('Do not map this field', 'emd-plugins') . '</option>';
			foreach($col_dropdown as $kdown => $cdown){
				echo '<option value="' . $kdown . '"';
				if(!empty($map_ptype_user[$keyfield]) && $map_ptype_user[$keyfield] == $cdown){
					echo " selected";
				}
				echo ">" . $cdown . "</option>";
			}
			echo "</select></td></tr>";
		}
	}
	wp_nonce_field('emd_user_map_nonce', 'emd_user_map_nonce'); ?>
	</table>
	<table>
	<tr>
	<th><label for="create_users">
	<?php 
	if(!empty($ent_list[$post_type]['limit_user_roles'])){
		printf(__('Create %s from existing users with %s roles?','emd-plugins'),$label,implode(",",$roles)); 
	}
	else {
		printf(__('Create %s from all existing users? ','emd-plugins'),$label);
	}
	?>
	</th>
	<td>
	<fieldset><legend class="screen-reader-text"><span>
	<?php 
	if(!empty($ent_list[$post_type]['limit_user_roles'])){
		printf(__('Create %s from existing users with %s roles?','emd-plugins'),$label,implode(",",$roles)); 
	}
	else {
		printf(__('Create %s from all existing users? ','emd-plugins'),$label);
	}
	?>
	</span></legend>
	<input id='create_users' name='create_users' type='checkbox' value=1 <?php if(isset($map_ptype_user['create_users']) && $map_ptype_user['create_users'] == 1){ echo "checked"; } ?>></input>
	</fieldset>
	</td></tr></table>

	<fieldset class='submit'>
	<input type='submit' class='button-primary' id='emd_user_map' name='emd_user_map' value='<?php _e('Save', 'emd-plugins'); ?>' name='submit'>
	</fieldset>
	</form>
	</div>
<?php
}
/**
 * Add post-meta to entity for each user meta in mapping
 * @since WPAS 4.4
 * @param string $pid
 * @param array $map
 * @param object $cuser
 *
 */
function emd_add_map_to_user_ent($pid,$map,$cuser){
	foreach($map as $kmap => $vmap){
		if(preg_match('/^emd_./',$kmap)){
			if($vmap == 'user_id'){
				add_post_meta($pid, $kmap, $cuser->ID);
			}
			else if(in_array($vmap,Array('display_name','user_email','user_url'))){
				add_post_meta($pid, $kmap, $cuser->data->$vmap);
			}
			else {
				$map_val = get_user_meta($cuser->ID,$vmap,true);
				add_post_meta($pid, $kmap, $map_val);
			}
		}
	}
}
