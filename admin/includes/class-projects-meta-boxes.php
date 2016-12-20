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
		// Metaboxes rendering.
		add_action( 'load-post.php',     array( $this, 'init' ), 10 );
		add_action( 'load-post-new.php', array( $this, 'init' ), 10 );
	}

	/**
	 * Run initialization of modules.
	 *
	 * @since 1.1.0
	 */
	public function init() {
		$prefix = CHERRY_PROJECTS_POSTMETA;

		/**
		 * Metabox fields settings filtering
		 *
		 * @since 1.2.0
		 * @var array
		 */
		$meta_settings = apply_filters(
			'cherry_projects_metabox_fields_settings',
			array(
				'id'            => 'projects-settings',
				'title'         => esc_html__( 'Popup settings', 'cherry-projects' ),
				'page'          => array( CHERRY_PROJECTS_NAME ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'fields'        => array(
					'project_meta_data' => array(
						'type'    => 'settings',
						'element' => 'settings',
					),
					'tab_vertical' => array(
						'type'    => 'component-tab-horizontal',
						'element' => 'component',
						'parent'  => 'project_meta_data',
						'class'   => 'cherry-project-tabs-wrapper',
					),
					'general_tab' => array(
						'element'     => 'settings',
						'parent'      => 'tab_vertical',
						'title'       => esc_html__( 'General', 'cherry-projects' ),
						'description' => esc_html__( 'General project settings', 'cherry-projects' ),
					),
					'image_format_tab' => array(
						'element'     => 'settings',
						'parent'      => 'tab_vertical',
						'title'       => esc_html__( 'Image Format', 'cherry-projects' ),
						'description' => esc_html__( 'Project options for Image format', 'cherry-projects' ),
					),
					'gallery_format_tab' => array(
						'element'     => 'settings',
						'parent'      => 'tab_vertical',
						'title'       => esc_html__( 'Gallery Format', 'cherry-projects' ),
						'description' => esc_html__( 'Project options for Gallery format', 'cherry-projects' ),
					),
					'audio_format_tab' => array(
						'element'     => 'settings',
						'parent'      => 'tab_vertical',
						'title'       => esc_html__( 'Audio Format', 'cherry-projects' ),
						'description' => esc_html__( 'Project options for Audio format', 'cherry-projects' ),
					),
					'video_format_tab' => array(
						'element'     => 'settings',
						'parent'      => 'tab_vertical',
						'title'       => esc_html__( 'Video Format', 'cherry-projects' ),
						'description' => esc_html__( 'Project options for Video format', 'cherry-projects' ),
					),
					$prefix . '_external_link' => array(
						'type'        => 'text',
						'parent'      => 'general_tab',
						'title'       => esc_html__( 'External link', 'cherry-projects' ),
						'description' => esc_html__( 'Input external link address', 'cherry-projects' ),
						'value'       => '#',
					),
					$prefix . '_external_link_text' => array(
						'type'        => 'text',
						'parent'      => 'general_tab',
						'title'       => esc_html__( 'External link text', 'cherry-projects' ),
						'description' => esc_html__( 'Text for external link', 'cherry-projects' ),
						'value'       => '',
					),
					$prefix . '_external_link_target' => array(
						'type'          => 'radio',
						'parent'        => 'general_tab',
						'title'         => esc_html__( 'External link target', 'cherry-projects' ),
						'description'   => esc_html__( 'Target for external link', 'cherry-projects' ),
						'value'         => 'blank',
						'display-input' => true,
						'options'       => array(
							'blank' => array(
								'label' => esc_html__( 'Blank', 'cherry-projects' ),
							),
							'self' => array(
								'label' => esc_html__( 'Self', 'cherry-projects' ),
							),
						),
					),
					$prefix . '_details' => array(
						'type'        => 'repeater',
						'parent'      => 'general_tab',
						'title'       => esc_html__( 'Projects Details', 'cherry-projects' ),
						'description' => esc_html__( 'Here you can create a list of project details', 'cherry-projects' ),
						'add_label'   => esc_html__( 'Add Projects Details', 'cherry-projects' ),
						'title_field' => 'detail_label',
						'fields'      => array(
							'detail_label'    => array(
								'type'        => 'text',
								'id'          => 'detail_label',
								'name'        => 'detail_label',
								'placeholder' => esc_html__( 'Enter label', 'cherry-projects' ),
								'label'       => esc_html__( 'Detail Label', 'cherry-projects' ),
							),
							'detail_info'     => array(
								'type'        => 'text',
								'id'          => 'detail_info',
								'name'        => 'detail_info',
								'placeholder' => esc_html__( 'Enter info', 'cherry-projects' ),
								'label'       => esc_html__( 'Detail Info', 'cherry-projects' ),
							),
						),
					),
					$prefix . '_skills' => array(
						'type'        => 'repeater',
						'parent'      => 'general_tab',
						'title'       => esc_html__( 'Projects skills', 'cherry-projects' ),
						'description' => esc_html__( 'Here you can create a list of participants in the creation of the project', 'cherry-projects' ),
						'add_label'   => esc_html__( 'Add Skill', 'cherry-projects' ),
						'title_field' => 'skill_label',
						'fields'      => array(
							'skill_label'     => array(
								'type'        => 'text',
								'id'          => 'skill_label',
								'name'        => 'skill_label',
								'placeholder' => esc_html__( 'Skill label', 'cherry-projects' ),
								'label'       => esc_html__( 'Skill Label', 'cherry-projects' ),
							),
							'skill_value'         => array(
								'type'        => 'slider',
								'id'          => 'skill_value',
								'name'        => 'skill_value',
								'label'       => esc_html__( 'Skill Value', 'cherry-projects' ),
							),
						),
					),
					$prefix . '_image_attachments_ids' => array(
						'type'               => 'media',
						'parent'             => 'image_format_tab',
						'title'              => esc_html__( 'Additional images', 'cherry-projects' ),
						'description'        => esc_html__( 'Select attachments images', 'cherry-projects' ),
						'display_image'      => true,
						'multi_upload'       => true,
						'upload_button_text' => __( 'Add images', 'cherry-projects' ),
						'library_type'       => 'image',
					),
					$prefix . '_listing_layout' => array(
						'type'          => 'radio',
						'parent'        => 'image_format_tab',
						'title'         => esc_html__( 'Image listing layout', 'cherry-projects' ),
						'description'   => esc_html__( 'Select listing layout', 'cherry-projects' ),
						'value'         => 'grid-layout',
						'class'         => '',
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
						),
					),
					$prefix . '_column_number' => array(
						'type'        => 'slider',
						'parent'      => 'image_format_tab',
						'title'       => esc_html__( 'Column number', 'cherry-projects' ),
						'description' => esc_html__( 'Select number of columns for masonry and grid projects layouts.', 'cherry-projects' ),
						'max_value'   => 10,
						'min_value'   => 1,
						'value'       => 3,
					),
					$prefix . '_image_margin' => array(
						'type'        => 'slider',
						'parent'      => 'image_format_tab',
						'title'       => esc_html__( 'Image margin', 'cherry-projects' ),
						'description' => esc_html__( 'Select projects item margin (outer indent) value.(px)', 'cherry-projects' ),
						'max_value'   => 50,
						'min_value'   => 0,
						'value'       => 10,
					),
					$prefix . '_slider_attachments_ids' => array(
						'type'               => 'media',
						'parent'             => 'gallery_format_tab',
						'title'              => esc_html__( 'Gallery images', 'cherry-projects' ),
						'description'        => esc_html__( 'Select gallery images', 'cherry-projects' ),
						'display_image'      => true,
						'multi_upload'       => true,
						'upload_button_text' => __( 'Add images', 'cherry-projects' ),
						'library_type'       => 'image',
					),
					$prefix . '_slider_navigation' => array(
						'type'        => 'switcher',
						'parent'      => 'gallery_format_tab',
						'value'       => 'true',
						'title'       => esc_html__( 'Use navigation?', 'cherry-projects' ),
						'description' => esc_html__( 'Set the value to true if you want to use navigation', 'cherry-projects' ),
					),
					$prefix . '_slider_loop' => array(
						'type'        => 'switcher',
						'parent'      => 'gallery_format_tab',
						'value'       => 'true',
						'title'       => esc_html__( 'Use infinite scrolling?', 'cherry-projects' ),
						'description' => esc_html__( 'Set the value to true if you want to use infinite scrolling', 'cherry-projects' ),
					),
					$prefix . '_slider_thumbnails_position' => array(
						'type'          => 'radio',
						'parent'        => 'gallery_format_tab',
						'title'         => esc_html__( 'Thumbnails position', 'cherry-projects' ),
						'description'   => esc_html__( 'Select position for Thumbnails list', 'cherry-projects' ),
						'value'         => 'bottom',
						'display-input' => true,
						'options'       => array(
							'top' => array(
								'label' => esc_html__( 'Top', 'cherry-projects' ),
							),
							'bottom' => array(
								'label' => esc_html__( 'Bottom', 'cherry-projects' ),
							),
							'right' => array(
								'label' => esc_html__( 'Right', 'cherry-projects' ),
							),
							'left' => array(
								'label' => esc_html__( 'Left', 'cherry-projects' ),
							),
						),
					),
					$prefix . '_audio_attachments_ids' => array(
						'type'               => 'media',
						'parent'             => 'audio_format_tab',
						'title'              => esc_html__( 'Audio source', 'cherry-projects' ),
						'description'        => esc_html__( 'Select audio source( mp3, m4a, ogg, wav, wma )', 'cherry-projects' ),
						'display_image'      => true,
						'multi_upload'       => true,
						'upload_button_text' => esc_html__( 'Add sound', 'cherry-projects' ),
						'library_type'       => 'audio',
					),
					$prefix . '_video_list' => array(
						'type'        => 'repeater',
						'parent'      => 'video_format_tab',
						'title'       => esc_html__( 'Video list', 'cherry-projects' ),
						'description' => esc_html__( 'Select video source', 'cherry-projects' ),
						'add_label'   => esc_html__( 'Add New Video', 'cherry-projects' ),
						'title_field' => 'detail_label',
						'fields'      => array(
							'video_type' => array(
								'type'          => 'radio',
								'label'         => esc_html__( 'Video source type', 'cherry-projects' ),
								'id'            => 'video_type',
								'name'          => 'video_type',
								'display-input' => true,
								'options'       => array(
									'embed' => array(
										'label' => esc_html__( 'Embed video type', 'cherry-projects' ),
									),
									'html5' => array(
										'label' => esc_html__( 'HTML5 video type', 'cherry-projects' ),
									),
								),
							),
							'video_embed'     => array(
								'type'        => 'text',
								'id'          => 'video_embed',
								'name'        => 'video_embed',
								'placeholder' => esc_html__( 'Select embed url', 'cherry-projects' ),
								'label'       => esc_html__( 'Video embed url', 'cherry-projects' ),
							),
							'video_src' => array(
								'type'               => 'media',
								'id'                 => 'video_src',
								'name'               => 'video_src',
								'label'              => esc_html__( 'HTML5 Video source', 'cherry-projects' ),
								'display_image'      => true,
								'multi_upload'       => false,
								'upload_button_text' => esc_html__( 'Add Video', 'cherry-projects' ),
								'library_type'       => 'video',
							),
							'poster_src' => array(
								'type'               => 'media',
								'id'                 => 'poster_src',
								'name'               => 'poster_src',
								'label'              => esc_html__( 'HTML5 video poster', 'cherry-projects' ),
								'display_image'      => true,
								'multi_upload'       => false,
								'upload_button_text' => esc_html__( 'Add Poster', 'cherry-projects' ),
								'library_type'       => 'image',
							),
						),
					),
				)
			)
		);

		cherry_projects()->get_core()->init_module( 'cherry-post-meta', $meta_settings);
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
