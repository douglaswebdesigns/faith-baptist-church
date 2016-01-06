jQuery.noConflict();
jQuery(document).ready(function($){
	jQuery("#post_date").datetimepicker({ dateFormat: "yy-mm-dd",timeFormat: "hh:mm:ss"});
        jQuery('#reset_all').click(function() {
                if(jQuery(this).attr('checked'))
                {
                        jQuery('#reset_tax').attr('checked',true);
                        jQuery('#reset_meta').attr('checked',true);
                        jQuery('#reset_rel').attr('checked',true);
                }
                else
                {
                        jQuery('#reset_tax').attr('checked',false);
                        jQuery('#reset_meta').attr('checked',false);
                        jQuery('#reset_rel').attr('checked',false);
                }
        });
        jQuery(document).on('click','#emd_operations_reset',function(event){
                jQuery('#reset_dialog').dialog("open");
                return false;
        });
        var dlg = jQuery("<div id='reset_dialog' title='"+oper_vars.dialog_title+"'/>")
                .html('<p><span style="float: left; margin: 0 7px 20px 0;"></span>'+oper_vars.dialog+'</p>').appendTo("#reset_form");
        var resetForm = jQuery('#reset_form');
        submit = false;

	dlg.dialog({
                'dialogClass' : 'wp-dialog',
                'modal': true,
                'autoOpen' : false,
                'closeOnEscape' : true,
                'buttons': [
                        {
                        'text' : oper_vars.btnTxt,
                        'click' : function() {
                                jQuery(this).dialog('close');
                                submit = true;
                                resetForm.submit();

                        }
                        },{
                        'text' : oper_vars.cancel,
                        'click' : function() {
                                jQuery(this).dialog('close');
                        }
                        }
                        ]
        });
        resetForm.submit(function() {
                if(submit) {
                        return true;
                }
        });


});
