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
        while ( have_posts() ) : the_post(); ?>

          <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php the_title( '<header class="entry-header"><h1 class="entry-title inline-edit-title">', '</h1></header>' ); ?>
            <div class="entry-content">
              <div class="inline-edit"><?php the_content(); ?></div>
            </div>
          </article>

        <?php endwhile;
        // Previous/next post navigation.
        twentyfourteen_paging_nav();

      else :
        // If no content, include the "No posts found" template.
        get_template_part( 'content', 'none' );

      endif;
    ?>

    </div><!-- #content -->
  </div><!-- #primary -->
  <?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
