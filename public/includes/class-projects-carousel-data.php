<?php
/**
 * Cherry Projects Carousel
 *
 * @package   Cherry_Project
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Projects Carousel data.
 *
 * @since 1.3.0
 */
class Cherry_Project_Carousel_Data extends Cherry_Project_Data {

	/**
	 * Default options array
	 *
	 * @var array
	 */
	public $default_options = array();

	/**
	 * Current options array
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Current template name
	 *
	 * @var string
	 */
	public $template = '';

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_get_new_projects_carousel_items', array( $this, 'get_new_projects_carousel_items' ) );
		add_action( 'wp_ajax_nopriv_get_new_projects_carousel_items', array( $this, 'get_new_projects_carousel_items' ) );

		$this->set_default_options();

	}

	/**
	 * Get defaults data options
	 *
	 * @return void
	 */
	public function set_default_options() {
		$this->default_options = array(
			'slides-per-view'                 => 4,
			'slides-per-view-laptop'          => 3,
			'slides-per-view-album-tablet'    => 2,
			'slides-per-view-portrait-tablet' => 2,
			'slides-per-view-mobile'          => 1,
			'slides-per-group'                => 3,
			'space-between-slides'            => 10,
			'speed'                           => 500,
			'loop'                            => false,
			'navigation'                      => true,
			'post-per-page'                   => 9,
			'filter-type'                     => 'category',
			'single-term'                     => '',
			'category-list'                   => array(),
			'tags-list'                       => array(),
			'template'                        => 'carousel-default.tmpl',
			'echo'                            => true,
		);

		/**
		 * Filter the array of default options.
		 *
		 * @since 1.3.0
		 * @param array options.
		 */
		$this->default_options = apply_filters( 'cherry_projects_carousel_default_options', $this->default_options );
	}

	/**
	 * Render project term
	 *
	 * @return string html string
	 */
	public function render_projects_carousel( $options = array() ) {
		$this->enqueue_styles();
		$this->enqueue_scripts();

		$this->options = wp_parse_args( $options, $this->default_options );

		$id_instance = uniqid();

		$this->options['id'] = $id_instance;

		$settings = json_encode( $this->options );

		$html = '<div class="cherry-projects-carousel-wrapper">';

			if ( empty( $this->options['single-term'] ) ) {

				$html .= $this->render_carousel_ajax_filter();
			}

			$containerClassArray = array(
				'cherry-projects-carousel-container',
				'cherry-animation-container',
				'swiper-container',
			);

			$html .= sprintf( '<div id="cherry-projects-carousel-%1$s" class="%2$s" data-settings=\'%3$s\'>', $id_instance, implode( ' ', $containerClassArray ), $settings );
				$html .= '<div class="cherry-projects-carousel-list swiper-wrapper"></div>';

				if ( filter_var( $this->options['navigation'], FILTER_VALIDATE_BOOLEAN ) ) {
					$html .= '<div id="cherry-projects-carousel-button-next-' . $id_instance . '" class="swiper-button-next"></div>';
					$html .= '<div id="cherry-projects-carousel-button-prev-' . $id_instance . '" class="swiper-button-prev"></div>';
				}

			$html .= '</div>';
			$html .= '<div class="cherry-projects-ajax-loader"><div class="cherry-spinner cherry-spinner-double-bounce"><div class="cherry-double-bounce1"></div><div class="cherry-double-bounce2"></div></div></div>';
		// Close wrapper.
		$html .= '</div>';

		if ( ! filter_var( $this->options['echo'], FILTER_VALIDATE_BOOLEAN ) ) {
			return $html;
		}

		echo $html;
	}

	/**
	 * Ajax new projects carousel list hook
	 *
	 * @return void
	 */
	public function get_new_projects_carousel_items() {

		if ( ! empty( $_POST ) && array_key_exists( 'settings', $_POST ) ) {

			$settings = $_POST['settings'];

			$term_type = ( 'category' == $settings['filter-type'] ) ? CHERRY_PROJECTS_NAME . '_category' : CHERRY_PROJECTS_NAME . '_tag';

			$query_args = array(
				$term_type       => $settings['slug'],
				'posts_per_page' => $settings['post-per-page'],
			);

			// The Query.
			$posts_query = $this->get_query_projects_items( $query_args );

			$count = 1;
			$html = '';

			if ( $posts_query->have_posts() ) {

				// Item template.
				$template = $this->get_template_by_name( $settings['template'], 'projects-carousel' );

				$macros    = '/%%.+?%%/';
				$callbacks = $this->setup_template_data( $settings );

				while ( $posts_query->have_posts() ) : $posts_query->the_post();
					$post_id  = $posts_query->post->ID;

					$template_content = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );

					$size_array	= cherry_projects()->projects_data->cherry_utility->satellite->get_thumbnail_size_array( 'large' );

					$html .= sprintf( '<div %1$s class="%2$s %3$s %4$s %5$s">',
						'id="quote-' . $post_id .'"',
						'swiper-slide projects-carousel-item projects-item-instance cherry-animation-item',
						'item-' . $count,
						( ( $count++ % 2 ) ? 'odd' : 'even' ),
						'animate-cycle-show'
					);
						$html .= '<div class="inner-wrapper">';
							$html .= $template_content;
						$html .= '</div>';
					$html .= '</div>';

					$callbacks->clear_data();
				endwhile;
			} else {
				echo '<h4>' . esc_html__( 'Posts not found', 'cherry-projects' ) . '</h4>';
			}

			// Reset the query.
			wp_reset_postdata();

			echo $html;

			exit();
		}
	}

	/**
	 * Get ajax filter fot list items.
	 *
	 * @since  1.0.0
	 * @param  array $options Filters settings.
	 * @return string.
	 */
	public function render_carousel_ajax_filter() {

		$tax_list = ( 'category' === $this->options['filter-type'] ) ? $this->options['category-list'] : $this->options['tags-list'];

		// $tax_list is array checking or convert to array.
		if ( is_string( $tax_list ) && ! empty( $tax_list ) ) {
			$tax_list = explode( ',', $tax_list );
		}

		$args = array(
			'type'       => CHERRY_PROJECTS_NAME,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'taxonomy'   => CHERRY_PROJECTS_NAME . '_' . $this->options['filter-type'],
			'pad_counts' => false,
		);

		$terms = get_categories( $args );

		$html = '<div class="projects-filters with-ajax">';

			/**
			 * Filtered before terms list render
			 *
			 * @since 1.0.0
			 */
			$html .= apply_filters( 'cherry-projects-before-filters-html', '' );

				$html .= '<div class="projects-filters-list-wrapper">';

					$html .= '<ul class="projects-filters-list filter-' . $this->options['filter-type'] . '">';

					if ( $terms ) {
						$show_all_text = apply_filters( 'cherry_projects_show_all_text', esc_html__( 'Show all', 'cherry-projects' ) );
						$html .= '<li class="active"><span data-cat-id="" data-slug="">'. $show_all_text .'</span></li>';

						$_tax_list = $this->prepare_to_wpml( $tax_list );

						foreach ( $terms as $term ) {
							if ( ( is_array( $_tax_list ) && in_array( $term->slug, $_tax_list ) ) || empty( $_tax_list ) ) {

								$html .= '<li><span data-cat-id="' .  $term->cat_ID . '" data-slug="' .  $term->slug . '">'. $term->name .'</span></li>';
							}
						}
					}
					$html .= '</ul>';

				$html .= '</div>';

		$html .= '</div>';

		return $html;
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.3.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/magnific-popup.css', array(), '1.1.0' );
		wp_enqueue_style( 'cherry-projects-swiper-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/swiper.min.css', array(), CHERRY_PROJECTS_VERSION );
		wp_enqueue_style( 'cherry-projects-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/styles.css', array(), CHERRY_PROJECTS_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.3.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'imagesloaded', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/imagesloaded.pkgd.min.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
		wp_enqueue_script( 'cherry-projects-swiper-js', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/swiper.jquery.min.js', array( 'jquery' ), '1.1.0', true );

		wp_enqueue_script( 'cherry-projects-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
	}

}
