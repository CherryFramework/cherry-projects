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

do_action( 'cherry_projects_before_main_content' ); ?>

	<section>
		<h2> Cherry Projects </h2>
		<?php
			the_content();

			cherry_projects()->projects_data->render_projects();
		?>

	</section> <?php

do_action( 'cherry_projects_after_main_content' );

get_footer( 'cherry_products' );

?>
