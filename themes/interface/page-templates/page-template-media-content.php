
<?php

/**
 * Template Name: Media Page Template
 *
 * Displays the Media Page with links to Videos and Images.
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

    <!-- Drop in the media menu -->


    <?php

      $defaults = array(
        'theme_location'  => '',
        'menu'            => 'Media Menu',
        'container'       => 'div',
        'container_class' => '',
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

      <h2>

        <?php wp_nav_menu( $defaults ); ?>

      </h2>


      <!-- Pulling in the Page Content -->

      <?php if (have_posts()) :
         while (have_posts()) :
            the_post(); ?>
             <p><?php  the_content(); ?> </p>
        
        <?php 
         endwhile;

            endif; ?>

      </section>

  </div><!-- #page-container -->

    </section>


      

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