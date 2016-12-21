<?php
/**
 * Plugin Name: Cherry Projects
 * Plugin URI:  http://www.cherryframework.com/
 * Description: A projects plugin for WordPress.
 * Version:     1.2.0
 * Author:      Cherry Team
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-projects
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class 'Cherry_Projects' not exists.
if ( ! class_exists( 'Cherry_Projects' ) ) {

	/**
	 * Sets up and initializes the Cherry Projects plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Projects {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Created options fields
		 * @var array
		 */
		public static $option_exist_array = array();

		/**
		 * Default options
		 * @var array
		 */
		public $default_options = array();

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $core = null;

		/**
		 * Cherry_Project_Data instance
		 *
		 * @var null
		 */
		public $projects_data = null;

		/**
		 * Cherry_Project_Single instance
		 *
		 * @var null
		 */
		public $projects_single_data = null;

		/**
		 * Cherry_Project_Term_Data instance
		 *
		 * @var null
		 */
		public $projects_term_data = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Set the constants needed by the plugin.
			$this->constants();

			// Load the functions files.
			$this->includes();

			// Load the installer core.
			add_action( 'after_setup_theme', require( CHERRY_PROJECTS_DIR . 'cherry-framework/setup.php' ), 0 );

			// Load the core functions/classes required by the rest of the theme.
			add_action( 'after_setup_theme', array( $this, 'get_core' ), 1 );

			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );

			// Initialization of modules.
			add_action( 'after_setup_theme', array( $this, 'init' ), 10 );

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'lang' ),      2 );

			// Load the admin files.
			add_action( 'plugins_loaded', array( $this, 'admin' ),     3 );

			// Load public-facing style sheet.
			add_action( 'wp_enqueue_scripts',         array( $this, 'enqueue_styles' ) );

			// Load public-facing JavaScript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation'     ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

			add_action( 'admin_init', array( $this, 'create_plugin_options' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		function constants() {

			/**
			 * Set constant name for the post type name.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_PROJECTS_NAME', 'projects' );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_PROJECTS_VERSION', '1.2.0' );

			/**
			 * Set the slug of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_PROJECTS_SLUG', basename( dirname( __FILE__ ) ) );

			/**
			 * Set the name for the 'meta_key' value in the 'wp_postmeta' table.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_PROJECTS_POSTMETA', 'cherry_projects' );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_PROJECTS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_PROJECTS_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			/**
			 * Set constant DB option field.
			 *
			 * @since 1.0.0
			 */
			define( 'OPTIONS_NAME', 'cherry_projects_options' );

		}

		/**
		 * Loads files from the '/inc' folder.
		 *
		 * @since 1.0.0
		 */
		function includes() {
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/aq_resizer.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-registration.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-page-template.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-data.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-term-data.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-single-data.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-shortcode.php' );
			require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'public/includes/class-projects-term-shortcode.php' );
		}

		/**
		 * Loads admin files.
		 *
		 * @since 1.0.0
		 */
		function admin() {
			if ( is_admin() ) {
				require_once( CHERRY_PROJECTS_DIR . 'admin/includes/class-projects-admin.php' );
				require_once( CHERRY_PROJECTS_DIR . 'admin/includes/class-projects-meta-boxes.php' );
				require_once( CHERRY_PROJECTS_DIR . 'admin/includes/class-projects-options-page.php' );
			}
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_styles() {

		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {

		}

		/**
		 * Loads the core functions. These files are needed before loading anything else in the
		 * theme because they have required functions for use.
		 *
		 * @since  1.0.0
		 */
		public function get_core() {
			/**
			 * Fires before loads the core theme functions.
			 *
			 * @since 1.0.0
			 */
			do_action( 'cherry_projects_core_before' );

			global $chery_core_version;

			if ( null !== $this->core ) {
				return $this->core;
			}

			if ( 0 < sizeof( $chery_core_version ) ) {
				$core_paths = array_values( $chery_core_version );

				require_once( $core_paths[0] );
			} else {
				die('Class Cherry_Core not found');
			}

			$this->core = new Cherry_Core( array(
				'base_dir' => CHERRY_PROJECTS_DIR . 'cherry-framework',
				'base_url' => CHERRY_PROJECTS_DIR . 'cherry-framework',
				'modules'  => array(
					'cherry-js-core' => array(
						'autoload' => true,
					),
					'cherry-ui-elements' => array(
						'autoload' => false,
					),
					'cherry-utility' => array(
						'autoload' => false,
					),
					'cherry-term-meta' => array(
						'autoload' => false,
					),
					'cherry-post-meta' => array(
						'autoload' => false,
					),
					'cherry-interface-builder' => array(
						'autoload' => false,
					),
					'cherry-handler' => array(
						'autoload' => false,
					),
					'cherry5-insert-shortcode' => array(
						'autoload' => false,
					),
					'cherry-db-updater' => array(
						'autoload' => true,
						'args'     => array(
							'slug'      => 'cherry-projects',
							'version'   => CHERRY_PROJECTS_VERSION,
							'callbacks' => array(
								CHERRY_PROJECTS_VERSION => array(
									array( $this, 'update_thumbs' ),
								),
							),
						),
					),
				),
			) );

			return $this->core;
		}

		/**
		 * Update thumbnail keys
		 *
		 * @since 1.2.0
		 * @return void
		 */
		public function update_thumbs() {

			$terms = get_terms( 'projects_category', array(
				'hide_empty' => false,
			) );

			if ( empty( $terms ) ) {
				return;
			}

			foreach ( $terms as $term ) {
				$thumb = get_term_meta( $term->term_id, 'cherry_terms_thumbnails', true );

				if ( $thumb ) {
					update_term_meta( $term->term_id, 'cherry_thumb', $thumb );
				}
			}

		}

		/**
		 * Run initialization of modules.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			$this->get_core()->init_module( 'cherry-utility', array(
				'meta_key' => array(
					'term_thumb' => 'cherry_terms_thumbnails',
				),
			) );

			$this->get_core()->init_module( 'cherry-term-meta', array(
				'tax'      => CHERRY_PROJECTS_NAME . '_category',
				'priority' => 10,
				'fields'   => array(
					'cherry_terms_thumbnails' => array(
						'type'               => 'media',
						'value'              => '',
						'multi_upload'       => false,
						'library_type'       => 'image',
						'upload_button_text' => esc_html__( 'Set thumbnail', 'cherry_projects' ),
						'label'              => esc_html__( 'Category thumbnail', 'cherry_projects' ),
					),
				),
			) );

			$this->get_core()->init_module( 'cherry-term-meta', array(
				'tax'      => CHERRY_PROJECTS_NAME . '_tag',
				'priority' => 10,
				'fields'   => array(
					'cherry_terms_thumbnails' => array(
						'type'               => 'media',
						'value'              => '',
						'multi_upload'       => false,
						'library_type'       => 'image',
						'upload_button_text' => esc_html__( 'Set thumbnail', 'cherry_projects' ),
						'label'              => esc_html__( 'Tag thumbnail', 'cherry_projects' ),
					),
				),
			) );

			$this->projects_data = new Cherry_Project_Data();
			$this->projects_single_data = new Cherry_Project_Single_Data();
			$this->projects_term_data = new Cherry_Project_Term_Data();
		}

		/**
		 * Create pluginoptions
		 *
		 * @since 1.0.0
		 */
		public function create_plugin_options() {

			$this->default_options = array(
				'listing-layout'               => 'grid-layout',
				'loading-mode'                 => 'ajax-pagination-mode',
				'loading-animation'            => 'loading-animation-move-up',
				'hover-animation'              => 'simple-scale',
				'filter-visible'               => 'true',
				'filter-type'                  => 'category',
				'category-list'                => array(),
				'tags-list'                    => array(),
				'order-filter-visible'         => 'false',
				'order-filter-default-value'   => 'desc',
				'orderby-filter-default-value' => 'date',
				'posts-format'                 => 'post-format-all',
				'column-number'                => 3,
				'post-per-page'                => 9,
				'item-margin'                  => 4,
				'justified-fixed-height'       => 300,
				'masonry-template'             => 'masonry-default.tmpl',
				'grid-template'                => 'grid-default.tmpl',
				'justified-template'           => 'justified-default.tmpl',
				'cascading-grid-template'      => 'cascading-grid-default.tmpl',
				'list-template'                => 'list-default.tmpl',
				'standard-post-template'       => 'standard-post-template.tmpl',
				'image-post-template'          => 'image-post-template.tmpl',
				'gallery-post-template'        => 'gallery-post-template.tmpl',
				'audio-post-template'          => 'audio-post-template.tmpl',
				'video-post-template'          => 'video-post-template.tmpl',
			);

			if ( ! self::is_db_option_exist( OPTIONS_NAME ) ) {
				$this->save_options( OPTIONS_NAME, $this->default_options );
			}

			if ( ! self::is_db_option_exist( OPTIONS_NAME . '_default' ) ) {
				$this->save_options( OPTIONS_NAME . '_default', $this->default_options );
			}
		}

		/**
		 *
		 *
		 * @since 1.0.0
		 */
		public static function is_db_option_exist( $option_name ) {

			( false == get_option( $option_name ) ) ? $is_exist = false : $is_exist = true;

			self::$option_exist_array[] = $option_name;

			return $is_exist;
		}

		/**
		 *
		 * Save options to DB
		 *
		 * @since 1.0.0
		 */
		public function save_options( $option_name, $options ) {

			$options = array_merge( $this->default_options, $options );
			update_option( $option_name, $options );
		}

		/**
		 *
		 * Get option value
		 *
		 * @since 1.0.0
		 */
		public function get_option( $option_name, $option_default = false ) {

			$cached = wp_cache_get( $option_name, OPTIONS_NAME );

			if ( $cached ) {
				return $cached;
			}

			if ( self::is_db_option_exist( OPTIONS_NAME ) ) {
				$current_options = get_option( OPTIONS_NAME );

				if ( array_key_exists( $option_name, $current_options ) ) {
					wp_cache_set( $option_name, $current_options[ $option_name ], OPTIONS_NAME );

					return $current_options[ $option_name ];
				}
			} else {
				$default_options = $this->default_options;

				if ( array_key_exists( $option_name, $default_options ) ) {
					wp_cache_set( $option_name, $default_options[ $option_name ], OPTIONS_NAME );

					return $default_options[ $option_name ];
				}
			}

			wp_cache_set( $option_name, $option_default, OPTIONS_NAME );

			return $option_default;
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'cherry_projects_template_path', 'cherry-projects/' );
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		function lang() {
			load_plugin_textdomain( 'cherry-projects', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * On plugin activation.
		 *
		 * @since 1.0.0
		 */
		function activation() {
			Cherry_Projects_Registration::register();
			Cherry_Projects_Registration::register_taxonomy();

			flush_rewrite_rules();
		}

		/**
		 * On plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		function deactivation() {
			flush_rewrite_rules();
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

	/**
	 * Returns instanse of main theme configuration class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cherry_projects() {
		return Cherry_Projects::get_instance();
	}

	cherry_projects();

}
