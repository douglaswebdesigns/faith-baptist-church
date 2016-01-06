<?php global $video_indicators_count;
$ent_attrs = get_option('yt_scase_pro_attr_list');
?>
<?php switch (emd_mb_meta('emd_video_list_type')) {
	case 'single':
		$cust = emd_mb_meta('emd_video_key');
	break;
	case 'playlist':
		$cust = emd_mb_meta('emd_video_list_playlist');
	break;
	case 'search':
		$cust = emd_mb_meta('emd_video_list_search');
	break;
	case 'user_uploads':
		$cust = emd_mb_meta('emd_video_user_uploads');
	break;
	case 'custom':
		$cust = emd_mb_meta('emd_video_playlist');
	break;
} ?> <?php echo (($video_indicators_count > 1 and ($video_indicators_count % 2 == 0 or $video_indicators_count % 3 == 0)) ? '
    <div class="clearfix ' : ''); ?> <?php echo (($video_indicators_count % 2 == 0 and $video_indicators_count != 0) ? 'visible-sm-block' : ''); ?> <?php echo (($video_indicators_count % 3 == 0 and $video_indicators_count != 0) ? 'visible-md-block' : ''); ?> <?php echo (($video_indicators_count % 4 == 0 and $video_indicators_count != 0) ? 'visible-lg-block' : ''); ?> <?php echo (($video_indicators_count > 1 and ($video_indicators_count % 2 == 0 or $video_indicators_count % 3 == 0)) ? '"></div>
    ' : ''); ?> 
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 <?php echo (($video_indicators_count == 0) ? 'active' : ''); ?>" data-slide-to="<?php echo $video_indicators_count; ?>" data-target="#emdvideos">
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="emd-thumb emd-coverjs" data-cust="<?php echo $cust; ?>" data-thumb="<?php if (get_post_meta($post->ID, 'emd_video_thumbnail_image')) {
	echo wp_get_attachment_url(get_post_meta($post->ID, 'emd_video_thumbnail_image') [0]);
} ?>" data-type="<?php echo esc_html(emd_mb_meta('emd_video_list_type')); ?>
"></div>
            </div>
            <div class="panel-footer"> <a title="<?php echo get_the_title(); ?>" href="#"><?php echo get_the_title(); ?></a> </div>
        </div>
    </div>