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

get_header( 'cherry_products' );

do_action( 'cherry_projects_before_main_content' );

if ( have_posts() ) : ?>

	<section>
		<h2> Cherry Projects </h2>
		<?php
			global $wp_query;

			the_content();

			$attr = array(
				'filter-visible' => false,
				'single-term'    => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
			);

			cherry_projects()->projects_data->render_projects( $attr );
		?>

	</section> <?php

endif;

do_action( 'cherry_projects_after_main_content' );

get_footer( 'cherry_products' );

?>
