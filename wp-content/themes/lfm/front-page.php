<?php
/**
 * The template for displaying Author archive pages
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>


<div id="main-content" class="main-content">

<?php
  if ( is_front_page() && twentyfourteen_has_featured_posts() ) {
    // Include the featured content template.
    get_template_part( 'featured-content' );
  }
?>

  <div id="primary" class="content-area">
    <div id="content" class="site-content" role="main">

    <?php
      if ( have_posts() ) :
        // Start the Loop.
        while ( have_posts() ) : the_post();

          /*
           * Include the post format-specific template for the content. If you want to
           * use this in a child theme, then include a file called called content-___.php
           * (where ___ is the post format) and that will be used instead.
           */
          get_template_part( 'content', get_post_format() );

        endwhile;
        // Previous/next post navigation.
        twentyfourteen_paging_nav();

      else :
        // If no content, include the "No posts found" template.
        get_template_part( 'content', 'none' );

      endif;
?>
<?php if ( current_user_can( 'publish_level', get_the_ID() ) ): ?>
<div class="new-level-outer">
  <div class="entry-content">
    <a href="/wp-admin/post-new.php?post_type=level" class="new-level-link">Add a new level</a>
  </div>
</div>
<?php endif;
      $args = array(
        'post_type' => 'level',
        'post_status' => 'publish'
      );
      $query = new WP_Query( $args );
      if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
          get_template_part( 'content', get_post_format() );
        endwhile;
      endif;
      wp_reset_postdata();
    ?>

    </div><!-- #content -->
  </div><!-- #primary -->
  <?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
