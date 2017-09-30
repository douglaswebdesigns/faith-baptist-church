<?php

/**
 * Template Name: Staff Page Template
 *
 * Displays the Staff Page with links to Videos and Images.
 *
 * @package Theme Horse
 * @subpackage Interface
 * @since Interface 1.0
 */

?>

<style>
li { font-size: 1em; }
</style>
<?php get_header(); ?>
  <div id="staff-page-container">
    <section>

    <?php if ( function_exists('yoast_breadcrumb') ) {
      yoast_breadcrumb('<p id="breadcrumbs">','</p>');
    } ?>

    <!-- Drop in the media menu -->



      <!-- Pulling in the Page Content -->
    <div class="staff-member">
      
    <p class="staff-member-content print-only">
      
      <?php if (have_posts()) :
         while (have_posts()) :
            the_post(); ?>

    </p>
        

           <p class="content"> <?php the_content(); ?> </p>

        
       

      <?php 

        endwhile;

          endif; ?>

    </section>

    <div class="staff-sidebar">

      <section>

        <h3>FBC - Church Staff</h3>

          <?php

            $defaults = array(
              'theme_location'  => '',
              'menu'            => 'Staff Menu',
              'container'       => 'div',
              'container_class' => 'staff-menu',
              'container_id'    => '',
              'menu_class'      => 'menu',
              'menu_id'         => '',
              'echo'            => true,
              'fallback_cb'     => 'wp_page_menu',
              'before'          => '',
              'after'           => '',
              'link_before'     => '',
              'link_after'      => '',
              'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
              'depth'           => 0,
              'walker'          => ''
            );

            ?>

            <p class="staff-page">

              <?php wp_nav_menu( $defaults ); ?>

            </p>

      </section>

    </div><!-- #staff-sidebar -->

  </div><!-- #staff-page-container -->

  <div class="clearfix"></div>

  <div class="welcome-message">      

        <?php
            if ( is_user_logged_in() ) {
                echo 'Welcome, registered Christian user!';
            } else {
                echo 'Welcome, Visitor!';
            };
          ?>  
  </div> <!-- #post-container -->   




<?php get_footer(); ?>