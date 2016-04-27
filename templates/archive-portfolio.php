<?php
/**
 * Template Name: Portfolio
 *
 * The template for displaying CPT Portfolio.
 *
 * @package Cherry_Portfolio
 * @since   1.0.0
 */
?>
<article <?php if ( function_exists( 'cherry_attr' ) ) cherry_attr( 'post' ); ?>>

	<?php
		$data = new Cherry_Portfolio_Data;
		$data->the_portfolio();
	?>

</article>