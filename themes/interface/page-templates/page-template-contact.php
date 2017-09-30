<?php
/**
 * Template Name: Contact Page Template
 *
 * Displays the contact page template.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */
?>
<?php get_header(); ?>


		<?php if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb('<p id="breadcrumbs">','</p>');
		} ?>


	<section>
		<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d23610.266814181974!2d-84.093797!3d42.293819!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x883cce25a07f3e05%3A0x537b7ed23bfb135b!2s4030+Kalmbach+Rd%2C+Chelsea%2C+MI+48118!5e0!3m2!1sen!2sus!4v1428617528186" width="100%" height="350" frameborder="0" style="border:0"></iframe>
	</section>

<?php
	/** 
	 * interface_before_main_container hook
	 */
	do_action( 'interface_before_main_container' );
?>



<?php
		/** 
		 * interface_contact_page_template_content hook
		 *
		 * HOOKED_FUNCTION_NAME PRIORITY
		 *
		 * interface_display_contact_page_template_content 10
		 */
		do_action( 'interface_contact_page_template_content' );
	?>
<?php
	/** 
	 * interface_after_main_container hook
	 */
	do_action( 'interface_after_main_container' );
?>
  <div id="secondary">
    <?php get_sidebar( 'contact-page' ); ?>
  </div>
<?php get_footer(); ?>