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
	 * [$utility description]
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
		//$this->utility = cherry_projects()->get_core()->modules['cherry-utility']->utility;
		//var_dump($this->utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_category', 'slug' ));
		$this->projects_options = array(
			'projects-listing-layout' => array(
				'type'			=> 'radio',
				'title'			=> __( 'Projects listing layout', 'cherry-projects' ),
				'description'	=> __( 'Choose projects listing view layout.', 'cherry-projects' ),
				'value'			=> 'grid-layout',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'grid-layout' => array(
						'label'		=> __( 'Grid', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-grid.svg',
					),
					'masonry-layout' => array(
						'label'		=> __( 'Masonry', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-masonry.svg',
					),
					'justified-layout' => array(
						'label'		=> __( 'Justified', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-justified.svg',
					),
					'list-layout' => array(
						'label'		=> __( 'List', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-listing.svg',
					),
				),
			),
			'projects-loading-mode' => array(
				'type'			=> 'radio',
				'title'			=> __( 'Pagination mode', 'cherry-projects' ),
				'description'	=> __( 'Choose projects pagination mode', 'cherry-projects' ),
				'value'			=> 'ajax-pagination-mode',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'ajax-pagination-mode' => array(
						'label'		=> __( 'Ajax pagination', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-pagination.svg',
					),
					'more-button-mode' => array(
						'label'		=> __( 'More button', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-more-button.svg',
					),
				),
			),
			'projects-loading-animation' => array(
				'type'			=> 'radio',
				'title'			=> __( 'Loading animation', 'cherry-projects' ),
				'description'	=> __( 'Choose posts loading animation', 'cherry-projects' ),
				'value'			=> 'loading-animation-move-up',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'loading-animation-fade' => array(
						'label'		=> __( 'Fade animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fade.svg',
					),
					'loading-animation-scale' => array(
						'label'		=> __( 'Scale animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-scale.svg',
					),
					'loading-animation-move-up' => array(
						'label'		=> __( 'Move Up animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-move-up.svg',
					),
					'loading-animation-flip' => array(
						'label'		=> __( 'Flip animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-flip.svg',
					),
					'loading-animation-helix' => array(
						'label'		=> __( 'Helix animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-helix.svg',
					),
					'loading-animation-fall-perspective' => array(
						'label'		=> __( 'Fall perspective animation', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fall-perspective.svg',
					),
				),
			),
			'projects-hover-animation' => array(
				'type'			=> 'radio',
				'title'			=> __( 'projects hover animation', 'cherry-projects' ),
				'description'	=> __( 'Choose posts images hover animation', 'cherry-projects' ),
				'value'			=> 'simple-scale',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'simple-fade' => array(
						'label'		=> __( 'Fade', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/inherit.svg',
					),
					'simple-scale' => array(
						'label'		=> __( 'Scale', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/inherit.svg',
					),
					'custom' => array(
						'label'		=> __( 'Custom', 'cherry-projects' ),
						'img_src'	=> CHERRY_PROJECTS_URI . 'public/assets/images/svg/inherit.svg',
					),
				),
			),
			'projects-filter-visible' => array(
				'type'			=> 'switcher',
				'title'			=> __( 'Filters', 'cherry-projects' ),
				'description'	=> __( 'Enable/disable listing filters', 'cherry-projects' ),
				'value'			=> 'true',
			),
			'projects-filter-type' => array(
				'type'			=> 'radio',
				'title'			=> 'Filter type',
				'description'	=> __( 'Select if you want to filter posts by tag or by category.', 'cherry-projects' ),
				'value'			=> 'category',
				'display-input'	=> true,
				'options'		=> array(
					'category' => array(
						'label' => __( 'Category', 'cherry-projects' ),
						'slave'		=> 'projects-filter-type-category',
					),
					'tag' => array(
						'label' => __( 'Tag', 'cherry-projects' ),
						'slave'		=> 'projects-filter-type-tag',
					),
				),
			),
			'projects-category-list' => array(
				'type'			=> 'select',
				'title'			=> __( 'Projects filter categories list', 'cherry-projects' ),
				'label'			=> '',
				'description'	=> '',
				'multiple'		=> true,
				'value'			=> array(),
				'class'			=> 'cherry-multi-select',
				//'options'		=> $this->utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_category', 'slug' ),
				'master'		=> 'projects-filter-type-category',
			),
			'projects-tags-list' => array(
				'type'			=> 'select',
				'title'			=> __( 'Projects filter tags list', 'cherry-projects' ),
				'label'			=> '',
				'description'	=> '',
				'multiple'		=> true,
				'value'			=> array(),
				'class'			=> 'cherry-multi-select',
				//'options'		=> $this->utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_tag', 'slug' ),
				'master'		=> 'projects-filter-type-tag',
			),
			'projects-order-filter-visible' => array(
				'type'			=> 'switcher',
				'title'			=> __( 'Order filters', 'cherry-projects' ),
				'description'	=> __( 'Enable/disable order filters', 'cherry-projects' ),
				'value'			=> 'false',
			),
			'projects-order-filter-default-value' => array(
				'type'			=> 'radio',
				'title'			=> __( 'Order filter default value', 'cherry-projects' ),
				'value'			=> 'desc',
				'display-input'	=> true,
				'options'		=> array(
					'desc' => array(
						'label' => __( 'DESC', 'cherry-projects' ),
					),
					'asc' => array(
						'label' => __( 'ASC', 'cherry-projects' ),
					),
				),
			),
			'projects-orderby-filter-default-value' => array(
				'type'			=> 'radio',
				'title'			=> __( 'Order by filter default value', 'cherry-projects' ),
				'value'			=> 'date',
				'display-input'	=> true,
				'options'		=> array(
					'date' => array(
						'label' => __( 'Date', 'cherry-projects' ),
					),
					'name' => array(
						'label' => __( 'Name', 'cherry-projects' ),
					),
					'modified' => array(
						'label' => __( 'Modified', 'cherry-projects' ),
					),
					'comment_count' => array(
						'label' => __( 'Comments', 'cherry-projects' ),
					),
				),
			),
			'projects-posts-format' => array(
				'type'			=> 'radio',
				'title'			=> __( 'Post Format', 'cherry-projects' ),
				'value'			=> 'post-format-all',
				'display-input'	=> true,
				'options'		=> array(
					'post-format-all' => array(
						'label' => __( 'All formats', 'cherry-projects' ),
					),
					'post-format-standard' => array(
						'label' => __( 'Standard', 'cherry-projects' ),
					),
					'post-format-image' => array(
						'label' => __( 'Image', 'cherry-projects' ),
					),
					'post-format-gallery' => array(
						'label' => __( 'Gallery', 'cherry-projects' ),
					),
					'post-format-audio' => array(
						'label' => __( 'Audio', 'cherry-projects' ),
					),
					'post-format-video' => array(
						'label' => __( 'Video', 'cherry-projects' ),
					),
				),
			),
			'projects-column-number' => array(
				'type'			=> 'slider',
				'title'			=> __( 'Column number', 'cherry-projects' ),
				'description'	=> __( 'Select number of columns for masonry and grid projects layouts. (Min 2, max 20)', 'cherry-projects' ),
				'max_value'		=> 10,
				'min_value'		=> 2,
				'value'			=> 3,
			),
			'projects-post-per-page' => array(
				'type'			=> 'slider',
				'title'			=> __( 'Posts per page', 'cherry-projects' ),
				'description'	=> __( 'Select how many posts per page do you want to display', 'cherry-projects' ),
				'max_value'		=> 50,
				'min_value'		=> -1,
				'value'			=> 9,
			),
			'projects-item-margin' => array(
				'type'			=> 'slider',
				'title'			=> __( 'Item margin', 'cherry-projects' ),
				'description'	=> __( 'Select projects item margin (outer indent) value.', 'cherry-projects' ),
				'max_value'		=> 100,
				'min_value'		=> 0,
				'value'			=> 4,
			),
			'projects-justified-fixed-height' => array(
				'type'			=> 'slider',
				'title'			=> __( 'Justified fixed height', 'cherry-projects' ),
				'description'	=> __( 'Select projects item justified height value.', 'cherry-projects' ),
				'max_value'		=> 1000,
				'min_value'		=> 50,
				'value'			=> 300,
			),
			'projects-is-crop-image' => array(
				'type'			=> 'switcher',
				'title'			=> __( 'Crop image', 'cherry-projects' ),
				'description'	=> __( 'Choose if you want to activate images crop.', 'cherry-projects' ),
				'value'			=> 'false',
			),
			'projects-crop-image-width' => array(
				'type'			=> 'stepper',
				'title'			=> __( 'Cropped image width', 'cherry-projects' ),
				'description'	=> __( 'Set width of the cropped image.', 'cherry-projects' ),
				'value'			=> '500',
				'value_step'	=> '1',
				'max_value'		=> '9999',
				'min_value'		=> '10',
			),
			'projects-crop-image-height' => array(
				'type'			=> 'stepper',
				'title'			=> __( 'Cropped image height', 'cherry-projects' ),
				'description'	=> __( 'Set height of the cropped image.', 'cherry-projects' ),
				'value'			=> '350',
				'value_step'	=> '1',
				'max_value'		=> '9999',
				'min_value'		=> '10',
			),
			'projects-masonry-template' => array(
				'type'			=> 'text',
				'title'			=> __( 'Masonry template', 'cherry-projects' ),
				'description'	=> __( 'Masonry content template', 'cherry-projects' ),
				'value'			=> 'masonry-default.tmpl',
			),
			'projects-grid-template' => array(
				'type'			=> 'text',
				'title'			=> __( 'Grid template', 'cherry-projects' ),
				'description'	=> __( 'Grid content template', 'cherry-projects' ),
				'value'			=> 'grid-default.tmpl',
			),
			'projects-justified-template' => array(
				'type'			=> 'text',
				'title'			=> __( 'Justified template', 'cherry-projects' ),
				'description'	=> __( 'Justified content template', 'cherry-projects' ),
				'value'			=> 'justified-default.tmpl',
			),
			'projects-list-template' => array(
				'type'			=> 'text',
				'title'			=> __( 'List template', 'cherry-projects' ),
				'description'	=> __( 'List content template', 'cherry-projects' ),
				'value'			=> 'list-default.tmpl',
			),
		);

		add_filter( 'cherry_core_js_ui_init_settings', array( $this, 'init_ui_js' ), 10 );

		array_walk( $this->projects_options, array( $this, 'set_field_types' ) );

		$this->ui_builder = cherry_projects()->get_core()->init_module( 'cherry-ui-elements', array( 'ui_elements' => $this->field_types ) );

		return true;
	}

	/**
	 * Init UI elements JS
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function init_ui_js() {

		$settings['auto_init'] = true;
		$settings['targets'] = array( 'body' );

		return $settings;
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
	 * [render_page description]
	 * @return [type] [description]
	 */
	public function render_page() {
		add_menu_page(
			__( 'Projects Options', 'cherry-projects' ),
			__( 'Projects Options', 'cherry-projects' ),
			'edit_theme_options',
			'cherry-projects-options',
			array( $this, 'projects_options_page' ),
			'',
			63
		);
	}

	/**
	 *
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
				'save-button-text' => __( 'Save', 'cherry-projects' ),
				'define-as-button-text' => __( 'Define as default', 'cherry-projects' ),
				'restore-button-text' => __( 'Restore', 'cherry-projects' ),
			),
		);

		foreach ( $this->projects_options as $key => $field ) {

			$value = isset( $current_options[ $key ] ) ? $current_options[ $key ] : false;
			$value = ( false !== $value ) ? $value : Cherry_Toolkit::get_arg( $field, 'value', '' );

			if ( isset( $field['options_callback'] ) ) {
				$options = call_user_func( $field['options_callback'] );
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
			);

			$current_element = $this->ui_builder->get_ui_element_instance( $args['type'], $args );
			$elements['ui-settings'][] = array(
				'title'			=> Cherry_Toolkit::get_arg( $field, 'title', '' ),
				'description'	=> Cherry_Toolkit::get_arg( $field, 'description', '' ),
				'ui-html'		=> $current_element->render(),
			);

		}

		return $elements;
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


			if ( wp_verify_nonce( $nonce, 'cherry_ajax_nonce' ) ) {

				switch ( $type ) {
					case 'save':
						cherry_projects()->save_options( OPTIONS_NAME, $post_array );
						$response = array(
							'message'	=> __( 'Options have been saved', 'cherry-projects' ),
							'type'		=> 'success-notice'
						);

						break;
					case 'define_as_default':
						cherry_projects()->save_options( OPTIONS_NAME . '_default', $post_array );
						$response = array(
							'message'	=> __( 'Settings have been define as default', 'cherry-projects' ),
							'type'		=> 'success-notice'
						);

						break;
					case 'restore':
						$default_options = get_option( OPTIONS_NAME . '_default' );
						cherry_projects()->save_options( OPTIONS_NAME, $default_options );

						$response = array(
							'message'	=> __( 'Settings have been restored', 'cherry-projects' ),
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
		if ( 'toplevel_page_cherry-projects-options' == $hook_suffix ) {
			wp_enqueue_style( 'projects-admin-style', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/css/admin-style.css', array(), CHERRY_PROJECTS_VERSION );
		}
	}

	/**
	 * Enqueue admin scripts function.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( 'toplevel_page_cherry-projects-options' == $hook_suffix ) {
			wp_enqueue_script( 'serialize-object', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/js/serialize-object.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
			wp_enqueue_script( 'cherry-projects-admin-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/js/cherry-projects-admin-scripts.js', array( 'jquery', 'cherry-js-core' ), CHERRY_PROJECTS_VERSION, true );

			$options_page_settings = array(
				'please_wait_processing'	=> __( 'Please wait, processing the previous request', 'cherry-projects' ),
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
