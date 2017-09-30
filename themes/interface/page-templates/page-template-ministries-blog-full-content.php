<?php
/**
 * Template Name: Ministries Page Full Content Display
 *
 * Displays the Ministries Page Blog with Full Content Display.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<?php get_header(); ?>
	<div id="page-container">
		<div class="open-door">
			<section>
				
					<?php if ( function_exists('yoast_breadcrumb') ) {
						yoast_breadcrumb('<p id="breadcrumbs">','</p>');
					} ?>

						<!-- Pulling in Page Content -->

						<?php if (have_posts()) :
						   while (have_posts()) :
						      the_post(); ?>
						       <p><?php  the_content(); ?> </p>
						  
						  <?php endwhile;
						endif; ?>
			</section>
			<div class="clearfix"></div>
			<section>
				
				<!-- Admin Edit Post -->
				
				<h6><strong><?php edit_post_link(); ?></strong></h6>			

				<?php
					if ( is_user_logged_in() ) {
					    echo 'Welcome, registered Christian user!';
					} else {
					    echo 'Welcome, Visitor!';
					};
				?>		
			</section>
		</div>
	</div><!-- #page-container -->	
	
<?php get_footer(); ?>