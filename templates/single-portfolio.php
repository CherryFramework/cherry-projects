<?php
/**
 * The Template for displaying single CPT Portfolio.
 *
 */

while ( have_posts() ) :

		the_post(); ?>

		<article <?php if ( function_exists( 'cherry_attr' ) ) cherry_attr( 'post' ); ?>>

		<?php

			do_action( 'cherry_entry_before' );

			$data = new Cherry_Portfolio_Data;
			$data->portfolio_single_data();

			?>

		</article>

		<?php

		do_action( 'cherry_entry_after' );

		?>

<?php endwhile; ?>