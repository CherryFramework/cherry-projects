<?php
/**
 * Handles custom post meta boxes for the 'testimonial' post type.
 *
 * @package   Cherry_Testimonials_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

class Cherry_Portfolio_Meta_Boxes {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;
	public $metabox_format = null;

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'add_meta_boxes_' . CHERRY_PORTFOLIO_NAME, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post',      array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Adds the meta box container.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		$post_id = get_the_ID();
		$format = get_post_format( $post_id );
		$format = (empty( $format )) ? 'standart' : $format;
		/**
		 * Filter the array of 'add_meta_box' parametrs.
		 *
		 * @since 1.0.0
		 */
		$metabox_post_settings = apply_filters( 'cherry_portfolio_post_settings_metabox_params', array(
			'id'            => 'cherry-portfolio-post-settings-options',
			'title'         => __( 'Post settings', 'cherry-portfolio' ),
			'post_type'     => CHERRY_PORTFOLIO_NAME,
			'context'       => 'normal',
			'priority'      => 'high',
			'callback_args' => array(
				array(
					'id'			=> 'external-link-url',
					'type'			=> 'text',
					'label'			=> __( 'External-link:', 'cherry-portfolio' ),
					'description'	=> __( 'Enter an external link', 'cherry-portfolio' ),
					'value'			=> '',
				),
				array(
					'id'			=> 'external-link-text',
					'type'			=> 'text',
					'label'			=> __( 'Link text:', 'cherry-portfolio' ),
					'description'	=> __( "Enter link text.", 'cherry-portfolio' ),
					'value'			=> '',
				),
				array(
					'id'			=> 'external-link-target',
					'type'			=> 'radio',
					'label'			=> __('Link target', 'cherry-portfolio'),
					'description'	=> __('Choose link target', 'cherry-portfolio'),
					'value'			=> '_blank',
					'options'		=> array(
						'_self' => array(
							'label' => __('Self', 'cherry-portfolio'),
							'img_src' => ''
						),
						'_blank' => array(
							'label' => __('Blank', 'cherry-portfolio'),
							'img_src' => ''
						),
					)
				),
			)
		));

		add_meta_box(
			$metabox_post_settings['id'],
			$metabox_post_settings['title'],
			array( $this, 'callback_metabox' ),
			$metabox_post_settings['post_type'],
			$metabox_post_settings['context'],
			$metabox_post_settings['priority'],
			$metabox_post_settings['callback_args']
		);


		// post format settings
		$post_format_settings = $this->format_settings( $format );

		$this->metabox_format = apply_filters( 'cherry_portfolio_metabox_params', array(
			'id'            => 'cherry-portfolio-post-format-options',
			'title'         => __( 'Post format options', 'cherry-portfolio' ),
			'post_type'     => CHERRY_PORTFOLIO_NAME,
			'context'       => 'normal',
			'priority'      => 'high',
			'callback_args' => $post_format_settings
			)
		);

		/**
		 * Add meta box to the administrative interface.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 */
		add_meta_box(
			$this->metabox_format['id'],
			$this->metabox_format['title'],
			array( $this, 'callback_metabox' ),
			$this->metabox_format['post_type'],
			$this->metabox_format['context'],
			$this->metabox_format['priority'],
			$this->metabox_format['callback_args']
		);
	}

	/**
	 * Prints the box content.
	 *
	 * @since 1.0.0
	 * @param object $post    Current post object.
	 * @param array  $metabox
	 */
	public function callback_metabox( $post, $metabox ) {
		$output = '';
		// Add an nonce field so we can check for it later.
		wp_nonce_field( plugin_basename( __FILE__ ), 'cherry_portfolio_options_meta_nonce' );
		foreach ( $metabox['args'] as $settings ) :
			// Get current post meta data.
			$post_meta  = get_post_meta( $post->ID, CHERRY_PORTFOLIO_POSTMETA, true );
			$format = get_post_format( $post->ID );
			$format = (empty( $format )) ? 'standart' : $format;

			if ( !empty( $post_meta ) && isset( $post_meta[ $settings['id'] ] ) ) {
				$field_value = $post_meta[ $settings['id'] ];
			} else {
				$field_value = $settings['value'];
			}
			$settings['value'] = $field_value;

			$builder = new Cherry_Interface_Builder( array(
				'name_prefix'	=> CHERRY_PORTFOLIO_POSTMETA,
				'pattern'		=> 'inline',
				'class'			=> array( 'section' => 'single-section' ),
			) );
			$output .= $builder->add_form_item( $settings );
		endforeach;
		printf( '<div class="%1$s cherry-ui-core">%2$s</div>', 'settings-item '.$format.'-post-format-settings', $output );
	}

	public function format_settings( $format = 'standart' ) {
		$post_format_settings = array();
		switch ($format) {
			case 'standart':
				/**
				 * Filter base settings for standart format options.
				 *
				 * @since 1.0.0
				 * @param array with base settings for standart format options.
				 */
				$post_format_settings = apply_filters( 'cherry-portfolio-standart-format-settings', array(
						array(
							'id'			=> 'portfolio-standart-zoom-image',
							'type'			=> 'switcher',
							'label'			=> __('Zoom image', 'cherry-portfolio'),
							'description'	=> __('Using zooming image', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Yes', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'No', 'cherry-portfolio' )
							)
						),
					)
				);
				break;
			case 'image':
				/**
				 * Filter base settings for image format options.
				 *
				 * @since 1.0.0
				 * @param array with base settings for image format options.
				 */
				$post_format_settings = apply_filters( 'cherry-portfolio-image-format-settings', array(
						array(
							'id'			=> 'portfolio-image-format-crop-image',
							'type'			=> 'switcher',
							'label'			=> __('Crop image', 'cherry-portfolio'),
							'description'	=> __('Using cropped image', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Yes', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'No', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-image-format-crop-width',
							'type'			=> 'slider',
							'label'			=> __('Cropping image width', 'cherry-portfolio'),
							'description'	=> __('Width value', 'cherry-portfolio'),
							'value'			=> 1024,
							'max_value'		=> 3840,
							'min_value'		=> 100,
						),
						array(
							'id'			=> 'portfolio-image-format-crop-height',
							'type'			=> 'slider',
							'label'			=> __('Cropping image height', 'cherry-portfolio'),
							'description'	=> __('Height value', 'cherry-portfolio'),
							'value'			=> 576,
							'max_value'		=> 2160,
							'min_value'		=> 100,
						),
					)
				);
				break;
			case 'gallery':
				/**
				 * Filter base settings for gallery format options.
				 *
				 * @since 1.0.0
				 * @param array with base settings for gallery format options.
				 */
				$post_format_settings = apply_filters( 'cherry-portfolio-gallery-format-settings', array(
						array(
							'id'				=> 'portfolio-gallery-attachments-ids',
							'type'				=> 'media',
							'label'				=> __('Slider images', 'cherry-portfolio'),
							'description'		=> __('Select attachments images for slider', 'cherry-portfolio'),
							'value'				=> '',
							'display_image'		=> true,
							'multi_upload'		=> true,
							'library_type'		=> 'image'
						),
						array(
							'id'			=> 'portfolio-gallery-type',
							'type'			=> 'radio',
							'label'			=> __('Gallery type', 'cherry-portfolio'),
							'description'	=> __('Select gallery type', 'cherry-portfolio'),
							'value'			=> 'slider',
							'display_input'	=> false,
							'options'		=> array(
								'slider' => array(
									'label' => __('Slider', 'cherry-portfolio'),
									'img_src' => CHERRY_PORTFOLIO_URI.'/admin/assets/images/svg/slider.svg'
								),
								'masonry' => array(
									'label' => __('Masonry', 'cherry-portfolio'),
									'img_src' => CHERRY_PORTFOLIO_URI.'/admin/assets/images/svg/list-layout-masonry.svg'
								),
								'justified' => array(
									'label' => __('Justified', 'cherry-portfolio'),
									'img_src' => CHERRY_PORTFOLIO_URI.'/admin/assets/images/svg/list-layout-justified.svg'
								),
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-effect',
							'type'			=> 'radio',
							'label'			=> __('Slider effect Layout', 'cherry-portfolio'),
							'description'	=> __('Could be "slide", "fade", "cube" or "coverflow"', 'cherry-portfolio'),
							'value'			=> 'swiper-effect-slide',
							'display_input'	=> false,
							'options'		=> array(
								'swiper-effect-slide' => array(
									'label' => __('Slide', 'cherry-portfolio'),
									'img_src' => CHERRY_PORTFOLIO_URI.'/admin/assets/images/svg/inherit.svg'
								),
								'swiper-effect-cube' => array(
									'label' => __('Cube', 'cherry-portfolio'),
									'img_src' => CHERRY_PORTFOLIO_URI.'/admin/assets/images/svg/inherit.svg'
								),
								'swiper-effect-coverflow' => array(
									'label' => __('Coverflow', 'cherry-portfolio'),
									'img_src' => CHERRY_PORTFOLIO_URI.'/admin/assets/images/svg/inherit.svg'
								),
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-slides-per-view',
							'type'			=> 'slider',
							'label'			=> __('Number of slides per view', 'cherry-portfolio'),
							'description'	=> __('Number of slides per view', 'cherry-portfolio'),
							'value'			=> 1,
							'max_value'		=> 10,
							'min_value'		=> 1,
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-space-between',
							'type'			=> 'slider',
							'label'			=> __('Space Between Slides', 'cherry-portfolio'),
							'description'	=> __('Width of the space between slides(px)', 'cherry-portfolio'),
							'value'			=> 10,
							'max_value'		=> 100,
							'min_value'		=> 0,
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-slides-per-column',
							'type'			=> 'slider',
							'label'			=> __('Multi Row Slides Layout', 'cherry-portfolio'),
							'description'	=> __('Number of slides per column', 'cherry-portfolio'),
							'value'			=> 1,
							'max_value'		=> 5,
							'min_value'		=> 1,
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-pagination',
							'type'			=> 'switcher',
							'label'			=> __('Slider pagination', 'cherry-portfolio'),
							'description'	=> __('Displaying slider pagination', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Enabled', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'Disabled', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-navigation',
							'type'			=> 'switcher',
							'label'			=> __('Slider navigation', 'cherry-portfolio'),
							'description'	=> __('Displaying slider navigation', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Enabled', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'Disabled', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-loop',
							'type'			=> 'switcher',
							'label'			=> __('Slider Infinite Loop', 'cherry-portfolio'),
							'description'	=> __('Slider Loop Mode', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Yes', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'No', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-mouse-wheel',
							'type'			=> 'switcher',
							'label'			=> __('Mousewheel Control', 'cherry-portfolio'),
							'description'	=> __('Mousewheel Control Mode', 'cherry-portfolio'),
							'value'			=> 'false',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Yes', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'No', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-duration-speed',
							'type'			=> 'slider',
							'label'			=> __('Duration of transition', 'cherry-portfolio'),
							'description'	=> __('Duration of transition between slides (in ms)', 'cherry-portfolio'),
							'value'			=> 300,
							'max_value'		=> 5000,
							'min_value'		=> 100,
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-free-mode',
							'type'			=> 'switcher',
							'label'			=> __('Free Mode sliding', 'cherry-portfolio'),
							'description'	=> __('No fixed positions for slides', 'cherry-portfolio'),
							'value'			=> 'false',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Enabled', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'Disabled', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-grab-cursor',
							'type'			=> 'switcher',
							'label'			=> __('Grab Cursor', 'cherry-portfolio'),
							'description'	=> __('Using Grab Cursor for slider', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Enabled', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'Disabled', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-crop-image',
							'type'			=> 'switcher',
							'label'			=> __('Crop image', 'cherry-portfolio'),
							'description'	=> __('Using cropped image', 'cherry-portfolio'),
							'value'			=> 'true',
							'toggle'		=> array(
								'true_toggle'	=> __( 'Yes', 'cherry-portfolio' ),
								'false_toggle'	=> __( 'No', 'cherry-portfolio' )
							)
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-crop-width',
							'type'			=> 'slider',
							'label'			=> __('Cropping image width', 'cherry-portfolio'),
							'description'	=> __('Width value', 'cherry-portfolio'),
							'value'			=> 1024,
							'max_value'		=> 3840,
							'min_value'		=> 100,
						),
						array(
							'id'			=> 'portfolio-gallery-swiper-crop-height',
							'type'			=> 'slider',
							'label'			=> __('Cropping image height', 'cherry-portfolio'),
							'description'	=> __('Height value', 'cherry-portfolio'),
							'value'			=> 576,
							'max_value'		=> 2160,
							'min_value'		=> 100,
						),

					)
				);
				break;
			case 'audio':
				/**
				 * Filter base settings for audio format options.
				 *
				 * @since 1.0.0
				 * @param array with base settings for audio format options.
				 */
				$post_format_settings = apply_filters( 'cherry-portfolio-audio-format-settings', array(
						array(
							'id'				=> 'portfolio-audio-src',
							'type'				=> 'media',
							'label'				=> __('Audio source', 'cherry-portfolio'),
							'description'		=> __('Enter audio source( mp3, m4a, ogg, wav, wma )', 'cherry-portfolio'),
							'value'				=> '',
							'display_image'		=> true,
							'multi_upload'		=> true,
							'library_type'		=> 'audio',
						),
					)
				);
				break;
			case 'video':
				/**
				 * Filter base settings for video format options.
				 *
				 * @since 1.0.0
				 * @param array with base settings for video format options.
				 */
				$post_format_settings = apply_filters( 'cherry-portfolio-video-format-settings', array(
						array(
							'id'			=> 'portfolio-video-type',
							'type'			=> 'radio',
							'label'			=> __('Video type', 'cherry-portfolio'),
							'description'	=> __('Choose video type', 'cherry-portfolio'),
							'value'			=> 'portfolio-video-type-embed',
							'options'		=> array(
								'portfolio-video-type-embed' => array(
									'label' => __('Embed video type', 'cherry-portfolio'),
									'img_src' => '',
									'slave'		=> 'embed-setting-items'
								),
								'portfolio-video-type-html5' => array(
									'label' => __('HTML5 video type', 'cherry-portfolio'),
									'img_src' => '',
									'slave'		=> 'html-setting-items'
								),
							)
						),
						array(
							'id'			=> 'portfolio-embed-video-src',
							'type'			=> 'text',
							'label'			=> __('Embed video source', 'cherry-portfolio'),
							'description'	=> __('Enter source for embed video', 'cherry-portfolio'),
							'value'			=> 'https://www.youtube.com/watch?v=2kodXWejuy0',
							'master'		=> 'embed-setting-items'
						),
						array(
							'id'				=> 'portfolio-mp4-video-id',
							'type'				=> 'media',
							'label'				=> __('MP4 video source', 'cherry-portfolio'),
							'description'		=> __('Enter source for MP4 video', 'cherry-portfolio'),
							'value'				=> '',
							'multi_upload'		=> false,
							'library_type'		=> 'video',
							'master'			=> 'html-setting-items'
						),
						array(
							'id'				=> 'portfolio-webm-video-id',
							'type'				=> 'media',
							'label'				=> __('WEBM video source', 'cherry-portfolio'),
							'description'		=> __('Enter source for WEBM video', 'cherry-portfolio'),
							'value'				=> '',
							'multi_upload'		=> false,
							'library_type'		=> 'video',
							'master'			=> 'html-setting-items'
						),
						array(
							'id'				=> 'portfolio-ogv-video-id',
							'type'				=> 'media',
							'label'				=> __('OGV video source', 'cherry-portfolio'),
							'description'		=> __('Enter source for OGV video', 'cherry-portfolio'),
							'value'				=> '',
							'multi_upload'		=> false,
							'library_type'		=> 'video',
							'master'			=> 'html-setting-items'
						),
					)
				);
			break;
		}
		return $post_format_settings;
	}

	public function format_metabox_builder( $post_id = null, $format = 'standart') {
		$output = '';
		$settings_field = $this->format_settings( $format );

		foreach ( $settings_field as $settings ) :
			// Get current post meta data.
			$post_meta  = get_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, true );

			if ( !empty( $post_meta ) && isset( $post_meta[ $settings['id'] ] ) ) {
				$field_value = $post_meta[ $settings['id'] ];
			} else {
				$field_value = $settings['value'];
			}
			$settings['value'] = $field_value;

			$builder = new Cherry_Interface_Builder( array(
				'name_prefix'	=> CHERRY_PORTFOLIO_POSTMETA,
				'pattern'		=> 'inline',
				'class'			=> array( 'section' => 'single-section' ),
			) );
			$output .= $builder->add_form_item( $settings );
		endforeach;

		printf( '<div class="%1$s cherry-ui-core">%2$s</div>', 'settings-item '.$format.'-post-format-settings', $output );
	}


	/**
	 * Save the meta when the post is saved.
	 *
	 * @since 1.0.0
	 * @param int    $post_id
	 * @param object $post
	 */
	public function save_post( $post_id, $post ) {

		// Verify the nonce.
		if ( !isset( $_POST['cherry_portfolio_options_meta_nonce'] ) || !wp_verify_nonce( $_POST['cherry_portfolio_options_meta_nonce'], plugin_basename( __FILE__ ) ) )
			return;

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
			return $post_id;

		// Don't save if the post is only a revision.
		if ( 'revision' == $post->post_type )
			return;

		// Array of new post meta value.
		$new_meta_value = array();

		// Check if $_POST have a needed key.
		if ( isset( $_POST[ CHERRY_PORTFOLIO_POSTMETA ] ) && !empty( $_POST[ CHERRY_PORTFOLIO_POSTMETA ] ) ) {
			foreach ( $_POST[ CHERRY_PORTFOLIO_POSTMETA ] as $key => $value ) {
				// Sanitize the user input.
				$new_meta_value[ $key ] = sanitize_text_field( $value );
			}
		}

		// Check if nothing found in $_POST array.
		if ( empty( $new_meta_value ) )
			return;

		// Get current post meta data.
		$meta_value = get_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, true );

		// If a new meta value was added and there was no previous value, add it.
		if ( $new_meta_value && '' == $meta_value ){
			add_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, $new_meta_value, true );
		}
		// If the new meta value does not match the old value, update it.
		elseif ( $new_meta_value && $new_meta_value != $meta_value ){
			$new_meta_value = array_merge($meta_value, $new_meta_value);
			update_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, $new_meta_value );
		}
		// If there is no new meta value but an old value exists, delete it.
		elseif ( '' == $new_meta_value && $meta_value ){
			delete_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, $meta_value );
		}
		error_log( var_export( $meta_value, true ) );
		error_log( var_export( $new_meta_value, true ) );
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

//Cherry_Portfolio_Meta_Boxes::get_instance();

