<?php
/**
 * Cherry Projects Term
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
class Cherry_Project_Term_Data extends Cherry_Project_Data {

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
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_default_options();
	}

	/**
	 * Get defaults data options
	 *
	 * @return void
	 */
	public function set_default_options() {
		$this->default_options = array(
			'term-type'               => 'category',
			'listing-layout'          => 'grid-layout',
			'loading-animation'       => 'loading-animation-fade',
			'column-number'           => 3,
			'post-per-page'           => 6,
			'item-margin'             => 10,
			'grid-template'           => 'terms-grid-default.tmpl',
			'masonry-template'        => 'terms-masonry-default.tmpl',
			'list-template'           => 'terms-list-default.tmpl',
			'cascading-grid-template' => 'terms-cascading-grid-default.tmpl',
			'echo'                    => true,
		);

		/**
		 * Filter the array of default options.
		 *
		 * @since 1.0.0
		 * @param array options.
		 * @param array The 'the_portfolio_items' function argument.
		 */
		$this->default_options = apply_filters( 'cherry_projects_term_default_options', $this->default_options );
	}

	/**
	 * Render project term
	 *
	 * @return string html string
	 */
	public function render_projects_term( $options = array() ) {
		$this->enqueue_styles();
		$this->enqueue_scripts();

		$this->options = wp_parse_args( $options, $this->default_options );

		switch ( $this->options['listing-layout'] ) {
			case 'masonry-layout':
				$this->template = $this->options['masonry-template'];
				break;
			case 'grid-layout':
				$this->template = $this->options['grid-template'];
				break;
			case 'list-layout':
				$this->template = $this->options['list-template'];
				break;
			case 'cascading-grid-layout':
				$this->template = $this->options['cascading-grid-template'];
				break;
		}

		$settings = array(
			'list-layout'   => $this->options['listing-layout'],
			'post-per-page' => $this->options['post-per-page'],
			'column-number' => $this->options['column-number'],
			'item-margin'   => $this->options['item-margin'],
		);

		$settings = json_encode( $settings );

		$terms = $this->get_projects_terms(
			array(
				'taxonomy' => CHERRY_PROJECTS_NAME . '_' . $this->options['term-type'],
				'number'   => $this->options['post-per-page'],
			)
		);

		$html = '<div class="cherry-projects-terms-wrapper">';

			$container_class = 'projects-terms-container cherry-animation-container ' . $this->options['listing-layout'] . ' ' . $this->options['loading-animation'];

			$html .= sprintf( '<div class="%1$s" data-settings=\'%2$s\'>', $container_class, $settings );
				$html .= '<div class="projects-terms-list cherry-animation-list">';
					$html .= $this->render_projects_term_items( $terms );
				$html .= '</div>';
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
	 * Get term set object
	 *
	 * @param  array  $args Args
	 * @return object
	 */
	public function get_projects_terms( $args = array() ) {

		$defaults_args = apply_filters( 'cherry_projects_default_terms_args',
			array(
				'taxonomy'   => null,
				'order'      => 'ASC',
				'number'     => '',
				'offset'     => '',
				'hide_empty' => false,
			)
		);

		$args = wp_parse_args( $args, $defaults_args );

		$terms = get_terms( $args );

		if ( isset( $terms ) && $terms ) {
			return $terms;
		} else {
			return false;
		}
	}

	/**
	 * Render terms item
	 *
	 * @param  object $terms Terms objects
	 * @return string
	 */
	public function render_projects_term_items( $terms ) {
		$count = 1;
		$html = '';

		if ( $terms ) {

			// Item template.
			$template = $this->get_template_by_name( $this->template, 'projects-terms' );

			$macros = '/%%.+?%%/';

			$callbacks = $this->setup_template_data( $this->options );

			foreach ( $terms as $term_key => $term ) {
				$callbacks->set_term_data( $term );
				$template_content = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );

				$html .= sprintf( '<div %1$s class="%2$s %3$s %4$s">',
					'id="projects-term-' . $term_key .'"',
					'projects-terms-item projects-item-instance cherry-animation-item simple-fade-hover animate-cycle-show',
					'item-' . $count,
					( ( $count++ % 2 ) ? 'odd' : 'even' )
				);
					$html .= '<div class="inner-wrapper">';
						$html .= $template_content;
					$html .= '</div>';
				$html .= '</div>';

				$callbacks->clear_term_data();
			}

		} else {
			echo '<h4>' . esc_html__( 'Terms not found', 'cherry-projects' ) . '</h4>';
		}

		return $html;
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/magnific-popup.css', array(), '1.1.0' );
		wp_enqueue_style( 'cherry-projects-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/styles.css', array(), CHERRY_PROJECTS_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'imagesloaded', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/imagesloaded.pkgd.min.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
		wp_enqueue_script( 'cherry-projects-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );

		$cascading_list_map = apply_filters( 'cherry_projects_terms_cascading_list_map', array( 1, 2, 2, 3, 3, 3, 4, 4, 4, 4 ) );

		wp_localize_script( 'cherry-projects-scripts', 'cherryProjectsTermObjects',
			array(
				'cascadingListMap' => $cascading_list_map,
			)
		);
	}

}
