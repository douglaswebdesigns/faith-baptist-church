"use strict";
jQuery(document).ready(function($){
        $(".emd-coverjs").each(function() {
                if($(this).data('thumb')){
                        $(this).css('background-image', 'url('+$(this).data('thumb')+')');
                        $(this).css('background-size', 'cover');
                }
                else {
                        switch($(this).data('type')){
                                case 'single':
                                $(this).css('background-image', 'url(https://i.ytimg.com/vi/' + $(this).data('cust') + '/mqdefault.jpg)');
                                $(this).css('background-size', 'cover');
                                break;
                        default:
                                $(this).addClass('emd-noimage');
                                break;
                        }
                }
        });
       $(".youtube").each(function() {
                if($(this).data('thumb')){
                        $(this).css('background-image', 'url('+$(this).data('thumb')+')');
                        $(this).css('background-size', 'cover');
                }
                else {
                        switch($(this).data('type')){
                                case 'single':
                                $(this).css('background-image', 'url(https://i.ytimg.com/vi/' + $(this).data('cust') + '/maxresdefault.jpg)');
                                $(this).css('background-size', 'cover');
                                break;
                                default:
                                $(this).css('background-color', 'peru');
                                break;
                        }
                }
                $(this).append($('<div />', {'class': 'play'}));
                $(document).delegate('#'+this.id, 'click', function() {
                        $('.embed-parent').addClass('emd-embed-responsive');
                        var you_param = "";
                        switch($(this).data('type')){
                                case 'single':
                                you_param = $(this).data('cust') + "?";
                                break;
                                case 'playlist':
                                you_param = "?listType=playlist;list="+$(this).data('cust');
                                break;
                                case 'search':
                                you_param = "?listType=search;list="+$(this).data('cust');
                                break;
                                case 'user_uploads':
                                you_param = "?listType=user_uploads;list="+$(this).data('cust');
                                break;
                                case 'custom':
                                cust_ids = $(this).data('cust').split(",");
                                first_id = cust_ids.shift();
                                you_param = first_id + "?playlist="+ cust_ids.join(",");
                                break;
                        }
                        var iframe_url = "https://www.youtube.com/embed/" + you_param;
                        if ($(this).data('params')) iframe_url+='&'+$(this).data('params');
                        var iframe = $('<iframe />', {'frameborder': '0', 'src': iframe_url })
                        $(this).replaceWith(iframe);
                });
        });
});