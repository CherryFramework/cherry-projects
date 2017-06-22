<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Cherry_Projects_Elementor_Compat' ) ) {

	/**
	 * Define Cherry_Projects_Elementor_Compat class
	 */
	class Cherry_Projects_Elementor_Compat {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Registered shortcodes array
		 *
		 * @var array
		 */
		public $shortcodes = array();

		/**
		 * Constructor for the class
		 */
		function __construct( $shortcodes ) {

			$this->shortcodes = $shortcodes;

			add_action( 'elementor/init', array( $this, 'register_category' ) );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );

		}

		/**
		 * Register elementor widget
		 *
		 * @return void
		 */
		public function register_widgets( $widgets_manager ) {

			foreach ( $this->shortcodes as $data ) {
				require $data['file'];
				unset( $data['file'] );
				$widgets_manager->register_widget_type( call_user_func( array( $data['class'], 'get_instance' ) ) );
			}

		}

		/**
		 * Register cherry category for elementor if not exists
		 *
		 * @return void
		 */
		public function register_category() {

			$elements_manager = Elementor\Plugin::instance()->elements_manager;
			$existing         = $elements_manager->get_categories();
			$cherry_cat       = 'cherry';

			if ( array_key_exists( $cherry_cat, $existing ) ) {
				return;
			}

			$elements_manager->add_category(
				$cherry_cat,
				array(
					'title' => esc_html__( 'Cherry Addons', 'cherry-projects' ),
					'icon'  => 'font',
				),
				1
			);
		}

		/**
		 * Register shortcode function
		 *
		 * @return void
		 */
		public function add_shortcode( $shortcode_slug, $shortcode_data = array() ) {
			$this->shortcodes[ $shortcode_slug ] = $shortcode_data;
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;
		}
	}

}

/**
 * Returns instance of Cherry_Projects_Elementor_Compat
 *
 * @return object
 */
function cherry_projects_elementor_compat( $shortcodes = array() ) {
	return Cherry_Projects_Elementor_Compat::get_instance( $shortcodes );
}
