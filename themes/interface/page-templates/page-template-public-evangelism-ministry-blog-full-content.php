<?php
/**
 * Template Name: Public Evangelism Ministry Page Full Content Display
 *
 * Displays the Public Evangelism Ministry Page Blog with Full Content Display.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<?php get_header(); ?>
	<div id="page-container">
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
	</div><!-- #page-container -->

		</section>

		<section>
			<div id="post-container">
				<p class="firstcharacter">
					<?php
					// The Query
						query_posts('cat=14'); 


			  		// The Loop 
						while (have_posts()) : the_post(); ?>


					<?php 
					// Place the Title in H1 Tags ?>
						<h1><?php echo get_the_title(); ?></h1>

					<?php
					// Display the Content of the Post
					?>
					

							<?php the_content(); ?> 

				</p>

					
				<!-- Admin Edit Post -->
				
				<h6><strong><?php edit_post_link(); ?></strong></h6>

				<?php
				// End the Loop
					endwhile;
				
				//Reset Query
					wp_reset_postdata(); ?>

				<?php
					if ( is_user_logged_in() ) {
					    echo 'Welcome, registered Christian user!';
					} else {
					    echo 'Welcome, Visitor!';
					};
				?>	
			</div> <!-- #post-container -->		
		</section>

<?php get_footer(); ?>