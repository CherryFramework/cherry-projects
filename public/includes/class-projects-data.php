<?php
/**
 * Cherry Project
 *
 * @package   Cherry_Project
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Portfolio data.
 *
 * @since 1.0.0
 */
class Cherry_Project_Data {

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
	 * Post query object.
	 *
	 * @var null
	 */
	private $posts_query = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_get_new_projects', array( $this, 'get_new_projects' ) );
		add_action( 'wp_ajax_nopriv_get_new_projects', array( $this, 'get_new_projects' ) );

		$this->default_options = array(
			'projects-listing-layout'				=> cherry_projects()->get_option( 'projects-listing-layout', 'masonry-layout' ),
			'projects-loading-mode'					=> cherry_projects()->get_option( 'projects-loading-mode', 'ajax-pagination-mode' ),
			'projects-loading-animation'			=> cherry_projects()->get_option( 'projects-loading-animation', 'loading-animation-fade' ),
			'projects-hover-animation'				=> cherry_projects()->get_option( 'projects-hover-animation', 'simple-fade' ),
			'projects-filter-visible'				=> cherry_projects()->get_option( 'projects-filter-visible', 'true' ),
			'projects-filter-type'					=> cherry_projects()->get_option( 'projects-filter-type', 'category' ),
			'projects-category-list'				=> cherry_projects()->get_option( 'projects-category-list', array() ),
			'projects-tags-list'					=> cherry_projects()->get_option( 'projects-tags-list', array() ),
			'projects-order-filter-visible'			=> cherry_projects()->get_option( 'projects-order-filter-visible', 'false' ),
			'projects-order-filter-default-value'	=> cherry_projects()->get_option( 'projects-order-filter-default-value', 'desc' ),
			'projects-orderby-filter-default-value'	=> cherry_projects()->get_option( 'projects-orderby-filter-default-value', 'date' ),
			'projects-posts-format'					=> cherry_projects()->get_option( 'projects-posts-format', 'post-format-all' ),
			'projects-column-number'				=> cherry_projects()->get_option( 'projects-column-number', 3 ),
			'projects-post-per-page'				=> cherry_projects()->get_option( 'projects-post-per-page', 9 ),
			'projects-item-margin'					=> cherry_projects()->get_option( 'projects-item-margin', 4 ),
			'projects-justified-fixed-height'		=> cherry_projects()->get_option( 'projects-justified-fixed-height', 300 ),
			'projects-is-crop-image'				=> cherry_projects()->get_option( 'projects-is-crop-image', 'false' ),
			'projects-crop-image-width'				=> cherry_projects()->get_option( 'projects-crop-image-width', 500 ),
			'projects-crop-image-height'			=> cherry_projects()->get_option( 'projects-crop-image-height', 350 ),
			'projects-masonry-template'				=> cherry_projects()->get_option( 'projects-masonry-template', 'masonry-default.tmpl' ),
			'projects-grid-template'				=> cherry_projects()->get_option( 'projects-grid-template', 'grid-default.tmpl' ),
			'projects-justified-template'			=> cherry_projects()->get_option( 'projects-justified-template', 'justified-default.tmpl' ),
			'projects-list-template'				=> cherry_projects()->get_option( 'projects-list-template', 'list-default.tmpl' ),
		);

		/**
		 * Filter the array of default options.
		 *
		 * @since 1.0.0
		 * @param array options.
		 * @param array The 'the_portfolio_items' function argument.
		 */
		$this->default_options = apply_filters( 'cherry_projects_default_options', $this->default_options );
	}

	/**
	 * [render_projects description]
	 *
	 * @return [type] [description]
	 */
	public function render_projects( $options = array() ) {

		$this->enqueue_styles();
		$this->enqueue_scripts();

		$this->options = wp_parse_args( $options, $this->default_options );

		// The Query.
		$posts_query = $this->get_query_projects_items( array() );

		if ( ! is_wp_error( $posts_query ) ) {

			switch ( $this->options['projects-listing-layout'] ) {
				case 'masonry-layout':
					$template = $this->options['projects-masonry-template'];
					break;
				case 'grid-layout':
					$template = $this->options['projects-grid-template'];
					break;
				case 'justified-layout':
					$template = $this->options['projects-justified-template'];
					break;
				case 'list-layout':
					$template = $this->options['projects-list-template'];
					break;
			}

			$settings = array(
				'list-layout'	=> $this->options['projects-listing-layout'],
				'loading-mode'	=> $this->options['projects-loading-mode'],
				'post-per-page'	=> $this->options['projects-post-per-page'],
				'column-number'	=> $this->options['projects-column-number'],
				'item-margin'	=> $this->options['projects-item-margin'],
				'fixed-height'	=> $this->options['projects-justified-fixed-height'],
				'posts-format'	=> $this->options['projects-posts-format'],
				'template'		=> $template,
			);

			$settings = json_encode( $settings );

			$html = '<div class="cherry-projects-wrapper">';

				if ( 'true' == $this->options['projects-filter-visible'] && $posts_query->have_posts() ) {
					$html .= $this->render_ajax_filter( array() );
				}

				$container_class = 'projects-container ' . $this->options['projects-listing-layout'] . ' ' . $this->options['projects-loading-mode'];

				$html .= sprintf( '<div class="%1$s" data-settings=\'%2$s\'>', $container_class, $settings );
					$html .= '<div class="projects-list" data-all-posts-count="' . $this->posts_query->found_posts . '"></div>';
				$html .= '</div>';

			// Close wrapper.
			$html .= '</div>';

			echo $html;
		}
	}

	/**
	 * Ajax new projects list hook
	 *
	 * @return void
	 */
	public function get_new_projects() {

		if ( ! empty( $_POST ) && array_key_exists( 'settings', $_POST ) ) {

			$settings = $_POST['settings'];

			$term_type = ( 'category' == $this->default_options['projects-filter-type'] ) ? CHERRY_PROJECTS_NAME . '_category' : CHERRY_PROJECTS_NAME . '_tag';
			$query_args = array(
				$term_type       => $settings['slug'],
				'posts_per_page' => $this->default_options['projects-post-per-page'],
				'order'          => $settings['order_settings']['order'],
				'orderby'        => $settings['order_settings']['orderby'],
				'paged'          => intval( $settings['page'] ),
			);

			// The Query.
			$posts_query = $this->get_query_projects_items( $query_args );

			$html = '<div class="projects-list" data-all-posts-count="' . $posts_query->found_posts . '">';
				$html .= $this->render_projects_items( $posts_query, $settings );
			$html .= '</div>';

			echo $html;

			exit();
		}
	}

	/**
	 * Get projects posts query.
	 *
	 * @since  1.0.0
	 * @param  array|string $query_args Arguments to be passed to the query.
	 * @return array|bool               Array if true, boolean if false.
	 */
	public function get_query_projects_items( $query_args = array() ) {

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$defaults_query_args = apply_filters( 'cherry_projects_default_query_args',
			array(
				'post_type'							=> CHERRY_PROJECTS_NAME,
				CHERRY_PROJECTS_NAME . '_category'	=> '',
				'order'								=> 'DESC',
				'orderby'							=> 'date',
				'posts_per_page'					=> 9,
				'paged'								=> $paged,
				'post_status'						=> 'publish',
			)
		);

		$query_args = wp_parse_args( $query_args, $defaults_query_args );

		// The Query.
		$posts_query = new WP_Query( $query_args );

		$this->posts_query = $posts_query;

		if ( ! is_wp_error( $posts_query ) ) {
			return $posts_query;
		} else {
			return false;
		}
	}

	/**
	 * Render projects items.
	 *
	 * @param  [type] $posts_query    Query posts object.
	 * @param  string $listing_layout Listing layout type.
	 * @param  string $template       Template name.
	 * @return string
	 */
	public function render_projects_items( $posts_query, $settings = array() ) {
		$count = 1;
		$html = '';

		$utility = cherry_projects()->get_core()->modules['cherry-utility']->utility;

		if ( $posts_query->have_posts() ) {

			while ( $posts_query->have_posts() ) : $posts_query->the_post();
				$post_id      = $posts_query->post->ID;
				$title        = get_the_title( $post_id );

				$image = $utility->media->get_image( array(
					'size'	=> 'large',
				) );

				$html .= sprintf( '<div %1$s class="%2$s %3$s %4$s %5$s %6$s">',
					'id="quote-' . $post_id .'"',
					'projects-item',
					'item-' . $count,
					( ( $count++ % 2 ) ? 'odd' : 'even' ),
					'animate-cycle-show',
					$this->default_options['projects-listing-layout'] . '-item',
					$this->default_options['projects-hover-animation'] . '-hover'
				);
					$html .= '<div class="inner-wrapper">';
						$html .= $image;
					$html .= '</div>';
				$html .= '</div>';

			endwhile;
		} else {
			echo '<h4>' . __( 'Posts not found', 'cherry-projects' ) . '</h4>';
		}

		// Reset the query.
		wp_reset_postdata();

		return $html;
	}

	/**
	 * Get ajax filter fot list items.
	 *
	 * @since  1.0.0
	 * @param  array $options Filters settings.
	 * @return string.
	 */
	public function render_ajax_filter( $options = array() ) {

		$tax_list = ( 'category' === $this->options['projects-filter-type'] ) ? $this->options['projects-category-list'] : $this->options['projects-tags-list'] ;

		$args = array(
			'type'			=> CHERRY_PROJECTS_NAME,
			'orderby'		=> 'name',
			'order'			=> 'ASC',
			'taxonomy'		=> CHERRY_PROJECTS_NAME . '_' . $this->options['projects-filter-type'],
			'pad_counts'	=> false,
		);
		$order_array = array(
			'desc'			=> __( 'Desc', 'cherry-projects' ),
			'asc'			=> __( 'Asc', 'cherry-projects' ),
		);
		$order_by_array = array(
			'date'			=> __( 'Date', 'cherry-projects' ),
			'name'			=> __( 'Name', 'cherry-projects' ),
			'modified'		=> __( 'Modified', 'cherry-projects' ),
			'comment_count'	=> __( 'Comments', 'cherry-projects' ),
		);

		$terms = get_categories( $args );

		$html = '<div class="projects-filters with-ajax" data-order-default="' . $this->options['projects-order-filter-default-value'] . '" data-orderby-default="' . $this->options['projects-orderby-filter-default-value'] . '">';

			/**
			 * Filtered before terms list render
			 *
			 * @since 1.0.0
			 */
			$html .= apply_filters( 'cherry-projects-before-filters-html', '' );

			$html .= '<div class="projects-filters-list-wrapper">';

				$html .= '<ul class="projects-filters-list filter-' . $this->options['projects-filter-type'] . '">';

				if ( $terms ) {
					$show_all_text = apply_filters( 'cherry_projects_show_all_text', __( 'Show all', 'cherry-projects' ) );
					$html .= '<li class="active"><span data-cat-id="" data-slug="">'. $show_all_text .'</span></li>';

					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $tax_list ) || empty( $tax_list ) ) {
							$html .= '<li><span data-cat-id="' .  $term->cat_ID . '" data-slug="' .  $term->slug . '">'. $term->name .'</span></li>';
						}
					}
				}
				$html .= '</ul>';

			$html .= '</div>';

			/**
			 * Filtered after terms list render
			 *
			 * @since 1.0.0
			 */
			$html .= apply_filters( 'cherry-projects-after-filters-html', '' );

			if ( 'true' == $this->options['projects-order-filter-visible'] ) {
				$html .= '<div class="projects-order-filters-wrapper">';
					$html .= '<ul class="order-filters">';
						$html .= '<li data-filter-type="order" data-desc-label="' . __( 'Desc', 'cherry-projects' ) . '" data-asc-label="' . __( 'Asc', 'cherry-projects' ) . '">';

							/**
							 * Filter order label text
							 *
							 * @since 1.0.0
							 */
							$html .= apply_filters( 'cherry-projects-order-filter-label', __( 'Order:', 'cherry-projects' ) );

							$html .= '<span class="current">' . $order_array[ $this->options['projects-order-filter-default-value'] ] . '</span>';
							/*$html .= '<ul class="order-list">';

								foreach ( $order_array as $key => $value ) {
									$class = ( $key == $this->options['projects-order-filter-default-value'] ) ? 'class="active"' : '';
									$html .= '<li data-orderby="' . $key . '" ' . $class . '><span>' . $value . '</span></li>';
								}

							$html .= '</ul>';*/

						$html .= '</li>';
						$html .= '<li data-filter-type="orderby">';

							/**
							 * Filter orderby label text
							 *
							 * @since 1.0.0
							 */
							$html .= apply_filters( 'cherry-projects-orderby-filter-label', __( 'Order by:', 'cherry-projects' ) );

							$html .= '<span class="current">' . $order_by_array[ $this->options['projects-orderby-filter-default-value'] ] . '</span>';
								$html .= '<ul class="orderby-list">';

									foreach ( $order_by_array as $key => $value ) {
										$class = ( $key == $this->options['projects-orderby-filter-default-value'] ) ? 'class="active"' : '';
										$html .= '<li data-orderby="' . $key . '" ' . $class . '><span>' . $value . '</span></li>';
									}

								$html .= '</ul>';
						$html .= '</li>';
					$html .= '</ul>';
				$html .= '</div>';
			}
		$html .= '</div>';

		return $html;
	}



	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'cherry-projects-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/styles.css', array(), CHERRY_PROJECTS_VERSION );
		//wp_enqueue_style( 'magnific-popup', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/css/magnific-popup.css', array(), CHERRY_PORTFOLIO_VERSION );
		//wp_enqueue_style( 'swiper', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/css/swiper.css', array(), CHERRY_PORTFOLIO_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		/*wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/min/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'imagesloaded', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/min/imagesloaded.pkgd.min.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'isotope', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/min/isotope.pkgd.min.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'cherry-portfolio-layout-plugin', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/min/cherry-portfolio-layout-plugin.min.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'swiper', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/min/swiper.min.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'cherry-portfolio-script', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/min/cherry-portfolio-scripts.min.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );

		*/

		wp_enqueue_script( 'cherry-projects-plugin', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-plugin.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		wp_enqueue_script( 'cherry-projects-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );

		// Ajax js object portfolio_type_ajax.
		wp_localize_script( 'cherry-projects-scripts', 'cherryProjectsObjects', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

}