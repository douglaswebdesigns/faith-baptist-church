<?php
/**
 * Template Name: Video Page Template
 *
 * Displays the Video Page of the theme.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.1
 */
?>
<?php get_header(); ?>

<?php
	/** 
	 * interface_before_main_container hook
	 */
	do_action( 'interface_before_main_container' );
?>
<?php
		/** 
		 * interface_business_template_content hook
		 *
		 * HOOKED_FUNCTION_NAME PRIORITY
		 *
		 * interface_display_business_template_content 10
		 */
		do_action( 'interface_video_template_content' );
	?>
<?php
	/** 
	 * interface_after_main_container hook
	 */
	do_action( 'interface_after_main_container' );
?>

<?php get_footer(); ?>