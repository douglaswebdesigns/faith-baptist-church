var page =1;
jQuery(document).ready(function($) {
var anchor = window.location.hash.replace("#", "");
if(anchor.match(/comment/g))
{       
    $("#emd-comments").collapse('show');
}   
$('#comment-submit').addClass('btn btn-primary btn-lg btn-block');
$('.pagination-bar a').click(function(){
   if($(this).hasClass('prev')){
     page --;
   }  
   else if($(this).hasClass('next')){
     page ++;
   }  
   else{  
     page = $(this).text();
   }  
   var div_id = '#emd-comment-list';
   load_comments(div_id);
   return false;
}); 
var load_comments = function(div_id){
   var post_id = $('#comment_post_ID').val();
   var com_type = $('#emd_comment_type').val();
   var theme = $('#emd_comment_theme').val();
   $.ajax({
    type: 'POST',
    url: comment_vars.ajax_url,
    cache: false,
    async: false,
    data: {action:'get_comment_type_page',pageno: page,post_id:post_id,com_type:com_type,theme:theme},
      success: function(response)
      {
        $(div_id).html(response);
        $('.pagination-bar a').click(function(){
        if($(this).hasClass('prev')){
          page --;
        }
        else if($(this).hasClass('next')){
          page ++;
        }
        else{
          page = $(this).text();
        }
   	var div_id = '#emd-comment-list';
   	load_comments(div_id);
        return false;
      });
   },
 });
}
});
