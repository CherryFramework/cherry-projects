<?php
/**
 * The Template for displaying single CPT Projects.
 *
 */

while ( have_posts() ) :

		the_post(); ?>

		<article <?php if ( function_exists( 'cherry_attr' ) ) cherry_attr( 'post' ); ?>>
			<h2> archive-projects </h2>
		<?php

			//do_action( 'cherry_entry_before' );

			//$data = new Cherry_Portfolio_Data;
			//$data->portfolio_single_data();

			?>

		</article>

		<?php

		//do_action( 'cherry_entry_after' );

		?>

<?php endwhile; ?>