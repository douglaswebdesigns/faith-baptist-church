<?php
/**
 * Youtube v3 API Functions
 *
 * @package     EMD
 * @copyright   Copyright (c) 2014,  Emarket Design
 * @since       WPAS 4.4
 */
if (!defined('ABSPATH')) exit;
/**
 * Sends request to google api v3 with api key to get stats and duration
 *
 * @since WPAS 4.4
 * @param string $app_name
 * @param string $ent_name
 *
 * @return array $youtube_stats
 */
function emd_get_youtube_stats($app_name,$ent_name){
	//get api key from options
	$api_keys = get_option($app_name . '_youtube_api_key');	
	$youtube_stats= Array('video_duration' => "",
			'video_published_at' => "",
			'video_view_count' => 0, 
			'video_like_count' => 0, 
			'video_dislike_count'=>0 , 
			'video_favorite_count' => 0,
			'video_comment_count' => 0,
			'channel_published_at' => "",
			'channel_view_count' => 0,
			'channel_comment_count' => 0,
			'channel_subscriber_count' => 0,
			'channel_video_count' => 0
			);
	if(!empty($api_keys[$ent_name])){
		$api_params = get_option($app_name . '_youtube_api_attr');
		if(!empty($api_params[$ent_name])){
			$video_ID = emd_mb_meta($api_params[$ent_name]['video_attr']);
			if(!empty($api_params[$ent_name]['username_attr'])){
				$channel_name = emd_mb_meta($api_params[$ent_name]['username_attr']);
			}
			if(!empty($video_ID)){
				$JSON = file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=snippet,statistics,contentDetails&id=" . $video_ID . "&key=" . $api_keys[$ent_name] );
			}
			elseif(!empty($channel_name)){
				$JSON = file_get_contents("https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics&forUsername=" . $channel_name . "&key=" . $api_keys[$ent_name]);
			}
			if(!empty($JSON)){
				$JSON_Data = json_decode($JSON);
				if(!empty($video_ID) && !empty($JSON_Data->items[0]->snippet)){
					$pub_timestamp = strtotime($JSON_Data->items[0]->snippet->publishedAt);
					$youtube_stats['video_published_at'] = human_time_diff($pub_timestamp, current_time('timestamp')) . " " . __('ago','emd-plugins');
				}	
				if(!empty($video_ID) && !empty($JSON_Data->items[0]->contentDetails)){
					$interval = new DateInterval($JSON_Data->items[0]->contentDetails->duration); 
					if($interval->h){
						$youtube_stats['video_duration'] = $interval->h . ":";
					}
					$youtube_stats['video_duration'] .= $interval->i . ":" . $interval->s;
				}	
				if(!empty($video_ID) && !empty($JSON_Data->items[0]->statistics)){
					$youtube_stats['video_view_count'] = $JSON_Data->items[0]->statistics->viewCount;
					$youtube_stats['video_like_count'] = $JSON_Data->items[0]->statistics->likeCount;
					$youtube_stats['video_dislike_count'] = $JSON_Data->items[0]->statistics->dislikeCount;
					$youtube_stats['video_favorite_count'] = $JSON_Data->items[0]->statistics->favoriteCount;
					$youtube_stats['video_comment_count'] = $JSON_Data->items[0]->statistics->commentCount;
				}
				if(!empty($channel_name) && !empty($JSON_Data->items[0]->snippet)){
					$ch_pub_timestamp = strtotime($JSON_Data->items[0]->snippet->publishedAt);
					$youtube_stats['channel_published_at'] = human_time_diff($ch_pub_timestamp, current_time('timestamp')) . " " . __('ago','emd-plugins');
				}	
				if(!empty($channel_name) && !empty($JSON_Data->items[0]->statistics)){
					$youtube_stats['channel_view_count'] = $JSON_Data->items[0]->statistics->viewCount;
					$youtube_stats['channel_comment_count'] = $JSON_Data->items[0]->statistics->commentCount;
					$youtube_stats['channel_subscriber_count'] = $JSON_Data->items[0]->statistics->subscriberCount;
					$youtube_stats['channel_video_count'] = $JSON_Data->items[0]->statistics->videoCount;
				}
			}
		}
	}
	return $youtube_stats;
}
/**
 * Display youtube page to save api key
 *
 * @since WPAS 4.4
 * @param string $app
 *
 */
function emd_display_youtube($app){
        global $title;
        $youtube_api = get_option($app . '_youtube_api_attr');
	$key_count = count($youtube_api);
        $settings = get_option($app . '_youtube_api_key');
	$ent_list = get_option($app . '_ent_list');
        ?>
        <div class="wrap">
        <h2><?php echo $title; ?></h2>
  	<?php if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == true) { ?>
                <div class="updated settings-error"><p><strong><?php _e( 'Settings saved.', 'emd-plugins' ); ?></strong></p></div>
        <?php } ?>
	

        <p><?php  printf(__('Google YouTube API Key allows you to request video stats from Google. The default key included below must be changed with your own key to minimize over-the-limit errors. Please refer to the <a href="%s" target="_blank">video</a> on how to get your own API Key.', 'emd-plugins'),'https://www.youtube.com/watch?feature=player_embedded&v=Im69kzhpR3I');?> </p>
        <form method="post" action="options.php">
        <table class="form-table">
        <tbody>
        <?php
        settings_fields($app . '_youtube_api_key');
        foreach($youtube_api as $key => $val){
		$label = $ent_list[$key]['label'];
		if($key_count == 1){
			$label = "";
		}
        ?>
                <tr>
                <th scope="row">
                <?php printf(__("%s Youtube Data API v3 Key",'emd-plugins'),$label); ?>
                </th>
                <td>
                <input id="<?php esc_attr_e($app) ?>_youtube_api_key_<?php echo $key; ?>" name="<?php esc_attr_e($app) ?>_youtube_api_key[<?php echo $key ?>]" type="text" class="regular-text" value="<?php esc_attr_e($settings[$key]); ?>"></input>
		</td>
		</tr>
        <?php
        }
        ?>
                </tbody>
                </table>
                <?php submit_button(); ?>
                </form>
                </div>
                <?php
}
function emd_youtube_register_settings($app){
	register_setting($app . "_youtube_api_key", $app . "_youtube_api_key");
}
