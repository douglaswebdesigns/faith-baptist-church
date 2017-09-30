<?php
/**
 * Template Name: SermonAudio.com Page Full Content Display
 *
 * Displays the SermonAudio.com Page Blog with Full Content Display.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<?php get_header(); ?>
		<div id="page-container">

			<section id="post">
				<div class="special">
					<?php if ( function_exists('yoast_breadcrumb') ) {
						yoast_breadcrumb('<p id="breadcrumbs">','</p>');
					} ?>
					<div class="sermon-audio-container">
					
					<!-- Pulling in audiosermon.com User ID: 1171615361810 Content -->

						<!--Begin SermonAudio.com Browser-->
							
							<!--Begin SermonAudio Link Button-->

								<script language="JavaScript" type="text/javascript">document.write("<" + "script  src='http://www.sermonaudio.com/code_sermonlist.asp?sourceid=fbcchelsea&style=2&hideheader=false&hidelogo=false&alwaysbible=false&rows=30&sourcehref=" + escape(location.href) + "'><","/script>");
								</script>

							<!--End SermonAudio Link Button-->

						<!--End SermonAudio Browser-->


						<img src="http://fbcchelsea.org/img/logo/roku logo.png" style="text-align:center;box-shadow:none"><br /><img src="http://fbcchelsea.org/img/icons/acrossplatforms.png" style="text-align:center;box-shadow:none;">
					</div>
				</div>
			</section>
		</div>		

		<section id="post">
			<div id="post-container">
				<p class="firstcharacter">
					<?php
					// The Query
						query_posts('cat=98'); 


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