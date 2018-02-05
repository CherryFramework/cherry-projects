<?php
/**
 * Template Name: Projects
 *
 * The template for displaying CPT Projects.
 *
 * @package Cherry_Projects
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! did_action( 'get_header' ) ) {
	get_header();

	do_action( 'cherry_projects_before_main_content' ); ?>

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php }

	while ( have_posts() ) : the_post();
		cherry_projects()->projects_single_data->render_projects_single();

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	endwhile;

	 do_action( 'cherry_projects_after_main_content' );

	if ( did_action( 'cherry_projects_before_main_content' ) ) { ?>
				</article><!-- #post-## -->
			</main><!-- .site-main -->
		</div><!-- .content-area -->

	<?php do_action( 'cherry_projects_content_after' );

	get_sidebar();

	get_footer();
} ?>
