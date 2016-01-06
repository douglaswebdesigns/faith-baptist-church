<?php
/**
*Function that trims excerpt by number of characters and ensures that the last word doesn't get cut off
 */
function get_excerpt(){
$excerpt = get_the_content();
$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
$excerpt = strip_shortcodes($excerpt);
$excerpt = strip_tags($excerpt);
$excerpt = substr($excerpt, 0, 360);
$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
$excerpt = $excerpt = '<p>'.$excerpt.'... </p>';
return $excerpt;
}
/**
 * Widget for business layout that shows selected page content,title and featured image. Uses function above to control excerpt length by number of characters and not words
 * Construct the widget.
 * i.e. Name, description and control options.
 */
class interface_child_service_widget extends WP_Widget {
	function interface_child_service_widget() {
		$widget_ops = array( 'classname' => 'widget_service', 'description' => __( 'Display Services by no.of chars( Business Layout )', 'interface' ) );
		$control_ops = array( 'width' => 200, 'height' =>250 );
		parent::WP_Widget( false, $name = __( 'Theme Horse: Services Custom', 'interface' ), $widget_ops, $control_ops);
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'number' => '8','page_id0'=>'','page_id1'=>'','page_id2'=>'','page_id3'=>'','page_id4'=>'','page_id5'=>'','page_id6'=>'','page_id7'=>'',));
		$number = absint( $instance[ 'number' ] );
		for ( $i=0; $i<$number; $i++ ) {
			$var = 'page_id'.$i;
			$defaults[$var] = '';
		} ?>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>">
				<?php _e( 'Number of Services:', 'interface' ); ?>
			</label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
		</p>

		<?php	for ( $i=0; $i<$number; $i++ ) {
			$var = 'page_id'.$i;
			$var = absint( $instance[ $var ] );
		}
		?>
		<?php for( $i=0; $i<$number; $i++) { ?>
		<p>
			<label for="<?php echo $this->get_field_id( key($defaults) ); ?>">
				<?php _e( 'Page', 'interface' ); ?>
				:</label>
				<?php wp_dropdown_pages( array( 'show_option_none' =>' ','name' => $this->get_field_name( key($defaults) ), 'selected' => $instance[key($defaults)] ) ); ?>
			</p>
			<?php
		next( $defaults );// forwards the key of $defaults array
	}
}

function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	$instance['number'] = absint( $new_instance['number'] );

	for( $i=0; $i<$instance['number']; $i++ ) {
		$var = 'page_id'.$i;
		$instance[ $var] = absint( $new_instance[ $var ] );
	}

	return $instance;
}

function widget( $args, $instance ) {
	extract( $args );
	extract( $instance );
	$number = empty( $instance['number'] ) ? 8 : $instance['number'];

	global $post;
	global $interface_theme_setting_value;
	$options = $interface_theme_setting_value;
	$page_array = array();
	for( $i=0; $i<$number ; $i++ ) {
		$var = 'page_id'.$i;
		$page_id = isset( $instance[ $var ] ) ? $instance[ $var ] : '';

		if( !empty( $page_id ) )
 				array_push( $page_array, $page_id );// Push the page id in the array
 		}
 		$get_featured_pages = new WP_Query( array(
 			'posts_per_page' 			=> -1,
 			'post_type'					=>  array( 'page' ),
 			'post__in'		 			=> $page_array,
 			'orderby' 		 			=> 'post__in'
 			) );
 			echo $before_widget; ?>
 			<div class="column clearfix">
 				<?php
 				$j = 1;
 				while( $get_featured_pages->have_posts() ):$get_featured_pages->the_post();
 				$page_title = get_the_title();
 				if( $j % 4 == 3 && $j > 1 ) {
 					$service_class = "one-fourth clearfix-half";
 				}
 				elseif ( $j % 4 == 1 && $j > 1 ) {
 					$service_class = "one-fourth clearfix-half clearfix-fourth";
 				}
 				else {
 					$service_class = "one-fourth";
 				}
 				?>
 				<div class="<?php echo $service_class; ?>">
 					<div class="service-item clearfix">
 						<?php
 						if ( has_post_thumbnail() ) {
 							echo'<div class="service-icon">'.get_the_post_thumbnail( $post->ID, 'icon' ).'</div>';
 						}
 						?>
 						<h3 class="service-title"><?php echo $page_title; ?></h3>
 					</div>
 					<!-- .service-item -->
 					<article>
 <?php echo get_excerpt(); ?>
 					</article>
 					<?php if( !empty( $options[ 'post_excerpt_more_text' ] ) ) { ?>
 					<a class="more-link" title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>">
 						<?php echo $options[ 'post_excerpt_more_text' ]; ?></a>
 						<?php } ?>
 					</div>
 					<!-- .one-fourth -->
 					<?php $j++; ?>
 					<?php endwhile;
		 		// Reset Post Data
 					wp_reset_query();
 					?>
 				</div>
 				<!-- .column -->
 				<?php echo $after_widget;
 			}
 		}
function myplugin_register_widgets() {
	register_widget( 'interface_child_service_widget' );
}

add_action( 'widgets_init', 'myplugin_register_widgets' );
 		/**************************************************************************************/
?>