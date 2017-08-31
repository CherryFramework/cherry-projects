<?php
/**
 * Cherry Projects Carousel Shortcode.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Projects term shortcode.
 *
 * @since 1.3.0
 */
class Cherry_Projects_Carousel_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.3.0
	 * @var   string
	 */
	public static $name = 'cherry_projects_carousel';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.3.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		// Register shortcode on 'init'.
		add_action( 'init', array( $this, 'register_shortcode' ) );

		// Shortcode insert module registration
		if ( is_admin() ) {
			add_action( 'after_setup_theme', array( $this, 'shortcode_registration' ), 11 );
		}
	}

	/**
	 * Returns shortcode tag.
	 *
	 * @return string
	 */
	public function tag() {

		/**
		 * Filters a shortcode name.
		 *
		 * @since 1.3.0
		 * @param string $this->name Shortcode name.
		 */
		$tag = apply_filters( self::$name . '_shortcode_name', self::$name );

		return $tag;
	}

	/**
	 * Registers the [$this->name] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {

		add_shortcode( $this->tag(), array( $this, 'do_shortcode' ) );

		if ( defined( 'ELEMENTOR_VERSION' ) ) {

			require_once( CHERRY_PROJECTS_DIR . 'public/includes/ext/class-cherry-projects-elementor-compat.php' );

			cherry_projects_elementor_compat()->add_shortcode( $this->tag(), array(
				'title' => esc_html__( 'Cherry Projects Carousel', 'cherry-projects' ),
				'file'  => CHERRY_PROJECTS_DIR . 'public/includes/ext/class-cherry-projects-carousel-elementor-module.php',
				'class' => 'Cherry_Projects_Carousel_Elementor_Widget',
				'icon'  => 'eicon-gallery-grid',
				'atts'  => $this->shortcode_args(),
			));

		}
	}

	/**
	 * Shortcode registration
	 *
	 * @return void
	 */
	public function shortcode_registration() {
		cherry_projects()->get_core()->init_module( 'cherry5-insert-shortcode', array() );

		cherry5_register_shortcode(
			array(
				'title'       => esc_html__( 'Projects', 'cherry-projects' ),
				'description' => esc_html__( 'Showcase your projects using a variety of layouts with Cherry Projects plugin', 'cherry-projects' ),
				'icon'        => '<span class="dashicons dashicons-layout"></span>',
				'slug'        => 'cherry-projects-plugin',
				'shortcodes'  => array(
					array(
						'title'       => esc_html__( 'Projects Carousel', 'cherry-projects' ),
						'description' => esc_html__( 'The shortcode displays projects carousel.', 'cherry-projects' ),
						'icon'        => '<span class="dashicons dashicons-category"></span>',
						'slug'        => 'cherry_projects_carousel',
						'options'     => $this->shortcode_args(),
					),
				),
			)
		);
	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.3.0
	 * @param  array  $atts      The user-inputted arguments.
	 * @param  string $content   The enclosed content (if the shortcode is used in its enclosing form).
	 * @param  string $shortcode The shortcode tag, useful for shared callback functions.
	 * @return string
	 */
	public function do_shortcode( $atts, $content = null, $shortcode = '' ) {
		$modify_atts = array();

		if ( ! empty( $atts ) ) {
			foreach ( $atts as $key => $value ) {
				$modify_atts[ str_replace( '_', '-', $key ) ] = $value;
			}
		}

		// Set up the default arguments.
		$defaults = cherry_projects()->projects_carousel_data->default_options;

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $modify_atts, $shortcode );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		return cherry_projects()->projects_carousel_data->render_projects_carousel( $atts );
	}

	/**
	 * Register shortcode arguments.
	 *
	 * @return array
	 */
	public function shortcode_args() {
		$utility = cherry_projects()->get_core()->modules['cherry-utility']->utility;

		$category_list =  $utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_category', 'slug' );
		$tag_list      = $utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_tag', 'slug' );

		return apply_filters( 'cherry_projects_carousel_shortcode_arguments', array(
			'slides_per_view' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Slides Per View', 'cherry-projects' ),
				'description' => esc_html__( 'Number of slides per view (slides visible at the same time on slider container).', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 4,
			),

			'slides_per_view_laptop' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Laptop Slides Per View', 'cherry-projects' ),
				'description' => esc_html__( 'Laptop Number of slides per view (slides visible at the same time on slider container).', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 3,
			),

			'slides_per_view_album_tablet' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Album Tablet Slides Per View', 'cherry-projects' ),
				'description' => esc_html__( 'Album Tablet Number of slides per view (slides visible at the same time on slider container).', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 2,
			),

			'slides_per_view_portrait_tablet' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Portrait Tablet Slides Per View', 'cherry-projects' ),
				'description' => esc_html__( 'Portrait Tablet Number of slides per view (slides visible at the same time on slider container).', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 2,
			),

			'slides_per_view_mobile' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Mobile Tablet Slides Per View', 'cherry-projects' ),
				'description' => esc_html__( 'Mobile Tablet Number of slides per view (slides visible at the same time on slider container).', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 1,
			),

			'space_between_slides' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space Between', 'cherry-projects' ),
				'description' => esc_html__( 'Width of the space between slides(px)', 'cherry-projects' ),
				'max_value'   => 100,
				'min_value'   => 0,
				'value'       => 20,
			),

			'speed' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Duration', 'cherry-projects' ),
				'description' => esc_html__( 'Duration of transition between slides (in ms)', 'cherry-projects' ),
				'max_value'   => 3000,
				'min_value'   => 100,
				'value'       => 500,
			),

			'navigation' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Slider navigation', 'cherry-projects' ),
				'description' => esc_html__( 'Use Slider navigation?', 'cherry-projects' ),
				'value'       => 'true',
				'toggle' => array(
					'true_toggle'  => esc_html__( 'Yes', 'cherry-projects' ),
					'false_toggle' => esc_html__( 'No', 'cherry-projects' ),
				),
			),

			'filter_type' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Filter type', 'cherry-projects' ),
				'description'   => esc_html__( 'Select if you want to filter posts by tag or by category.', 'cherry-projects' ),
				'value'         => 'category',
				'display-input' => true,
				'options'       => array(
					'category' => array(
						'label' => esc_html__( 'Category', 'cherry-projects' ),
						'slave' => 'carousel-projects-filter-type-category',
					),
					'tag' => array(
						'label' => esc_html__( 'Tag', 'cherry-projects' ),
						'slave' => 'carousel-projects-filter-type-tag',
					),
				),
			),

			'category_list' => array(
				'type'     => 'select',
				'title'    => esc_html__( 'Carousel filter categories list', 'cherry-projects' ),
				'multiple' => true,
				'value'    => array(),
				'class'    => 'cherry-multi-select',
				'options'  => $category_list,
				'master'   => 'carousel-projects-filter-type-category',
			),

			'tags_list' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Carousel filter tags list', 'cherry-projects' ),
				'multiple'         => true,
				'value'            => array(),
				'class'            => 'cherry-multi-select',
				'options'          => $tag_list,
				'master'           => 'carousel-projects-filter-type-tag',
			),

			'template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Template', 'cherry-projects' ),
				'description' => esc_html__( 'Template', 'cherry-projects' ),
				'value'       => 'carousel-default.tmpl',
			),
		) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.3.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

}

Cherry_Projects_Carousel_Shortcode::get_instance();
