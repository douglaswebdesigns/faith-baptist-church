<?php
/**
 * Template Name: Preach The Word Ministry Page Full Content Display
 *
 * Displays the Preach The Word Ministry Page Blog with Full Content Display.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<?php get_header(); ?>


	<?php putRevSlider('preach-the-word') ?>



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

		<div class="clearfix"></div>

		<section>
			<div id="featured-image">

				<?php if ( has_post_thumbnail() ) : ?>
			    
			    <?php the_post_thumbnail(); ?>
			   
				<?php endif; ?>

			</div>
			

			<div class="post-content">

				<p class="firstcharacter">

					<h2>Updates</h2>

					<?php
					
					// The Query
						query_posts('cat=167,168'); 


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

			</div>

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
		</section>

<?php get_footer(); ?>