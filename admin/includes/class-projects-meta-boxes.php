<?php
/**
 * Handles custom post meta boxes for the projects post type.
 *
 * @package   Cherry_Projects
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

class Cherry_Projects_Meta_Boxes {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * [$metabox_format description]
	 * @var null
	 */
	public $metabox_format = null;

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Initialization of modules.
		add_action( 'after_setup_theme', array( $this, 'init' ), 10 );
	}

	/**
	 * Run initialization of modules.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		$prefix = CHERRY_PROJECTS_POSTMETA;
		cherry_projects()->get_core()->init_module( 'cherry-post-meta', array(
			'id'			=> 'post-layout',
			'title'			=> esc_html__( 'Format Options', '__tm' ),
			'page'			=> array( CHERRY_PROJECTS_NAME ),
			'context'		=> 'normal',
			'priority'		=> 'high',
			'callback_args'	=> false,
			'fields'		=> array(
				CHERRY_PROJECTS_POSTMETA . '[image][crop_image]' => array(
					'type'			=> 'switcher',
					'label'			=> esc_html__( 'Crop image', 'cherry-projects' ),
					'value'			=> 'true',
					'toggle'		=> array(
						'true_toggle'	=> __( 'Yes', 'cherry-projects' ),
						'false_toggle'	=> __( 'No', 'cherry-projects' )
					)
				),
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
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Cherry_Projects_Meta_Boxes::get_instance();