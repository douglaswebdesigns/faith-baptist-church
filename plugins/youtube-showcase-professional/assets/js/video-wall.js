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
});
