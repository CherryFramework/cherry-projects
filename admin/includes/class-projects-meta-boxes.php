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
			'id'			=> 'projects-settings',
			'title'			=> esc_html__( 'Projects settings', '__tm' ),
			'page'			=> array( CHERRY_PROJECTS_NAME ),
			'context'		=> 'normal',
			'priority'		=> 'high',
			'callback_args'	=> false,
			'fields'		=> array(
				$prefix . '_details' => array(
					'type'        => 'repeater',
					'label'       => esc_html__( 'Projects Details', 'cherry-projects' ),
					'add_label'   => esc_html__( 'Add Projects Details', 'cherry-projects' ),
					'title_field' => 'detail_label',
					'fields'      => array(
						'detail_label'       => array(
							'type'        => 'text',
							'id'          => 'detail_label',
							'name'        => 'detail_label',
							'placeholder' => esc_html__( 'Enter label', 'cherry-projects' ),
							'label'       => esc_html__( 'Detail Label', 'cherry-projects' ),
						),
						'detail_info'         => array(
							'type'        => 'text',
							'id'          => 'detail_info',
							'name'        => 'detail_info',
							'placeholder' => esc_html__( 'Enter info', 'cherry-projects' ),
							'label'       => esc_html__( 'Detail Info', 'cherry-projects' ),
						),
					),
				),
			),
		) );

		cherry_projects()->get_core()->init_module( 'cherry-post-meta', array(
			'id'			=> 'post-layout',
			'title'			=> esc_html__( 'Format Options', '__tm' ),
			'page'			=> array( CHERRY_PROJECTS_NAME ),
			'context'		=> 'normal',
			'priority'		=> 'high',
			'callback_args'	=> false,
			'fields'		=> array(
				$prefix . '_skills' => array(
					'type'        => 'repeater',
					'label'       => esc_html__( 'Projects skills', 'cherry-projects' ),
					'add_label'   => esc_html__( 'Add Skill', 'cherry-projects' ),
					'title_field' => 'detail_label',
					'fields'      => array(
						'skill_label'     => array(
							'type'        => 'text',
							'id'          => 'skill_label',
							'name'        => 'skill_label',
							'placeholder' => esc_html__( 'Skill label', 'cherry-projects' ),
							'label'       => esc_html__( 'Skill Label', 'cherry-projects' ),
						),
						'detail_info'         => array(
							'type'        => 'slider',
							'id'          => 'skill_label',
							'name'        => 'skill_label',
							'label'       => esc_html__( 'Skill Value', 'cherry-projects' ),
						),
					),
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