<?php
/**
 * WPAS Media Button Std View Filter Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.0
 */
if (!defined('ABSPATH')) exit;
if (!function_exists('emd_get_attr_tax_rel_list')) {
	add_action('wp_ajax_emd_get_attr_tax_rel_list', 'emd_get_attr_tax_rel_list');
}
add_action('emd_std_media_js', 'emd_std_media_js');
add_action('emd_std_call_js', 'emd_std_call_js');
add_action('emd_add_comp_std_js', 'emd_add_comp_std_js');
add_action('emd_std_div', 'emd_std_div');
add_action('emd_std_hide_div', 'emd_std_hide_div');
/**
 * Return filter div
 *
 * @since WPAS 4.0
 *
 */
function emd_std_div() { ?>
<div id='wpas-shc-gen' style='display:none;padding-bottom:10px;'>
<p><?php _e('Limit results by creating a content filter and clicking plus icon:', 'emd-plugins'); ?></p>
<div id='wpas-shc-list'>
</div>
<div id='wpas-shc-filter' style='display:none;'><h4><?php _e('If all of the following conditions are met:', 'emd-plugins'); ?></h4><table></table></div>
</div>
<?php
}
/**
 * Return js for filters
 *
 * @since WPAS 4.0
 *
 */
function emd_add_comp_std_js() { ?>
         if(ent != undefined && ent != '' )
                {
                shc += ' app=\"' + $('#wpas-components option:selected').attr('app') + '\"';
                shc += ' entity=\"' + ent + '\"';
                }
                $('#wpas-shc-filter #shc-hidden').each(function (){
                        if($(this).val() != '')
                        {
                        shc_hiddens  += $(this).val() + ';';
                        }
                        });
                if(shc_hiddens != '')
                {
                        shc_filters += 'filter=\"' + shc_hiddens + '\"';
                        shc += ' ' + shc_filters;
                }
<?php
}
/**
 * Call getfilter func
 *
 * @since WPAS 4.0
 *
 */
function emd_std_call_js() { ?>
          else {
        $.fn.getFilter(ent_val,$('#wpas-components option:selected').attr('app'));
        $('input[type="submit"]').show();
}
<?php
}
/**
 * Return hide js for filters
 *
 * @since WPAS 4.0
 *
 */
function emd_std_hide_div() { ?>
                $('#wpas-shc-gen').hide();
                $('#wpas-shc-filter tr').each(function (){
                        $(this).remove();
                        }); 
<?php
}
/**
 * Define get filter func which gets all attr, tax and rels
 *
 * @since WPAS 4.0
 *
 */
function emd_std_media_js() { ?>
$.fn.getFilter = function(ent_name,app_name){
	$.ajax({
type:'GET',
url : ajaxurl,
data: {action:'emd_get_attr_tax_rel_list',ent_name:ent_name,app_name:app_name},
success : function(response){
if(response)
{
var filt_val = '';
var op_val = 'is';
var op_html = '=';
$('#wpas-shc-list').html(response);
$('#wpas-shc-gen').show();
$('#wpas-shc-filter tr').each(function (){
	$(this).remove();
	});
$('#wpas-shc-filter').hide();
$('#add-attr').click(function(e){
	var shc_val = '';
	if($('.wpas-attr option:selected').val() != '')
	{
	if($('#attr-value').val() != '')
	{
	filt_val = $('#attr-value').val();
	}
	else if($('#attr-date-value').val() != '')
	{
	filt_val = $('#attr-date-value').val();
	}
	if($('.wpas-attr option:selected').attr('type') == 'tax')
	{
	op_val =  'is';
        op_html =  'slug = ';
	}
	else if($('.wpas-attr option:selected').attr('type') == 'rel')
	{
	op_val =  $('#rel-op-user option:selected').val();
	op_html = $('#rel-op-user option:selected').html();
	if(op_val == 'current_user')
	{
		filt_val = op_val;      
		op_val = 'is';
		op_html = '=';
	}
	}
	else
	{
		op_val =  $('#attr-op option:selected').val();
		op_html =  $('#attr-op option:selected').html();
	}
	if(filt_val != '')
	{
		shc_val = $('.wpas-attr option:selected').attr('type') + '::' + $('.wpas-attr option:selected').val();
		shc_val += '::' + op_val;
		shc_val += '::' + filt_val;
		var query_div = '<tr><td><a class="delete-attr"><img src="<?php echo plugin_dir_url(__FILE__) . '../../assets/img/minus.png'; ?>"></a></td><td>' + $('.wpas-attr option:selected').html() + '</td><td style=\"text-align:center;\">' + op_html + '</td><td>' + filt_val + '<input type=\"hidden\" id=\"shc-hidden\" value=\"' + shc_val + '\"></td></tr>';
		$('#wpas-shc-filter table').append(query_div);
		$('#wpas-shc-filter').show();
		$('.delete-attr').click(function(e){
				$(this).closest('tr').remove();
				});
	}
	}
});
$('.wpas-attr').change(function(e){
		var limit_type = $('.wpas-attr option:selected').attr('type');
		switch (limit_type) {
		case 'attr':
		$('#attr-op').show();
		$('#attr-date-value').hide();
		$('#attr-value').show();
		$('#rel-op-span').hide();
		$('#rel-op-user').hide();
		$('#tax-op').hide();
		if($('.wpas-attr option:selected').attr('date') != undefined)
		{
		$('#date-relative').show();
		$('#date-rel-sel').prop('checked',false);
		$('#attr-op option').each(function(i) {
			if($(this).val() == 'like' || $(this).val() == 'not_like')
			{
			$(this).attr('disabled','disabled');
			}
			else
			{
			$(this).removeAttr('disabled');
			}
			});     
		$('#attr-op').val('is');
		}
		else
		{
			$('#date-relative').hide();
			$('#attr-op option').each(function(i) {
					$(this).removeAttr('disabled');
					});
		}
		break;
		case 'rel':
		if($('.wpas-attr option:selected').attr('has_wpuser') == 1)
		{
			$('#rel-op-user option[value=\"current_user\"]').removeAttr('disabled');
		}
		else
		{
			$('#rel-op-user option[value=\"current_user\"]').attr('disabled','disabled');
		}
		$('#rel-op-user').show();
		$('#rel-op-user').val('is');
		$('#rel-op-span').html('by ' + $('.wpas-attr option:selected').attr('other'));
		$('#rel-op-span').show();
		$('#tax-op').hide();
		$('#attr-op').hide();
		$('#attr-op').val('is');
		$('#date-relative').hide();
		$('#attr-date-value').hide();
		$('#attr-value').show();
		break;
		case 'tax':
		$('#attr-op').hide();
		$('#rel-op-span').hide();
		$('#rel-op-user').hide();
		$('#tax-op').show();
		$('#date-relative').hide();
		$('#attr-date-value').hide();
		$('#attr-value').show();
		break;
		}             
		$('#attr-value').val('');
		if($('.wpas-attr option:selected').attr('dformat') != undefined)
		{
			$('#attr-value').datepicker('destroy');
			var date = $('.wpas-attr option:selected').attr('date');
			if(date == 'date')
			{
				$('#attr-value').datepicker({'dateFormat':$('.wpas-attr option:selected').attr('dformat')});
			}
			else
			{
				$('#attr-value').datetimepicker({'dateFormat':$('.wpas-attr option:selected').attr('dformat'),'timeFormat':$('.wpas-attr option:selected').attr('tformat'),'showSecond':$('.wpas-attr option:selected').attr('showSecond')});
			}
		}
		else
		{
			$('#attr-value').datepicker('destroy');
		}
});
$('#date-rel-sel').click(function(e){
		if($(this).attr('checked'))
		{
		$('#attr-date-value').show();
		$('#attr-value').hide();
		$('#attr-op option').each(function(i) {
			if($(this).val() != 'is')
			{
			$(this).attr('disabled','disabled');
			}
			});     
		$('#attr-value').val('');
		$('#attr-op').val('is');
		}
		else
		{
		$('#attr-date-value').hide();
		$('#attr-value').show();
		$('#attr-op option').each(function(i) {
			if($(this).val() != 'is')
			{
			$(this).removeAttr('disabled');
			}
			});   
		}
});
$('#rel-op-user').click(function(e){
		if($(this).val() == 'current_user')
		{
		$('#attr-value').hide();
		$('#attr-value').val('');
		}
		else
		{
		$('#attr-value').show();
		}       
		});
}
else
{
	$('#wpas-shc-gen').hide();
	$('#wpas-shc-filter tr').each(function (){
			$(this).remove();
			});
}
},
	});
}

<?php
}
/**
 * Ajax func to get all attr, tax and rels
 *
 * @since WPAS 4.0
 *
 */
if (!function_exists('emd_get_attr_tax_rel_list')) {
	function emd_get_attr_tax_rel_list() {
		$ent_name = isset($_GET['ent_name']) ? $_GET['ent_name'] : '';
		$app_name = isset($_GET['app_name']) ? $_GET['app_name'] : '';
		$attr_list = get_option($app_name . '_attr_list');
		$tax_list = get_option($app_name . '_tax_list');
		$rel_list = get_option($app_name . '_rel_list');
		$ent_list = get_option($app_name . '_ent_list');
		if (isset($attr_list[$ent_name]) || isset($tax_list[$ent_name])) {
			echo "<table><tr>";
			echo "<td><select id='attr-" . $app_name . "__" . $ent_name . "' class='wpas-attr'>";
			if (!empty($attr_list[$ent_name])) {
				echo "<option value='' style='font-style:italic;font-weight:bold;'>" . __('Attributes', 'emd-plugins') . "</option>";
				foreach ($attr_list[$ent_name] as $key_attr => $val_attr) {
					echo "<option value='" . $key_attr . "' type='attr' ";
					if (isset($val_attr['type']) && in_array($val_attr['type'], Array(
						'date',
						'datetime',
						'time'
					))) {
						echo "date='" . $val_attr['type'] . "' ";
					}
					if (isset($val_attr['dformat']) && is_array($val_attr['dformat'])) {
						if (isset($val_attr['dformat']['dateFormat'])) {
							echo "dformat='" . $val_attr['dformat']['dateFormat'] . "'";
						}
						if (isset($val_attr['dformat']['timeFormat'])) {
							echo " tformat='" . $val_attr['dformat']['timeFormat'] . "'";
						}
						if (isset($val_attr['dformat']['showSecond'])) {
							echo " showSecond='" . $val_attr['dformat']['showSecond'] . "'";
						}
					}
					echo " style='padding-left:1em;'>" . $val_attr['label'] . "</option>";
				}
			}
			if (!empty($tax_list[$ent_name])) {
				echo "<option value='' style='font-style:italic;font-weight:bold'>" . __('Taxonomies', 'emd-plugins') . "</option>";
				foreach ($tax_list[$ent_name] as $key_tax => $val_tax) {
					echo "<option value='" . $key_tax . "' type='tax' style='padding-left:1em;'>" . $val_tax['label'] . "</option>";
				}
			}
			if (!empty($rel_list)) {
				echo "<option value='' style='font-style:italic;font-weight:bold'>" . __('Relationships', 'emd-plugins') . "</option>";
				foreach ($rel_list as $key_rel => $val_rel) {
					$other = "";
					$has_wpuser = 1;
					if ($val_rel['from'] == $ent_name) {
						$other = $val_rel['to_title'];
					} elseif ($val_rel['to'] == $ent_name) {
						$other = $val_rel['from_title'];
					}
					if (!empty($other)) {
						$key_rel = preg_replace("/rel_/", "", $key_rel, 1);
						$rlabel = str_replace("_", " ", $key_rel);
						$rlabel = ucwords($rlabel);
						if (isset($ent_list[$ent_name]['user_key'])) {
							$has_wpuser = 1;
						}
						echo "<option value='" . $key_rel . "' type='rel' other='" . $other . "' has_wpuser=" . $has_wpuser . " style='padding-left:1em;'>" . $rlabel . "</option>";
					}
				}
			}
			echo "</select></td>";
			echo "<td><select id='attr-op'><option value='is'>=</option>
			<option value='is_not'>&#8800;</option><option value='like'>&#8776;</option>
			<option value='not_like'>!&#8776;</option><option value='less_than'><</option>
			<option value='greater_than'>></option><option value='less_than_eq'><=</option>
			<option value='greater_than_eq'>>=</option>
			</select>
			<span id='tax-op' style='display:none;'>slug =</span>
			<span id='rel-op-span' style='display:none;'></span>
			<select id='rel-op-user' style='display:none;'><option value='is'>post id =</option><option value='current_user'>current_user</option></select>
			</td>";
			echo "<td><div id='date-relative' style='display:none;'><div>Relative</div><div><input id='date-rel-sel' type='checkbox' value=1></input></div></div></td>";
			echo "<td><input id='attr-value' type='text'>
			<select id='attr-date-value' style='display:none;'>
			<option value='current_date'>Current Date</option>
			<option value='yesterday'>Yesterday</option>
			<option value='tomorrow'>Tomorrow</option>
			<option value='current_week'>Current Week</option>
			<option value='last_week'>Last Week</option>
			<option value='next_week'>Next Week</option>
			<option value='current_month'>Current Month</option>
			<option value='last_month'>Last Month</option>
			<option value='next_month'>Next Month</option>
			<option value='current_year'>Current Year</option>
			<option value='last_year'>Last Year</option>
			<option value='next_year'>Next Year</option>
			</select>
			</td>";
			echo "<td><a id='add-attr'><img src='" . plugin_dir_url(__FILE__) . "../../assets/img/plus.png'></a></td>";
			echo "</table>";
		}
		die();
	}
}
