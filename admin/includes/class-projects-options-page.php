<?php
/**
 * Projects options page
 *
 * @package   Cherry_Projects_Options_Page
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

class Cherry_Projects_Options_Page {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * UI builder instance
	 *
	 * @var object
	 */
	public $ui_builder = null;

	/**
	 * Existing field types
	 *
	 * @var array
	 */
	public $field_types = array();

	/**
	 * Projects options
	 *
	 * @var array
	 */
	public $projects_options = array();

	/**
	 * Cherry utility instance
	 *
	 * @var null
	 */
	public $utility = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'render_page' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'after_setup_theme', array( $this, 'init' ), 10 );

		add_action( 'wp_ajax_cherry_projects_ajax_request', array( $this, 'cherry_projects_ajax_request' ) );

	}

	/**
	 * Run initialization of modules.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$this->utility = cherry_projects()->get_core()->modules['cherry-utility']->utility;

		$this->projects_options = array(
			'listing-layout' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Projects listing layout', 'cherry-projects' ),
				'description'	=> esc_html__( 'Choose projects listing view layout.', 'cherry-projects' ),
				'value'			=> 'grid-layout',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'grid-layout' => array(
						'label'		=> esc_html__( 'Grid', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-grid.svg',
						'slave'		=> 'projects-listing-layout-grid-layout',
					),
					'masonry-layout' => array(
						'label'		=> esc_html__( 'Masonry', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-masonry.svg',
						'slave'		=> 'projects-listing-layout-masonry-layout',
					),
					'justified-layout' => array(
						'label'		=> esc_html__( 'Justified', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-justified.svg',
						'slave'		=> 'projects-listing-layout-justified-layout',
					),
					'cascading-grid-layout' => array(
						'label'		=> esc_html__( 'Cascading grid', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-cascading-grid.svg',
						'slave'		=> 'projects-listing-layout-cascading-grid-layout',
					),
					'list-layout' => array(
						'label'		=> esc_html__( 'List', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-listing.svg',
						'slave'		=> 'projects-listing-layout-list-layout',
					),
				),
			),
			'loading-mode' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Pagination mode', 'cherry-projects' ),
				'description'	=> esc_html__( 'Choose projects pagination mode', 'cherry-projects' ),
				'value'			=> 'ajax-pagination-mode',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'ajax-pagination-mode' => array(
						'label'		=> esc_html__( 'Ajax pagination', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-pagination.svg',
					),
					'more-button-mode' => array(
						'label'		=> esc_html__( 'More button', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-more-button.svg',
					),
					'lazy-loading-mode' => array(
						'label'		=> esc_html__( 'Lazy loading', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-lazy-loading.svg',
					),
					'none-mode' => array(
						'label'		=> esc_html__( 'None', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-none.svg',
					),
				),
			),
			'loading-animation' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Loading animation', 'cherry-projects' ),
				'description'	=> esc_html__( 'Choose posts loading animation', 'cherry-projects' ),
				'value'			=> 'loading-animation-move-up',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'loading-animation-fade' => array(
						'label'		=> esc_html__( 'Fade animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fade.svg',
					),
					'loading-animation-scale' => array(
						'label'		=> esc_html__( 'Scale animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-scale.svg',
					),
					'loading-animation-move-up' => array(
						'label'		=> esc_html__( 'Move Up animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-move-up.svg',
					),
					'loading-animation-flip' => array(
						'label'		=> esc_html__( 'Flip animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-flip.svg',
					),
					'loading-animation-helix' => array(
						'label'		=> esc_html__( 'Helix animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-helix.svg',
					),
					'loading-animation-fall-perspective' => array(
						'label'		=> esc_html__( 'Fall perspective animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fall-perspective.svg',
					),
				),
			),
			'hover-animation' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Hover animation', 'cherry-projects' ),
				'description'	=> esc_html__( 'Choose posts images hover animation', 'cherry-projects' ),
				'value'			=> 'simple-scale',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'simple-fade' => array(
						'label'		=> esc_html__( 'Fade', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/hover-fade.svg',
					),
					'simple-scale' => array(
						'label'		=> esc_html__( 'Scale', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/hover-scale.svg',
					),
					'custom' => array(
						'label'		=> esc_html__( 'Custom', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/inherit.svg',
					),
				),
			),
			'filter-visible' => array(
				'type'			=> 'switcher',
				'title'			=> esc_html__( 'Filters', 'cherry-projects' ),
				'description'	=> esc_html__( 'Enable/disable listing filters', 'cherry-projects' ),
				'value'			=> 'true',
			),
			'filter-type' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Filter type', 'cherry-projects' ),
				'description'	=> esc_html__( 'Select if you want to filter posts by tag or by category.', 'cherry-projects' ),
				'value'			=> 'category',
				'display-input'	=> true,
				'options'		=> array(
					'category' => array(
						'label' => esc_html__( 'Category', 'cherry-projects' ),
						'slave'		=> 'projects-filter-type-category',
					),
					'tag' => array(
						'label' => esc_html__( 'Tag', 'cherry-projects' ),
						'slave'		=> 'projects-filter-type-tag',
					),
				),
			),
			'category-list' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Projects filter categories list', 'cherry-projects' ),
				'label'            => '',
				'description'      => '',
				'multiple'         => true,
				'value'            => array(),
				'class'            => 'cherry-multi-select',
				'options'          => false,
				'options_callback' => array( $this->utility->satellite, 'get_terms_array', array( CHERRY_PROJECTS_NAME . '_category', 'slug' ) ),
				'master'           => 'projects-filter-type-category',
			),
			'tags-list' => array(
				'type'             => 'select',
				'title'            => esc_html__( 'Projects filter tags list', 'cherry-projects' ),
				'label'            => '',
				'description'      => '',
				'multiple'         => true,
				'value'            => array(),
				'class'            => 'cherry-multi-select',
				'options'          => false,
				'options_callback' => array( $this->utility->satellite, 'get_terms_array', array( CHERRY_PROJECTS_NAME . '_tag', 'slug' ) ),
				'master'           => 'projects-filter-type-tag',
			),
			'order-filter-visible' => array(
				'type'			=> 'switcher',
				'title'			=> esc_html__( 'Order filters', 'cherry-projects' ),
				'description'	=> esc_html__( 'Enable/disable order filters', 'cherry-projects' ),
				'value'			=> 'false',
				'toggle'		=> array(
					'true_toggle'	=> 'On',
					'false_toggle'	=> 'Off',
					'true_slave'	=> 'projects-order-filter-visible-true',
					'false_slave'	=> 'projects-order-filter-visible-false',
				),
			),
			'order-filter-default-value' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Order filter default value', 'cherry-projects' ),
				'value'			=> 'desc',
				'display-input'	=> true,
				'options'		=> array(
					'desc' => array(
						'label' => esc_html__( 'DESC', 'cherry-projects' ),
					),
					'asc' => array(
						'label' => esc_html__( 'ASC', 'cherry-projects' ),
					),
				),
				'master'		=> 'projects-order-filter-visible-true',
			),
			'orderby-filter-default-value' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Order by filter default value', 'cherry-projects' ),
				'value'			=> 'date',
				'display-input'	=> true,
				'options'		=> array(
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
			'posts-format' => array(
				'type'			=> 'radio',
				'title'			=> esc_html__( 'Post Format', 'cherry-projects' ),
				'value'			=> 'post-format-all',
				'display-input'	=> true,
				'options'		=> array(
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
			'column-number' => array(
				'type'			=> 'slider',
				'title'			=> esc_html__( 'Column number', 'cherry-projects' ),
				'description'	=> esc_html__( 'Select number of columns for masonry and grid projects layouts. (Min 2, max 6)', 'cherry-projects' ),
				'max_value'		=> 6,
				'min_value'		=> 2,
				'value'			=> 3,
			),
			'post-per-page' => array(
				'type'			=> 'slider',
				'title'			=> esc_html__( 'Posts per page', 'cherry-projects' ),
				'description'	=> esc_html__( 'Select how many posts per page do you want to display(-1 means that will show all projects)', 'cherry-projects' ),
				'max_value'		=> 50,
				'min_value'		=> -1,
				'value'			=> 9,
			),
			'item-margin' => array(
				'type'			=> 'slider',
				'title'			=> esc_html__( 'Item margin', 'cherry-projects' ),
				'description'	=> esc_html__( 'Select projects item margin (outer indent) value.', 'cherry-projects' ),
				'max_value'		=> 50,
				'min_value'		=> 0,
				'value'			=> 4,
			),
			'justified-fixed-height' => array(
				'type'			=> 'slider',
				'title'			=> esc_html__( 'Justified fixed height', 'cherry-projects' ),
				'description'	=> esc_html__( 'Select projects item justified height value.', 'cherry-projects' ),
				'max_value'		=> 1000,
				'min_value'		=> 50,
				'value'			=> 300,
				'master'		=> 'projects-listing-layout-justified-layout',
			),
			'grid-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Grid template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Grid content template', 'cherry-projects' ),
				'value'			=> 'grid-default.tmpl',
				'master'		=> 'projects-listing-layout-grid-layout',
			),
			'masonry-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Masonry template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Masonry content template', 'cherry-projects' ),
				'value'			=> 'masonry-default.tmpl',
				'master'		=> 'projects-listing-layout-masonry-layout',
			),
			'justified-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Justified template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Justified content template', 'cherry-projects' ),
				'value'			=> 'justified-default.tmpl',
				'master'		=> 'projects-listing-layout-justified-layout',
			),
			'cascading-grid-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Cascading grid template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Cascading grid template', 'cherry-projects' ),
				'value'			=> 'cascading-grid-default.tmpl',
				'master'		=> 'projects-listing-layout-cascading-grid-layout',
			),
			'list-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'List template', 'cherry-projects' ),
				'description'	=> esc_html__( 'List content template', 'cherry-projects' ),
				'value'			=> 'list-default.tmpl',
				'master'		=> 'projects-listing-layout-list-layout',
			),
			'standard-post-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Standard post template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Standard post template', 'cherry-projects' ),
				'value'			=> 'standard-post-template.tmpl',
			),
			'image-post-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Image post template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Image post template', 'cherry-projects' ),
				'value'			=> 'image-post-template.tmpl',
			),
			'gallery-post-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Gallery post template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Gallery post template', 'cherry-projects' ),
				'value'			=> 'gallery-post-template.tmpl',
			),
			'audio-post-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Audio post template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Audio post template', 'cherry-projects' ),
				'value'			=> 'audio-post-template.tmpl',
			),
			'video-post-template' => array(
				'type'			=> 'text',
				'title'			=> esc_html__( 'Video post template', 'cherry-projects' ),
				'description'	=> esc_html__( 'Video post template', 'cherry-projects' ),
				'value'			=> 'video-post-template.tmpl',
			),
		);

		array_walk( $this->projects_options, array( $this, 'set_field_types' ) );

		if ( in_array( 'slider', $this->field_types ) ) {
			$this->field_types[] = 'stepper';
		}


		$this->ui_builder = cherry_projects()->get_core()->init_module( 'cherry-ui-elements', array( 'ui_elements' => $this->field_types ) );

		return true;
	}

	/**
	 * Store field types used in this widget into class property
	 *
	 * @since  1.0.0
	 * @param  array  $field field data.
	 * @param  [type] $id    field key.
	 * @return bool
	 */
	public function set_field_types( $field, $id ) {

		if ( ! isset( $field['type'] ) ) {
			return false;
		}

		if ( ! in_array( $field['type'], $this->field_types ) ) {
			$this->field_types[] = $field['type'];
		}

		return true;
	}

	/**
	 * Register setting sub page
	 *
	 * @return void
	 */
	public function render_page() {
		add_submenu_page(
			'edit.php?post_type=projects',
			esc_html__( 'Projects Options', 'cherry-projects' ),
			esc_html__( 'Settings', 'cherry-projects' ),
			'edit_theme_options',
			'cherry-projects-options',
			array( $this, 'projects_options_page' ),
			'',
			63
		);
	}

	/**
	 * Option page callback
	 *
	 * @return void
	 */
	public function projects_options_page() {
		$html = '';

		$saved_options = get_option( OPTIONS_NAME );
		$current_options = array_merge( cherry_projects()->default_options, $saved_options );

		$settings = $this->get_fields( $current_options );

		$html = Cherry_Toolkit::render_view(
			CHERRY_PROJECTS_DIR . 'views/options-page.php',
			array(
				'settings' => $settings,
			)
		);

		echo $html;
	}

	/**
	 * Get registered control fields
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_fields( $current_options ) {

		$elements = array(
			'ui-settings' => array(),
			'labels'		=> array(
				'save-button-text' => esc_html__( 'Save', 'cherry-projects' ),
				'define-as-button-text' => esc_html__( 'Define as default', 'cherry-projects' ),
				'restore-button-text' => esc_html__( 'Restore', 'cherry-projects' ),
			),
		);

		foreach ( $this->projects_options as $key => $field ) {

			$value = isset( $current_options[ $key ] ) ? $current_options[ $key ] : false;
			$value = ( false !== $value ) ? $value : Cherry_Toolkit::get_arg( $field, 'value', '' );

			if ( isset( $field['options_callback'] ) ) {
				$callback = $this->get_callback_data( $field['options_callback'] );
				$options  = call_user_func_array( $callback['callback'], $callback['args'] );
			} else {
				$options = Cherry_Toolkit::get_arg( $field, 'options', array() );
			}

			$args = array(
				'type'               => Cherry_Toolkit::get_arg( $field, 'type', 'text' ),
				'id'                 => $key,
				'name'               => $key,
				'value'              => $value,
				'label'              => Cherry_Toolkit::get_arg( $field, 'label', '' ),
				'options'            => $options,
				'multiple'           => Cherry_Toolkit::get_arg( $field, 'multiple', false ),
				'filter'             => Cherry_Toolkit::get_arg( $field, 'filter', false ),
				'size'               => Cherry_Toolkit::get_arg( $field, 'size', 1 ),
				'null_option'        => Cherry_Toolkit::get_arg( $field, 'null_option', 'None' ),
				'multi_upload'       => Cherry_Toolkit::get_arg( $field, 'multi_upload', true ),
				'library_type'       => Cherry_Toolkit::get_arg( $field, 'library_type', 'image' ),
				'upload_button_text' => Cherry_Toolkit::get_arg( $field, 'upload_button_text', 'Choose' ),
				'max_value'          => Cherry_Toolkit::get_arg( $field, 'max_value', '100' ),
				'min_value'          => Cherry_Toolkit::get_arg( $field, 'min_value', '0' ),
				'max'                => Cherry_Toolkit::get_arg( $field, 'max', '100' ),
				'min'                => Cherry_Toolkit::get_arg( $field, 'min', '0' ),
				'step_value'         => Cherry_Toolkit::get_arg( $field, 'step_value', '1' ),
				'style'              => Cherry_Toolkit::get_arg( $field, 'style', 'normal' ),
				'display_input'      => Cherry_Toolkit::get_arg( $field, 'display_input', true ),
				'controls'           => Cherry_Toolkit::get_arg( $field, 'controls', array() ),
				'toggle'             => Cherry_Toolkit::get_arg( $field, 'toggle', array(
					'true_toggle'  => 'On',
					'false_toggle' => 'Off',
					'true_slave'   => '',
					'false_slave'  => '',
				) ),
				'required'           => Cherry_Toolkit::get_arg( $field, 'required', false ),
				'master'             => Cherry_Toolkit::get_arg( $field, 'master', '' ),
				'icon_data'          => Cherry_Toolkit::get_arg( $field, 'icon_data', array() ),
			);

			$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );
			$elements['ui-settings'][] = array(
				'title'			=> Cherry_Toolkit::get_arg( $field, 'title', '' ),
				'description'	=> Cherry_Toolkit::get_arg( $field, 'description', '' ),
				'master'		=> Cherry_Toolkit::get_arg( $field, 'master', '' ),
				'ui-html'		=> $current_element->render(),
			);

		}

		return $elements;
	}

	/**
	 * Parse callback data.
	 *
	 * @since  1.0.0
	 * @param  array $options_callback Callback data.
	 * @return array
	 */
	public function get_callback_data( $options_callback ) {

		if ( 2 === count( $options_callback ) ) {

			$callback = array(
				'callback' => $options_callback,
				'args'     => array(),
			);

			return $callback;
		}

		$callback = array(
			'callback' => array_slice( $options_callback, 0, 2 ),
			'args'     => $options_callback[2],
		);

		return $callback;
	}

	/**
	 * Ajax request
	 *
	 * @since 4.0.0
	 */
	public function cherry_projects_ajax_request() {

		if ( ! empty( $_POST ) && array_key_exists( 'post_array', $_POST ) && array_key_exists( 'nonce', $_POST ) && array_key_exists( 'type', $_POST ) ) {

			$post_array = $_POST['post_array'];
			$nonce		= $_POST['nonce'];
			$type		= $_POST['type'];

			if ( ! current_user_can( 'manage_options' ) ) {
				$response = array(
					'message'	=> esc_html__( 'No right to preserve options', 'cherry-projects' ),
					'type'		=> 'error-notice'
				);

				wp_send_json( $response );
			}

			if ( wp_verify_nonce( $nonce, 'cherry_ajax_nonce' ) ) {

				switch ( $type ) {
					case 'save':
						cherry_projects()->save_options( OPTIONS_NAME, $post_array );
						$response = array(
							'message'	=> esc_html__( 'Options have been saved', 'cherry-projects' ),
							'type'		=> 'success-notice'
						);

						break;
					case 'define_as_default':
						cherry_projects()->save_options( OPTIONS_NAME . '_default', $post_array );
						$response = array(
							'message'	=> esc_html__( 'Settings have been define as default', 'cherry-projects' ),
							'type'		=> 'success-notice'
						);

						break;
					case 'restore':
						$default_options = get_option( OPTIONS_NAME . '_default' );
						cherry_projects()->save_options( OPTIONS_NAME, $default_options );

						$response = array(
							'message'	=> esc_html__( 'Settings have been restored', 'cherry-projects' ),
							'type'		=> 'success-notice'
						);

						break;
				}

				wp_send_json( $response );
			} else {
				exit();
			}

		}
	}

	/**
	 * Enqueue admin styles function.
	 *
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {
		if ( 'projects_page_cherry-projects-options' == $hook_suffix ) {
			wp_enqueue_style( 'projects-admin-style', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/css/admin-style.css', array(), CHERRY_PROJECTS_VERSION );
		}
	}

	/**
	 * Enqueue admin scripts function.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( 'projects_page_cherry-projects-options' == $hook_suffix ) {
			wp_enqueue_script( 'serialize-object', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/js/serialize-object.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
			wp_enqueue_script( 'cherry-projects-admin-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/js/cherry-projects-admin-scripts.js', array( 'jquery', 'cherry-js-core' ), CHERRY_PROJECTS_VERSION, true );

			$options_page_settings = array(
				'please_wait_processing'	=> esc_html__( 'Please wait, processing the previous request', 'cherry-projects' ),
				'redirect_url'				=> menu_page_url( 'cherry-projects-options', false ),
			);

			wp_localize_script( 'cherry-projects-admin-scripts', 'cherryProjectsPluginSettings', $options_page_settings );
		}
	}


	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Cherry_Projects_Options_Page::get_instance();
