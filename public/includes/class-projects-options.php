<?php
/**
 * portfolio Configuration class.
 *
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

// If class 'Portfolio_Options' not exists.
if ( !class_exists( 'Portfolio_Options' ) ) {

	class Portfolio_Options {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		static $options = array();

		private function __construct() {
			// Cherry option filter.
			add_filter( 'cherry_defaults_settings',      array( $this, 'cherry_portfolio_settings' ) );
			add_filter( 'cherry_layouts_options_list',   array( $this, 'add_cherry_options' ), 11 );
			add_filter( 'cherry_get_single_post_layout', array( $this, 'get_single_option' ),  11, 2 );
		}

		public function get_terms( $tax = 'category', $key = 'id' ) {
			$terms = array();
			/*if ( $key === 'id' ) foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) $terms[$term->term_id] = $term->name;
				elseif ( $key === 'slug' ) foreach ( (array) get_terms( $tax, array( 'hide_empty' => false ) ) as $term ) $terms[$term->slug] = $term->name;
					return $terms;*/
			$_tems = get_terms( $tax, array( 'hide_empty' => false ) );

			if( empty( $_tems ) || is_wp_error( $_tems ) ){
				return $terms;
			}

			if ( $key === 'id' ) {
				foreach ( (array) $_tems as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}elseif ( $key === 'slug' ) {
				foreach ( (array) $_tems as $term ) {
					$terms[ $term->slug ] = $term->name;
				}
			}

			return $terms;
		}

		function cherry_portfolio_settings( $result_array ) {
			$portfolio_options = array();

			$portfolio_options['portfolio-listing-layout'] = array(
				'type'			=> 'radio',
				'title'			=> __('Portfolio listing layout', 'cherry-portfolio'),
				'description'	=> __('Choose portfolio listing view layout.', 'cherry-portfolio'),
				'value'			=> 'masonry-layout',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'masonry-layout' => array(
						'label'		=> __('Masonry', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/list-layout-masonry.svg'
					),
					'grid-layout' => array(
						'label'		=> __('Grid', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/list-layout-grid.svg'
					),
					'justified-layout' => array(
						'label'		=> __('Justified', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/list-layout-justified.svg'
					),
					'list-layout' => array(
						'label'		=> __('List', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/list-layout-listing.svg'
					)
				)
			);
			$portfolio_options['portfolio-loading-mode'] = array(
				'type'			=> 'radio',
				'title'			=> __('Pagination mode', 'cherry-portfolio'),
				'description'	=> __('Choose portfolio pagination mode', 'cherry-portfolio'),
				'value'			=> 'portfolio-ajax-pagination-mode',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'portfolio-ajax-pagination-mode' => array(
						'label'		=> __('Ajax pagination', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/loading-mode-ajax-pagination.svg'
					),
					'portfolio-more-button-mode' => array(
						'label'		=> __('More button', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/loading-mode-ajax-more-button.svg'
					),
				)
			);
			$portfolio_options['portfolio-loading-animation'] = array(
				'type'			=> 'radio',
				'title'			=> __('Loading animation', 'cherry-portfolio'),
				'description'	=> __('Choose posts loading animation', 'cherry-portfolio'),
				'value'			=> 'loading-animation-move-up',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'loading-animation-fade' => array(
						'label'		=> __('Fade animation', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/animation-fade.svg'
					),
					'loading-animation-scale' => array(
						'label'		=> __('Scale animation', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/animation-scale.svg'
					),
					'loading-animation-move-up' => array(
						'label'		=> __('Move Up animation', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/animation-move-up.svg'
					),
					'loading-animation-flip' => array(
						'label'		=> __('Flip animation', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/animation-flip.svg'
					),
					'loading-animation-helix' => array(
						'label'		=> __('Helix animation', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/animation-helix.svg'
					),
					'loading-animation-fall-perspective' => array(
						'label'		=> __('Fall perspective animation', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/animation-fall-perspective.svg'
					),
				)
			);
			$portfolio_options['portfolio-hover-animation'] = array(
				'type'			=> 'radio',
				'title'			=> __('Portfolio hover animation', 'cherry-portfolio'),
				'description'	=> __('Choose posts images hover animation', 'cherry-portfolio'),
				'value'			=> 'simple-scale',
				'class'			=> '',
				'display_input'	=> false,
				'options'	=> array(
					'simple-fade' => array(
						'label'		=> __('Fade', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/inherit.svg'
					),
					'simple-scale' => array(
						'label'		=> __('Scale', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/inherit.svg'
					),
					'custom' => array(
						'label'		=> __('Custom', 'cherry-portfolio'),
						'img_src'	=> CHERRY_PORTFOLIO_URI.'public/assets/images/svg/inherit.svg'
					),
				)
			);
			$portfolio_options['portfolio-filter-type'] = array(
				'type'			=> 'radio',
				'title'			=> 'Filter type',
				'description'	=> __('Select if you want to filter posts by tag or by category.', 'cherry-portfolio'),
				'value'			=> 'portfolio-filter-type-category',
				'display-input'	=> true,
				'options'		=> array(
					'portfolio-filter-type-category' => array(
						'label' => __('Category', 'cherry-portfolio'),
						'slave'		=> 'portfolio-filter-type-category'
					),
					'portfolio-filter-type-tag' => array(
						'label' => __('Tag', 'cherry-portfolio'),
						'slave'		=> 'portfolio-filter-type-tag'
					),
				)
			);
			$portfolio_options['portfolio-category-list'] = array(
				'type'			=> 'select',
				'title'			=> __('Portfolio filter categories list', 'cherry'),
				'label'			=> '',
				'description'	=> '',
				'multiple'		=> true,
				'value'			=> array('select-1','select-2'),
				'class'			=> 'cherry-multi-select',
				'options'		=> $this->get_terms( CHERRY_PORTFOLIO_NAME.'_category', 'slug'),
				'master'		=> 'portfolio-filter-type-category',
			);
			$portfolio_options['portfolio-tags-list'] = array(
				'type'			=> 'select',
				'title'			=> __('Portfolio filter tags list', 'cherry'),
				'label'			=> '',
				'description'	=> '',
				'multiple'		=> true,
				'value'			=> array('select-1','select-2'),
				'class'			=> 'cherry-multi-select',
				'options'		=> $this->get_terms( CHERRY_PORTFOLIO_NAME.'_tag', 'slug'),
				'master'		=> 'portfolio-filter-type-tag',
			);
			$portfolio_options['portfolio-filter-visible'] = array(
				'type'			=> 'switcher',
				'title'			=> __('Filters', 'cherry-portfolio'),
				'description'	=> __('Enable/disable listing filters', 'cherry-portfolio'),
				'value'			=> 'true'
			);
			$portfolio_options['portfolio-order-filter-visible'] = array(
				'type'			=> 'switcher',
				'title'			=> __('Order filters', 'cherry-portfolio'),
				'description'	=> __('Enable/disable order filters', 'cherry-portfolio'),
				'value'			=> 'false'
			);
			$portfolio_options['portfolio-order-filter-default-value'] = array(
				'type'			=> 'radio',
				'title'			=> 'Order filter default value',
				'value'			=> 'desc',
				'display-input'	=> true,
				'options'		=> array(
					'desc' => array(
						'label' => __('DESC', 'cherry-portfolio'),
					),
					'asc' => array(
						'label' => __('ASC', 'cherry-portfolio'),
					),
				)
			);
			$portfolio_options['portfolio-orderby-filter-default-value'] = array(
				'type'			=> 'radio',
				'title'			=> 'Order by filter default value',
				'value'			=> 'date',
				'display-input'	=> true,
				'options'		=> array(
					'date' => array(
						'label' => __('Date', 'cherry-portfolio'),
					),
					'name' => array(
						'label' => __('Name', 'cherry-portfolio'),
					),
					'modified' => array(
						'label' => __('Modified', 'cherry-portfolio'),
					),
					'comment_count' => array(
						'label' => __('Comments', 'cherry-portfolio'),
					),
				)
			);
			$portfolio_options['portfolio-column-number'] = array(
				'type'			=> 'slider',
				'title'			=> __('Column number', 'cherry-portfolio'),
				'description'	=> __('Select number of columns for masonry and grid portfolio layouts. (Min 2, max 20)', 'cherry-portfolio'),
				'max_value'		=> 10,
				'min_value'		=> 2,
				'value'			=> 3
			);
			$portfolio_options['portfolio-post-per-page'] = array(
				'type'			=> 'slider',
				'title'			=> __('Posts per page', 'cherry-portfolio'),
				'description'	=> __('Select how many posts per page do you want to display', 'cherry-portfolio'),
				'max_value'		=> 50,
				'min_value'		=> -1,
				'value'			=> 9
			);
			$portfolio_options['portfolio-item-margin'] = array(
				'type'			=> 'slider',
				'title'			=> __('Item margin', 'cherry-portfolio'),
				'description'	=> __('Select portfolio item margin (outer indent) value.', 'cherry-portfolio'),
				'max_value'		=> 50,
				'min_value'		=> 0,
				'value'			=> 4
			);
			$portfolio_options['portfolio-justified-fixed-height'] = array(
				'type'			=> 'slider',
				'title'			=> __('Justified fixed height', 'cherry-portfolio'),
				'description'	=> __('Select portfolio item justified height value.', 'cherry-portfolio'),
				'max_value'		=> 1000,
				'min_value'		=> 50,
				'value'			=> 300
			);
			$portfolio_options['portfolio-is-crop-image'] = array(
				'type'			=> 'switcher',
				'title'			=> __('Crop image', 'cherry-portfolio'),
				'description'	=> __('Choose if you want to activate images crop.', 'cherry-portfolio'),
				'value'			=> 'false'
			);
			$portfolio_options['portfolio-crop-image-width'] = array(
				'type'			=> 'stepper',
				'title'			=> __('Cropped image width', 'cherry-portfolio'),
				'description'	=> __('Set width of the cropped image.', 'cherry-portfolio'),
				'value'			=> '500',
				'value_step'	=> '1',
				'max_value'		=> '9999',
				'min_value'		=> '10'
			);
			$portfolio_options['portfolio-crop-image-height'] = array(
				'type'			=> 'stepper',
				'title'			=> __('Cropped image height', 'cherry-portfolio'),
				'description'	=> __('Set height of the cropped image.', 'cherry-portfolio'),
				'value'			=> '350',
				'value_step'	=> '1',
				'max_value'		=> '9999',
				'min_value'		=> '10'
			);
			$portfolio_options['portfolio-more-button-text'] = array(
				'type'			=> 'text',
				'title'			=> __('More button text', 'cherry-portfolio'),
				'description'	=> __('Set text for portfolio "read more" buttons.', 'cherry-portfolio'),
				'value'			=> __('Read more', 'cherry-portfolio'),
			);
			$portfolio_options['portfolio-masonry-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Masonry template', 'cherry-portfolio'),
				'description'	=> __('Masonry content template', 'cherry-portfolio'),
				'value'			=> 'masonry-default.tmpl',
			);
			$portfolio_options['portfolio-grid-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Grid template', 'cherry-portfolio'),
				'description'	=> __('Grid content template', 'cherry-portfolio'),
				'value'			=> 'grid-default.tmpl',
			);
			$portfolio_options['portfolio-justified-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Justified template', 'cherry-portfolio'),
				'description'	=> __('Justified content template', 'cherry-portfolio'),
				'value'			=> 'justified-default.tmpl',
			);
			$portfolio_options['portfolio-list-template'] = array(
				'type'			=> 'text',
				'title'			=> __('List template', 'cherry-portfolio'),
				'description'	=> __('List content template', 'cherry-portfolio'),
				'value'			=> 'list-default.tmpl',
			);
			$portfolio_options['portfolio-single-standart-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Standard post template', 'cherry-portfolio'),
				'description'	=> __('Standard post format template content', 'cherry-portfolio'),
				'value'			=> 'post-format-standart-template.tmpl',
			);
			$portfolio_options['portfolio-single-image-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Image post template', 'cherry-portfolio'),
				'description'	=> __('Image post format template content', 'cherry-portfolio'),
				'value'			=> 'post-format-image-template.tmpl',
			);
			$portfolio_options['portfolio-single-gallery-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Gallery post template', 'cherry-portfolio'),
				'description'	=> __('Gallery post format template content', 'cherry-portfolio'),
				'value'			=> 'post-format-gallery-template.tmpl',
			);
			$portfolio_options['portfolio-single-audio-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Audio post template', 'cherry-portfolio'),
				'description'	=> __('Audio post format template content', 'cherry-portfolio'),
				'value'			=> 'post-format-audio-template.tmpl',
			);
			$portfolio_options['portfolio-single-video-template'] = array(
				'type'			=> 'text',
				'title'			=> __('Video post template', 'cherry-portfolio'),
				'description'	=> __('Video post format template content', 'cherry-portfolio'),
				'value'			=> 'post-format-video-template.tmpl',
			);

			$portfolio_options = apply_filters( 'cherry_portfolio_default_settings', $portfolio_options );
			$result_array['portfolio-options-section'] = array(
				'name'			=> __('Cherry Portfolio', 'cherry-portfolio'),
				'icon' 			=> 'dashicons dashicons-format-gallery',
				'priority'		=> 120,
				'options-list'	=> $portfolio_options
			);

			return $result_array;
		}

		/**
		 * Adds a option in `Grid -> Layouts` subsection.
		 *
		 * @since 1.0.0
		 * @param array $sections
		 */
		public function add_cherry_options( $layouts_options ) {
			$layouts_options['single-portfolio-layout'] = array(
				'type'        => 'radio',
				'title'       => __( 'Portfolio posts', 'cherry-portfolio' ),
				'hint'        => array(
					'type'    => 'text',
					'content' => __( 'You can choose if you want to display sidebars and how you want to display them.', 'cherry-portfolio' ),
				),
				'value'         => 'no-sidebar',
				'display_input' => false,
				'options'       => array(
					'sidebar-content' => array(
						'label'   => __( 'Left sidebar', 'cherry-portfolio' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-left-sidebar.svg',
					),
					'content-sidebar' => array(
						'label'   => __( 'Right sidebar', 'cherry-portfolio' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-right-sidebar.svg',
					),
					'sidebar-content-sidebar' => array(
						'label'   => __( 'Left and right sidebar', 'cherry-portfolio' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-both-sidebar.svg',
					),
					'sidebar-sidebar-content' => array(
						'label'   => __( 'Two sidebars on the left', 'cherry-portfolio' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-sameside-left-sidebar.svg',
					),
					'content-sidebar-sidebar' => array(
						'label'   => __( 'Two sidebars on the right', 'cherry-portfolio' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-sameside-right-sidebar.svg',
					),
					'no-sidebar' => array(
						'label'   => __( 'No sidebar', 'cherry-portfolio' ),
						'img_src' => get_template_directory_uri() . '/lib/admin/assets/images/svg/page-layout-fullwidth.svg',
					),
				)
			);

			return $layouts_options;
		}

		/**
		 * Rewrite a single option.
		 *
		 * @since 1.0.0
		 */
		public function get_single_option( $value, $object_id ) {

			if ( CHERRY_PORTFOLIO_NAME != get_post_type( $object_id ) ) {
				return $value;
			}

			return Cherry_Portfolio_Data::cherry_portfolio_get_option( 'single-portfolio-layout', 'no-sidebar' );
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

	}//end class

	Portfolio_Options::get_instance();
}
