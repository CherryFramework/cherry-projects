<?php
/**
 * Template Name: Projects
 *
 * The archive index page for CPT Projects.
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

	global $wp_query;

	$default_options = cherry_projects()->projects_data->default_options;

	if ( is_tax() ) {
		$filter_visible = false;
	} elseif ( filter_var( $default_options['filter-visible'], FILTER_VALIDATE_BOOLEAN ) ) {
		$filter_visible = true;
	} else {
		$filter_visible = false;
	}

	$filter_type = $default_options['filter-type'];

	if ( isset( $wp_query->query_vars['taxonomy'] ) && 'projects_category' === $wp_query->query_vars['taxonomy'] ) {
		$filter_type = 'category';
	}

	if ( isset( $wp_query->query_vars['taxonomy'] ) && 'projects_tag' === $wp_query->query_vars['taxonomy'] ) {
		$filter_type = 'tag';
	}

	$attr = array(
		'filter-visible' => $filter_visible,
		'single-term'    => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
		'filter-type'    => $filter_type,
	);

	cherry_projects()->projects_data->render_projects( $attr );

	do_action( 'cherry_projects_after_main_content' );

	if ( did_action( 'cherry_projects_before_main_content' ) ) { ?>
				</article><!-- #post-## -->
			</main><!-- .site-main -->
		</div><!-- .content-area -->

	<?php do_action( 'cherry_projects_content_after' );

	get_sidebar();

	get_footer();
} ?>
