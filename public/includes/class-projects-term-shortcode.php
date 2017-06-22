<?php
/**
 * Cherry Projects Term Shortcode.
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
 * @since 1.0.0
 */
class Cherry_Projects_Term_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'cherry_projects_terms';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
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
		 * @since 1.0.0
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
				'title' => esc_html__( 'Cherry Projects Terms', 'cherry-projects' ),
				'file'  => CHERRY_PROJECTS_DIR . 'public/includes/ext/class-cherry-projects-terms-elementor-module.php',
				'class' => 'Cherry_Projects_Terms_Elementor_Widget',
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
					'slug'        => 'cherry-prijects-plugin',
					'shortcodes'  => array(
						array(
							'title'       => esc_html__( 'Projects Terms', 'cherry-projects' ),
							'description' => esc_html__( 'The shortcode displays Category and Tag sections content listing with set parameters.', 'cherry-projects' ),
							'icon'        => '<span class="dashicons dashicons-category"></span>',
							'slug'        => 'cherry_projects_terms',
							'options'     => $this->shortcode_args(),
						),
					),
				)
			);
	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.0.0
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
		$defaults = cherry_projects()->projects_term_data->default_options;

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $modify_atts, $shortcode );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		return cherry_projects()->projects_term_data->render_projects_term( $atts );
	}

	/**
	 * Register shortcode arguments.
	 *
	 * @return array
	 */
	public function shortcode_args() {

		return apply_filters( 'cherry_projects_terms_shortcode_arguments', array(
			'term_type' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Filter type', 'cherry-projects' ),
				'description'   => esc_html__( 'Select if you want to filter posts by tag or by category.', 'cherry-projects' ),
				'value'         => 'category',
				'display-input' => true,
				'options'       => array(
					'category' => array(
						'label' => esc_html__( 'Category', 'cherry-projects' ),
					),
					'tag' => array(
						'label' => esc_html__( 'Tag', 'cherry-projects' ),
					),
				),
			),

			'listing_layout' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Terms listing layout', 'cherry-projects' ),
				'description'   => esc_html__( 'Choose terms listing view layout.', 'cherry-projects' ),
				'value'         => 'grid-layout',
				'display_input' => false,
				'options'       => array(
					'grid-layout' => array(
						'label'   => esc_html__( 'Grid', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-grid.svg',
						'slave'   => 'projects-listing-layout-grid-layout',
					),
					'masonry-layout' => array(
						'label'   => esc_html__( 'Masonry', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-masonry.svg',
						'slave'   => 'projects-listing-layout-masonry-layout',
					),
					'cascading-grid-layout' => array(
						'label'   => esc_html__( 'Cascading grid', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-cascading-grid.svg',
						'slave'   => 'projects-listing-layout-cascading-grid-layout',
					),
					'list-layout' => array(
						'label'   => esc_html__( 'List', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-listing.svg',
						'slave'   => 'projects-listing-layout-list-layout',
					),
				),
			),

			'loading_animation' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Loading animation', 'cherry-projects' ),
				'description'   => esc_html__( 'Choose terms loading animation', 'cherry-projects' ),
				'value'         => 'loading-animation-move-up',
				'class'         => '',
				'display_input' => false,
				'options'       => array(
					'loading-animation-fade' => array(
						'label'   => esc_html__( 'Fade animation', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fade.svg',
					),
					'loading-animation-scale' => array(
						'label'   => esc_html__( 'Scale animation', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-scale.svg',
					),
					'loading-animation-move-up' => array(
						'label'   => esc_html__( 'Move Up animation', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-move-up.svg',
					),
					'loading-animation-flip' => array(
						'label'   => esc_html__( 'Flip animation', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-flip.svg',
					),
					'loading-animation-helix' => array(
						'label'   => esc_html__( 'Helix animation', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-helix.svg',
					),
					'loading-animation-fall-perspective' => array(
						'label'   => esc_html__( 'Fall perspective animation', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fall-perspective.svg',
					),
				),
			),

			'device_layout_column_number' => array(
				'type'        => 'component-tab-horizontal',
			),

			'column_number_desktop_layout' => array(
				'type'        => 'settings',
				'parent'      => 'device_layout_column_number',
				'title'       => esc_html__( 'Desktop', 'cherry-projects' ),
				'description' => esc_html__( 'Define column number for desktop layout', 'cherry-projects' ),
			),

			'column_number_laptop_layout' => array(
				'type'        => 'settings',
				'parent'      => 'device_layout_column_number',
				'title'       => esc_html__( 'Laptop', 'cherry-projects' ),
				'description' => esc_html__( 'Define column number for laptop layout', 'cherry-projects' ),
			),

			'column_number_album_tablet_layout' => array(
				'type'        => 'settings',
				'parent'      => 'device_layout_column_number',
				'title'       => esc_html__( 'Album Tablet', 'cherry-projects' ),
				'description' => esc_html__( 'Define column number for tablet layout', 'cherry-projects' ),
			),

			'column_number_portrait_tablet_layout' => array(
				'type'        => 'settings',
				'parent'      => 'device_layout_column_number',
				'title'       => esc_html__( 'Portrait Tablet', 'cherry-projects' ),
				'description' => esc_html__( 'Define column number for tablet layout', 'cherry-projects' ),
			),

			'column_number_mobile_layout' => array(
				'type'        => 'settings',
				'parent'      => 'device_layout_column_number',
				'title'       => esc_html__( 'Mobile', 'cherry-projects' ),
				'description' => esc_html__( 'Define column number for mobile layout', 'cherry-projects' ),
			),

			'column_number' => array(
				'type'        => 'slider',
				'parent'      => 'column_number_desktop_layout',
				'title'       => esc_html__( 'Column number', 'cherry-projects' ),
				'description' => esc_html__( 'Select number of columns for masonry and grid projects layouts. (Min 2, max 6)', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 3,
			),

			'column_number_laptop' => array(
				'type'        => 'slider',
				'parent'      => 'column_number_laptop_layout',
				'title'       => esc_html__( 'Labtop column number', 'cherry-projects' ),
				'description' => esc_html__( 'Select laptop number of columns for masonry and grid projects layouts. (Min 1, max 6)', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 3,
			),

			'column_number_album_tablet' => array(
				'type'        => 'slider',
				'parent'      => 'column_number_album_tablet_layout',
				'title'       => esc_html__( 'Album Tablet column number', 'cherry-projects' ),
				'description' => esc_html__( 'Select album tablet number of columns for masonry and grid projects layouts. (Min 1, max 6)', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 2,
			),

			'column_number_portrait_tablet' => array(
				'type'        => 'slider',
				'parent'      => 'column_number_portrait_tablet_layout',
				'title'       => esc_html__( 'Portrait Tablet column number', 'cherry-projects' ),
				'description' => esc_html__( 'Select portrait tablet number of columns for masonry and grid projects layouts. (Min 1, max 6)', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 2,
			),

			'column_number_mobile' => array(
				'type'        => 'slider',
				'parent'      => 'column_number_mobile_layout',
				'title'       => esc_html__( 'Tablet column number', 'cherry-projects' ),
				'description' => esc_html__( 'Select mobile number of columns for masonry and grid projects layouts. (Min 1, max 6)', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 1,
			),

			'post_per_page' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Posts per page', 'cherry-projects' ),
				'description' => esc_html__( 'Select how many posts per page do you want to display(-1 means that will show all projects)', 'cherry-projects' ),
				'max_value'   => 50,
				'min_value'   => -1,
				'value'       => 6,
			),

			'item_margin' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Item margin', 'cherry-projects' ),
				'description' => esc_html__( 'Select projects item margin (outer indent) value.', 'cherry-projects' ),
				'max_value'   => 50,
				'min_value'   => 0,
				'value'       => 4,
			),

			'grid_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Grid template', 'cherry-projects' ),
				'description' => esc_html__( 'Grid content template', 'cherry-projects' ),
				'value'       => 'terms-grid-default.tmpl',
			),

			'masonry_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Masonry template', 'cherry-projects' ),
				'description' => esc_html__( 'Masonry content template', 'cherry-projects' ),
				'value'       => 'terms-masonry-default.tmpl',
			),

			'cascading_grid_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Cascading grid template', 'cherry-projects' ),
				'description' => esc_html__( 'Cascading grid template', 'cherry-projects' ),
				'value'       => 'terms-cascading-grid-default.tmpl',
			),

			'list_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'List template', 'cherry-projects' ),
				'description' => esc_html__( 'List content template', 'cherry-projects' ),
				'value'       => 'terms-list-default.tmpl',
			),
		) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance )
			self::$instance = new self;

		return self::$instance;
	}

}

Cherry_Projects_Term_Shortcode::get_instance();
