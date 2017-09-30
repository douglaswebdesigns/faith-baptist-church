<?php
/**
 * Template Name: Streaming Video Page
 *
 * Displays the Streaming Video with Full Content Display.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<?php get_header(); ?>

<style type="text/css">.pf-content{margin-top:30px;border-top: 4px solid #095A4C;}</style>

	<?php

	//* Add custom body class to the head
	add_filter( 'body_class', 'streaming_video_body_class' );
	function streaming_video_body_class( $classes ) {

	   $classes[] = 'streaming-video-container';
	   return $classes;
	}

	?>

	<div id="streaming-page-container">
	    <section>

		    <?php if ( function_exists('yoast_breadcrumb') ) {
		      yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		    } ?>

		    <div class="streaming-video">		      
			    <div class="streaming-iframe">
					<div class="streaming-container">

						<?php do_shortcode('[su_shadow style="default" inline="no" class=""]'); ?>

							<?php

								// Get the video URL and put it in the $video variable
								// 
								$videoID = get_post_meta($post->ID, 'video_urls', true);
								// Check if there is in fact a video URL
								if ($videoID) {
									echo '<div class="video-container">';
									// Echo the embed code via oEmbed
									echo wp_oembed_get( 'https://www.youtube.com/watch?v=' . $videoID ); 
									echo '</div>';
								}

							?>

						<?php do_shortcode('[/su_shadow]'); ?>

					</div>	
					<div class="notification-area">
						<h2>OUR LIVE STREAM IS IN HD</h2>
						<h3>For best picture results, set YouTube quality settings <i class="fa fa-cog" aria-hidden="true"></i> to <strong>720p HD</strong></h3>
					</div>					   

					<div class="page-content">
 						
 						<!-- Pulling in the Page Content -->

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

						<?php the_content(); ?>

					</div>

					<div class="clearfix"></div>

					<div class="tiny-video-container">
						<?php 
						// Calls up the YouTube video thumbnail or, if no URL is provided, the featured image from WordPress

						// Add a container and a link around the video
						echo '<div class="tiny-video-thumb">';
						echo '<a href="' . get_permalink() . '" title="Go to ' . the_title() . '" rel="bookmark">';

						if ( $video_urls ) { // if there is a video URL
							
							// Get the video URL from custom field
							$videoID = get_post_meta($post->ID, 'video_urls', true); 
							// Query YouTube for video meta data
							$thumb_query_url = 'http://gdata.youtube.com/feeds/api/videos/' . $videoID . '?v=2&alt=jsonc?modestbranding=1';
							// Decode the json data from YouTube and put it in a readable format
							$json = json_decode(file_get_contents( $thumb_query_url ));
							// Echo out the thumbnail, give it height and weight and set the alternate description to post title
							echo '<img src="' . $json->data->thumbnail->sqDefault . '" width="60" height="45" alt="' . the_title() . '">';
							echo '</a>';
							echo '</div>';				
												
						} else { // else use the standard featured image
																	
							the_post_thumbnail('tinyThumb', array('alt' => $postTitle, 'title' => $postTitle)); 
																		
						} ?> 

						<?php endwhile; ?>
						
						<?php endif; ?>
					</div>
				</div>
			</div>
		</section>
		
	</div>

  <div class="clearfix"></div>

  	<div class="welcome-message">      

        <?php
            if ( is_user_logged_in() ) {
                echo 'Welcome, registered Christian user!';
            } else {
                echo 'Welcome, Visitor!';
            };
          ?> 
    </div> 
</div> <!-- #post-container -->   

<?php get_footer(); ?>