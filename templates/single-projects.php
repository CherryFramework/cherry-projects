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

if ( have_posts() ) :

	while ( have_posts() ) :

			the_post(); ?>

			<section>
				<?php
					cherry_projects()->projects_single_data->render_projects_single();
				?>
			</section>

	<?php endwhile;

endif;

do_action( 'cherry_projects_after_main_content' );

get_footer( 'cherry_products' );

?>
