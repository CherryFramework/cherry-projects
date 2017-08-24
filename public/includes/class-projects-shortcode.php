<?php
/**
 * Cherry Projects Shortcode.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Projects shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Projects_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'cherry_projects';

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
		add_action( 'init', array( $this, 'register_shortcode' ), -1 );

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
				'title' => esc_html__( 'Cherry Projects', 'cherry-projects' ),
				'file'  => CHERRY_PROJECTS_DIR . 'public/includes/ext/class-cherry-projects-elementor-module.php',
				'class' => 'Cherry_Projects_Elementor_Widget',
				'icon'  => 'eicon-gallery-grid',
				'atts'  => $this->shortcode_args(),
			));

			/*cherry_projects_elementor_compat( array(
				$this->tag() => array(
					'title' => esc_html__( 'Cherry Projects', 'cherry-projects' ),
					'file'  => CHERRY_PROJECTS_DIR . 'public/includes/ext/class-cherry-projects-elementor-module.php',
					'class' => 'Cherry_Projects_Elementor_Widget',
					'icon'  => 'eicon-gallery-grid',
					'atts'  => $this->shortcode_args(),
				),
			) );*/
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
						'title'       => esc_html__( 'Projects', 'cherry-projects' ),
						'description' => esc_html__( 'Shortcode is used to display the projects list with set parameters.', 'cherry-projects' ),
						'icon'        => '<span class="dashicons dashicons-layout"></span>',
						'slug'        => 'cherry_projects',
						'options'     => $this->shortcode_args(),
					),
				),
			)
		);
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

		return apply_filters( 'cherry_projects_shortcode_arguments', array(
			'listing_layout' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Projects listing layout', 'cherry-projects' ),
				'description'   => esc_html__( 'Choose projects listing view layout.', 'cherry-projects' ),
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
					'justified-layout' => array(
						'label'   => esc_html__( 'Justified', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-justified.svg',
						'slave'   => 'projects-listing-layout-justified-layout',
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

			'loading_mode' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Pagination mode', 'cherry-projects' ),
				'description'   => esc_html__( 'Choose projects pagination mode', 'cherry-projects' ),
				'value'         => 'ajax-pagination-mode',
				'class'         => '',
				'display_input' => false,
				'options'       => array(
					'ajax-pagination-mode' => array(
						'label'   => esc_html__( 'Ajax pagination', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-pagination.svg',
					),
					'more-button-mode' => array(
						'label'   => esc_html__( 'More button', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-more-button.svg',
					),
					'lazy-loading-mode' => array(
						'label'   => esc_html__( 'Lazy loading', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-lazy-loading.svg',
					),
					'none-mode' => array(
						'label'   => esc_html__( 'None', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-none.svg',
					),
				),
			),

			'loading_animation' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Loading animation', 'cherry-projects' ),
				'description'   => esc_html__( 'Choose posts loading animation', 'cherry-projects' ),
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

			'hover_animation' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Hover animation', 'cherry-projects' ),
				'description'   => esc_html__( 'Choose posts images hover animation', 'cherry-projects' ),
				'value'         => 'simple-scale',
				'class'         => '',
				'display_input' => false,
				'options'       => array(
					'simple-fade' => array(
						'label'   => esc_html__( 'Fade', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/hover-fade.svg',
					),
					'simple-scale' => array(
						'label'   => esc_html__( 'Scale', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/hover-scale.svg',
					),
					'custom' => array(
						'label'   => esc_html__( 'Custom', 'cherry-projects' ),
						'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/inherit.svg',
					),
				),
			),

			'filter_visible' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Filters', 'cherry-projects' ),
				'description' => esc_html__( 'Enable/disable listing filters', 'cherry-projects' ),
				'value'       => 'true',
				'toggle' => array(
					'true_toggle'  => esc_html__( 'Show', 'cherry-projects' ),
					'false_toggle' => esc_html__( 'Hide', 'cherry-projects' ),
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
						'slave' => 'projects-filter-type-category',
					),
					'tag' => array(
						'label' => esc_html__( 'Tag', 'cherry-projects' ),
						'slave' => 'projects-filter-type-tag',
					),
				),
			),

			'category_list' => array(
				'type'     => 'select',
				'title'    => esc_html__( 'Projects filter categories list', 'cherry-projects' ),
				'multiple' => true,
				'value'    => array(),
				'class'    => 'cherry-multi-select',
				'options'  => $category_list,
				'master'   => 'projects-filter-type-category',
			),

			'tags_list' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Projects filter tags list', 'cherry-projects' ),
				'multiple'         => true,
				'value'            => array(),
				'class'            => 'cherry-multi-select',
				'options'          => $tag_list,
				'master'           => 'projects-filter-type-tag',
			),

			'order_filter_visible' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Order filters', 'cherry-projects' ),
				'description' => esc_html__( 'Enable/disable order filters', 'cherry-projects' ),
				'value'       => 'false',
				'toggle'      => array(
					'true_toggle'  => 'On',
					'false_toggle' => 'Off',
					'true_slave'   => 'projects-order-filter-visible-true',
					'false_slave'  => 'projects-order-filter-visible-false',
				),
			),

			'order_filter_default_value' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Order filter default value', 'cherry-projects' ),
				'value'         => 'desc',
				'display-input' => true,
				'options'       => array(
					'desc' => array(
						'label' => esc_html__( 'DESC', 'cherry-projects' ),
					),
					'asc' => array(
						'label' => esc_html__( 'ASC', 'cherry-projects' ),
					),
				),
				'master'		=> 'projects-order-filter-visible-true',
			),

			'orderby_filter_default_value' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Order by filter default value', 'cherry-projects' ),
				'value'         => 'date',
				'display-input' => true,
				'options'       => array(
					'date' => array(
						'label' => esc_html__( 'Date', 'cherry-projects' ),
					),
					'name' => array(
						'label' => esc_html__( 'Name', 'cherry-projects' ),
					),
					'modified' => array(
						'label' => esc_html__( 'Modified', 'cherry-projects' ),
					),
					'comment_count' => array(
						'label' => esc_html__( 'Comments', 'cherry-projects' ),
					),
				),
				'master'		=> 'projects-order-filter-visible-true',
			),

			'posts_format' => array(
				'type'          => 'radio',
				'title'         => esc_html__( 'Post Format', 'cherry-projects' ),
				'value'         => 'post-format-all',
				'display-input' => true,
				'options'       => array(
					'post-format-all' => array(
						'label' => esc_html__( 'All formats', 'cherry-projects' ),
					),
					'post-format-standard' => array(
						'label' => esc_html__( 'Standard', 'cherry-projects' ),
					),
					'post-format-image' => array(
						'label' => esc_html__( 'Image', 'cherry-projects' ),
					),
					'post-format-gallery' => array(
						'label' => esc_html__( 'Gallery', 'cherry-projects' ),
					),
					'post-format-audio' => array(
						'label' => esc_html__( 'Audio', 'cherry-projects' ),
					),
					'post-format-video' => array(
						'label' => esc_html__( 'Video', 'cherry-projects' ),
					),
				),
			),

			'single_term' => array(
				'type'            => 'text',
				'title'           => esc_html__( 'Single term slug', 'cherry-projects' ),
				'value'           => '',
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
				'description' => esc_html__( 'Select number of columns for masonry and grid projects layouts. (Min 1, max 6)', 'cherry-projects' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 4,
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
				'title'       => esc_html__( 'Mobile column number', 'cherry-projects' ),
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
				'value'       => 9,
			),

			'item_margin' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Item margin', 'cherry-projects' ),
				'description' => esc_html__( 'Select projects item margin (outer indent) value.', 'cherry-projects' ),
				'max_value'   => 50,
				'min_value'   => 0,
				'value'       => 4,
			),

			'justified_fixed_height' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Justified fixed height', 'cherry-projects' ),
				'description' => esc_html__( 'Select projects item justified height value.', 'cherry-projects' ),
				'max_value'   => 1000,
				'min_value'   => 50,
				'value'       => 300,
				'master'      => 'projects-listing-layout-justified-layout',
			),

			'grid_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Grid template', 'cherry-projects' ),
				'description' => esc_html__( 'Grid content template', 'cherry-projects' ),
				'value'       => 'grid-default.tmpl',
				'master'      => 'projects-listing-layout-grid-layout',
			),

			'masonry_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Masonry template', 'cherry-projects' ),
				'description' => esc_html__( 'Masonry content template', 'cherry-projects' ),
				'value'       => 'masonry-default.tmpl',
				'master'      => 'projects-listing-layout-masonry-layout',
			),

			'justified_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Justified template', 'cherry-projects' ),
				'description' => esc_html__( 'Justified content template', 'cherry-projects' ),
				'value'       => 'justified-default.tmpl',
				'master'      => 'projects-listing-layout-justified-layout',
			),

			'cascading_grid_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Cascading grid template', 'cherry-projects' ),
				'description' => esc_html__( 'Cascading grid template', 'cherry-projects' ),
				'value'       => 'cascading-grid-default.tmpl',
				'master'      => 'projects-listing-layout-cascading-grid-layout',
			),

			'list_template' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'List template', 'cherry-projects' ),
				'description' => esc_html__( 'List content template', 'cherry-projects' ),
				'value'       => 'list-default.tmpl',
				'master'      => 'projects-listing-layout-list-layout',
			),
		) );
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
		$defaults = cherry_projects()->projects_data->default_options;

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $modify_atts, $shortcode );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		return cherry_projects()->projects_data->render_projects( $atts );
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

Cherry_Projects_Shortcode::get_instance();
