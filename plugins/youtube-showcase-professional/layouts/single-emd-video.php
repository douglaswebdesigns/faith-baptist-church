<?php $ent_attrs = get_option('yt_scase_pro_attr_list');
if (function_exists('emd_get_youtube_stats')) {
	$youtube_stats = emd_get_youtube_stats('yt_scase_pro', 'emd_video');
} ?>
<div style="position:relative" class="emd-container">
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
} ?> <div class="panel panel-default single" data-video-type="<?php echo esc_html(emd_mb_meta('emd_video_list_type')); ?>
">
    <div class="panel-heading">
        <div class="youtube-stats text-right" data-video-type="<?php echo esc_html(emd_mb_meta('emd_video_list_type')); ?>
">
            <div class="label label-info <?php echo ((emd_mb_meta('emd_video_like_count') == 0) ? 'hidden' : ''); ?>" id="video_like_count"> <span><?php _e('Likes:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['video_like_count']; ?> </div>
            <div class="label label-success <?php echo ((emd_mb_meta('emd_video_favorite_count') == 0) ? 'hidden' : ''); ?>" id="video_favorite_count"> <span><?php _e('Favorites:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['video_favorite_count']; ?> </div>
            <div class="label label-default <?php echo ((emd_mb_meta('emd_video_comment_count') == 0) ? 'hidden' : ''); ?>" id="video_comment_count"> <span><?php _e('Comments:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['video_comment_count']; ?> </div>
            <div class="label label-danger <?php echo ((emd_mb_meta('emd_video_view_count') == 0) ? 'hidden' : ''); ?>" id="video_view_count"> <span><?php _e('Views:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['video_view_count']; ?> </div>
            <div class="label label-info <?php echo ((emd_mb_meta('emd_video_channel_view_count') == 0) ? 'hidden' : ''); ?>" id="channel_view_count"> <span><?php _e('Views:', 'yt-scase-pro'); ?></span><?php echo $youtube_stats['channel_view_count']; ?> </div>
            <div class="label label-default <?php echo ((emd_mb_meta('emd_video_channel_comment_count') == 0) ? 'hidden' : ''); ?>" id="channel_comment_count"> <span><?php _e('Comments:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['channel_comment_count']; ?> </div>
            <div class="label label-success <?php echo ((emd_mb_meta('emd_video_channel_subscriber_count') == 0) ? 'hidden' : ''); ?>" id="channel_subscriber_count"> <span><?php _e('Subscribers:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['channel_subscriber_count']; ?> </div>
            <div class="label label-warning <?php echo ((emd_mb_meta('emd_video_channel_video_count') == 0) ? 'hidden' : ''); ?>" id="channel_video_count"> <span><?php _e('Videos:', 'yt-scase-pro'); ?></span> <?php echo $youtube_stats['channel_video_count']; ?> </div>
            <div class="label <?php echo ((emd_mb_meta('emd_video_duration') == 0) ? 'hidden' : ''); ?>" style="background-color:#4169E1"> <?php echo $youtube_stats['video_duration']; ?> </div>
            <div class="label <?php echo ((emd_mb_meta('emd_video_published') == 0) ? 'hidden' : ''); ?>" style="background-color:#778899"> <?php echo $youtube_stats['video_published_at']; ?> </div>
            <div class=" label <?php echo ((emd_mb_meta('emd_video_channel_published') == 0) ? 'hidden' : ''); ?>" style="background-color:#778899"> <?php echo $youtube_stats['channel_published_at']; ?> </div>
        </div>
    </div>
    <div class="panel-body embed-parent">
        <div class="youtube emd-thumb" data-cust="<?php echo $cust; ?>" data-params="html5=1&autohide=<?php echo esc_html(emd_mb_meta('emd_video_autohide')); ?>
&autoplay=<?php echo esc_html(emd_mb_meta('emd_video_autoplay')); ?>
&cc_load_policy=<?php echo esc_html(emd_mb_meta('emd_video_cc_load')); ?>
&color=<?php echo esc_html(emd_mb_meta('emd_video_color')); ?>
&controls=<?php echo esc_html(emd_mb_meta('emd_video_player_controls')); ?>
&disablekb=<?php echo esc_html(emd_mb_meta('emd_video_disablekb')); ?>
&end=<?php echo esc_html(emd_mb_meta('emd_video_end')); ?>
&fs=<?php echo esc_html(emd_mb_meta('emd_video_fullscreen')); ?>
&hl=<?php echo esc_html(emd_mb_meta('emd_video_language')); ?>
&iv_load_policy=<?php echo esc_html(emd_mb_meta('emd_video_iv_load_policy')); ?>
&loop=<?php echo esc_html(emd_mb_meta('emd_video_loop')); ?>
&modestbranding=<?php echo esc_html(emd_mb_meta('emd_video_modestbranding')); ?>
&rel=<?php echo esc_html(emd_mb_meta('emd_video_rel')); ?>
&showinfo=<?php echo esc_html(emd_mb_meta('emd_video_showinfo')); ?>
&start=<?php echo esc_html(emd_mb_meta('emd_video_start')); ?>
&theme=<?php echo esc_html(emd_mb_meta('emd_video_theme')); ?>
" data-thumb="<?php if (get_post_meta($post->ID, 'emd_video_thumbnail_image')) {
	echo wp_get_attachment_url(get_post_meta($post->ID, 'emd_video_thumbnail_image') [0]);
} ?>" data-type="<?php echo esc_html(emd_mb_meta('emd_video_list_type')); ?>
" id="single-video"></div>
    </div>
    <div class="video-summary"><?php echo $post->post_content; ?></div>
    <div class="panel-footer"> <span class="label label-danger">Category</span> <?php echo get_the_term_list(get_the_ID() , 'video_category', '', ' ', ''); ?> <span class="label label-info">Tags</span> <?php echo get_the_term_list(get_the_ID() , 'video_tag', '', ' ', ''); ?> </div>
</div>
<section id="related-videos">
    <h3>
        <?php _e('Related Videos', 'yt-scase-pro'); ?>
    </h3>
    
<?php $post = get_post();
$res = emd_get_p2p_connections('connected', 'related_videos', 'std', $post, 1, 0);
if (!empty($res['rels'])) {
?>
<div class="row"><?php
	echo $res['before_list'];
	$real_post = $post;
	foreach ($res['rels'] as $myrel) {
		$post = $myrel;
		echo $res['before_item']; ?>
<div class="col-md-4">
  <div class="panel panel-info">
    <div class="panel-body"><a href="<?php echo get_permalink($post->ID); ?>" title="<?php echo get_the_title(); ?>">
      <div class="emd-thumb emd-coverjs" data-thumb="<?php if (get_post_meta($post->ID, 'emd_video_thumbnail_image')) {
			echo wp_get_attachment_url(get_post_meta($post->ID, 'emd_video_thumbnail_image') [0]);
		} ?>" data-type="<?php echo esc_html(emd_mb_meta('emd_video_list_type', '', $myrel->ID)); ?>" data-cust="<?php echo esc_html(emd_mb_meta('emd_video_key', '', $myrel->ID)); ?>"></div></a>
    </div>
    <div class="panel-footer">
      <a title="<?php echo get_the_title(); ?>" href="#"><?php echo get_the_title(); ?></a>
    </div></div></div><?php
		echo $res['after_item'];
	}
	$post = $real_post;
	echo $res['after_list']; ?>
</div><?php
} ?>
 
</section>
</div><!--container-end-->