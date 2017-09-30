<?php

// Theme Customizations Stylesheets Enqueued Here
function theme_enqueue_styles() {
    
    wp_enqueue_style( 'interface-child-styles', get_stylesheet_directory_uri() . '/css/style.css', '', '1.1.0', '' );
    wp_enqueue_style( 'interface-parent-styles', get_template_directory_uri() . '/style.css', '', '2.1.5', '' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

