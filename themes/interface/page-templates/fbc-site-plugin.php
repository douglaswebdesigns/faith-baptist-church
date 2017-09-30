<?php

/*
Plugin Name: FBC Site Plugin
Plugin URI: http://douglaswebdesigns.com/prison-services-plugin
Description: This plugin customizes the WordPress Admin Dashboard.
Version: 1.0
Author: Paul Douglas
Author URI: http://douglaswebdesigns.com
License: GPLv2
*/

?>

<?php 

// Remove Comments menu item for all but Administrators
function dwd_remove_comments_menu_item() {
    $user = wp_get_current_user();
    if ( ! $user->has_cap( 'manage_options' ) ) {
        remove_menu_page( 'edit-comments.php' );
    }
}
add_action( 'admin_menu', 'dwd_remove_comments_menu_item' );

// Move Pages above Media
function dwd_change_menu_order( $menu_order ) {
    return array(
        'index.php',
        'edit.php',
        'edit.php?post_type=page',
        'upload.php',
    );
}
add_filter( 'custom_menu_order', '__return_true' );
add_filter( 'menu_order', 'dwd_change_menu_order' );

?>