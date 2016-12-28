<?php
/**
 * Cherry Project
 *
 * @package   Cherry_Project
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Portfolio data.
 *
 * @since 1.0.0
 */
class Cherry_Project_Data {

	/**
	 * Default options array
	 *
	 * @var array
	 */
	public $default_options = array();

	/**
	 * Current options array
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Post query object.
	 *
	 * @var null
	 */
	private $posts_query = null;

	/**
	 * Cherry utility init
	 *
	 * @var null
	 */
	public $cherry_utility = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_get_new_projects', array( $this, 'get_new_projects' ) );
		add_action( 'wp_ajax_nopriv_get_new_projects', array( $this, 'get_new_projects' ) );

		add_action( 'wp_ajax_get_more_projects', array( $this, 'get_more_projects' ) );
		add_action( 'wp_ajax_nopriv_get_more_projects', array( $this, 'get_more_projects' ) );

		$this->set_default_options();

		$this->set_cherry_utility();
	}

	/**
	 * Get defaults data options
	 *
	 * @return void
	 */
	public function set_default_options() {
		$this->default_options = array(
			'listing-layout'               => cherry_projects()->get_option( 'listing-layout', 'masonry-layout' ),
			'loading-mode'                 => cherry_projects()->get_option( 'loading-mode', 'ajax-pagination-mode' ),
			'loading-animation'            => cherry_projects()->get_option( 'loading-animation', 'loading-animation-fade' ),
			'hover-animation'              => cherry_projects()->get_option( 'hover-animation', 'simple-fade' ),
			'filter-visible'               => cherry_projects()->get_option( 'filter-visible', 'true' ),
			'filter-type'                  => cherry_projects()->get_option( 'filter-type', 'category' ),
			'category-list'                => cherry_projects()->get_option( 'category-list', array() ),
			'tags-list'                    => cherry_projects()->get_option( 'tags-list', array() ),
			'order-filter-visible'         => cherry_projects()->get_option( 'order-filter-visible', 'true' ),
			'order-filter-default-value'   => cherry_projects()->get_option( 'order-filter-default-value', 'desc' ),
			'orderby-filter-default-value' => cherry_projects()->get_option( 'orderby-filter-default-value', 'date' ),
			'posts-format'                 => cherry_projects()->get_option( 'posts-format', 'post-format-all' ),
			'single-term'                  => cherry_projects()->get_option( 'single-term', '' ),
			'column-number'                => cherry_projects()->get_option( 'column-number', 3 ),
			'post-per-page'                => cherry_projects()->get_option( 'post-per-page', 9 ),
			'item-margin'                  => cherry_projects()->get_option( 'item-margin', 4 ),
			'justified-fixed-height'       => cherry_projects()->get_option( 'justified-fixed-height', 300 ),
			'masonry-template'             => cherry_projects()->get_option( 'masonry-template', 'masonry-default.tmpl' ),
			'grid-template'                => cherry_projects()->get_option( 'grid-template', 'grid-default.tmpl' ),
			'justified-template'           => cherry_projects()->get_option( 'justified-template', 'justified-default.tmpl' ),
			'cascading-grid-template'      => cherry_projects()->get_option( 'cascading-grid-template', 'cascading-grid-default.tmpl' ),
			'list-template'                => cherry_projects()->get_option( 'list-template', 'list-default.tmpl' ),
			'standard-post-template'       => cherry_projects()->get_option( 'standard-post-template', 'standard-post-template.tmpl' ),
			'image-post-template'          => cherry_projects()->get_option( 'image-post-template', 'image-post-template.tmpl' ),
			'gallery-post-template'        => cherry_projects()->get_option( 'gallery-post-template', 'gallery-post-template.tmpl' ),
			'audio-post-template'          => cherry_projects()->get_option( 'audio-post-template', 'audio-post-template.tmpl' ),
			'video-post-template'          => cherry_projects()->get_option( 'video-post-template', 'video-post-template.tmpl' ),
			'echo'                         => true,
		);

		/**
		 * Filter the array of default options.
		 *
		 * @since 1.0.0
		 * @param array options.
		 * @param array The 'the_portfolio_items' function argument.
		 */
		$this->default_options = apply_filters( 'cherry_projects_default_options', $this->default_options );
	}

	/**
	 * Set cherry utility object
	 *
	 * @return void
	 */
	public function set_cherry_utility() {
		cherry_projects()->get_core()->init_module( 'cherry-utility' );
		$this->cherry_utility = cherry_projects()->get_core()->modules['cherry-utility']->utility;
	}

	/**
	 * Render project
	 *
	 * @return string html string
	 */
	public function render_projects( $options = array() ) {
		$this->enqueue_styles();
		$this->enqueue_scripts();

		$this->options = wp_parse_args( $options, $this->default_options );

		// The Query.
		$filter_type = CHERRY_PROJECTS_NAME . '_' . $this->options['filter-type'];
		$posts_query = $this->get_query_projects_items(
			array(
				$filter_type     => '',
				'posts_per_page' => $this->options['post-per-page'],
				'order'          => $this->options['order-filter-default-value'],
				'orderby'        => $this->options['orderby-filter-default-value'],
			)
		);

		if ( ! is_wp_error( $posts_query ) ) {
			switch ( $this->options['listing-layout'] ) {
				case 'masonry-layout':
					$template = $this->options['masonry-template'];
					break;
				case 'grid-layout':
					$template = $this->options['grid-template'];
					break;
				case 'justified-layout':
					$template = $this->options['justified-template'];
					break;
				case 'cascading-grid-layout':
					$template = $this->options['cascading-grid-template'];
					break;
				case 'list-layout':
					$template = $this->options['list-template'];
					break;
			}

			$settings = array(
				'list-layout'   => $this->options['listing-layout'],
				'loading-mode'  => $this->options['loading-mode'],
				'post-per-page' => $this->options['post-per-page'],
				'column-number' => $this->options['column-number'],
				'item-margin'   => $this->options['item-margin'],
				'fixed-height'  => $this->options['justified-fixed-height'],
				'posts-format'  => $this->options['posts-format'],
				'single-term'   => $this->options['single-term'],
				'filter-type'   => $this->options['filter-type'],
				'template'      => $template,
			);

			$settings = json_encode( $settings );

			$html = '<div class="cherry-projects-wrapper">';

				if ( 'true' == $this->options['filter-visible'] && $posts_query->have_posts() ) {
					$html .= $this->render_ajax_filter( array() );
				}

				$container_class = 'projects-container cherry-animation-container ' . $this->options['listing-layout'] . ' ' . $this->options['loading-mode'] . ' ' . $this->options['loading-animation'];

				$html .= sprintf( '<div class="%1$s" data-settings=\'%2$s\'>', $container_class, $settings );
					$html .= '<div class="projects-list cherry-animation-list" data-all-posts-count="' . $this->posts_query->found_posts . '"></div>';
				$html .= '</div>';

				/**
				 * End line spinner html filter
				 *
				 * @since 1.1.0
				 */
				$line_spinner_html = apply_filters( 'cherry-projects-end-line-spinner-html', '<div class="projects-end-line-spinner"><div class="cherry-spinner cherry-spinner-double-bounce"><div class="cherry-double-bounce1"></div><div class="cherry-double-bounce2"></div></div></div>' );

				$html .= $line_spinner_html;

				/**
				 * Ajax loader html filter
				 *
				 * @since 1.1.0
				 */
				$ajax_loader_html = apply_filters( 'cherry-projects-ajax-loader-html', '<div class="cherry-projects-ajax-loader"><div class="cherry-spinner cherry-spinner-double-bounce"><div class="cherry-double-bounce1"></div><div class="cherry-double-bounce2"></div></div></div>' );

				$html .= $ajax_loader_html;
			// Close wrapper.
			$html .= '</div>';

			if ( ! filter_var( $this->options['echo'], FILTER_VALIDATE_BOOLEAN ) ) {
				return $html;
			}

			echo $html;
		}
	}

	/**
	 * Ajax new projects list hook
	 *
	 * @return void
	 */
	public function get_new_projects() {

		if ( ! empty( $_POST ) && array_key_exists( 'settings', $_POST ) ) {

			$settings = $_POST['settings'];

			$term_type = ( 'category' == $settings['filter_type'] ) ? CHERRY_PROJECTS_NAME . '_category' : CHERRY_PROJECTS_NAME . '_tag';

			$query_args = array(
				$term_type       => $settings['slug'],
				'posts_per_page' => $settings['post_per_page'],
				'order'          => $settings['order_settings']['order'],
				'orderby'        => $settings['order_settings']['orderby'],
				'paged'          => intval( $settings['page'] ),
			);

			if ( 'post-format-all' !== $settings['posts_format'] ) {
				$terms = array( $settings['posts_format'] );
				$operator = 'IN';

				if ( 'post-format-standard' == $settings['posts_format'] ) {
					$terms = array( 'post-format-gallery', 'post-format-image', 'post-format-audio', 'post-format-video' );
					$operator = 'NOT IN';
				}

				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => $terms,
						'operator' => $operator,
					),
				);
			}

			// The Query.
			$posts_query = $this->get_query_projects_items( $query_args );

			$html = '<div class="projects-list cherry-animation-list" data-all-posts-count="' . $posts_query->found_posts . '">';
				$html .= $this->render_projects_items( $posts_query, $settings );
			$html .= '</div>';

			$page_count = intval( ceil( $this->posts_query->found_posts / intval( $settings['post_per_page'] ) ) );

			switch ( $settings['loading_mode'] ) {
				case 'ajax-pagination-mode':
						$html .= $this->render_ajax_pagination( $posts_query->query_vars['paged'], $page_count );
					break;
				case 'more-button-mode':
					/**
					 * Filter more button text
					 *
					 * @since 1.0.0
					 */
					$button_text = apply_filters( 'cherry-projects-more-buttom-text', esc_html__( 'Load more', 'cherry-projects' ) );

					if ( $page_count > 1 ) {
						$html .= '<div class="projects-ajax-button-wrapper">';
							$html .= '<div class="projects-ajax-button"><span>' . $button_text . '</span></div>';
						$html .= '</div>';
					}

					break;
				case 'none-mode':
					$html .= '<!-- Loading mode: none -->';
				break;
			}

			echo $html;

			exit();
		}
	}

	/**
	 * Ajax new projects list hook
	 *
	 * @return void
	 */
	public function get_more_projects() {

		if ( ! empty( $_POST ) && array_key_exists( 'settings', $_POST ) ) {

			$settings = $_POST['settings'];

			$term_type = ( 'category' == $this->default_options['filter-type'] ) ? CHERRY_PROJECTS_NAME . '_category' : CHERRY_PROJECTS_NAME . '_tag';
			$query_args = array(
				$term_type       => $settings['slug'],
				'posts_per_page' => $settings['post_per_page'],
				'order'          => $settings['order_settings']['order'],
				'orderby'        => $settings['order_settings']['orderby'],
				'paged'          => intval( $settings['page'] ),
			);

			if ( 'post-format-all' !== $settings['posts_format'] ) {
				$terms = array( $settings['posts_format'] );
				$operator = 'IN';

				if ( 'post-format-standard' == $settings['posts_format'] ) {
					$terms = array( 'post-format-gallery', 'post-format-image', 'post-format-audio', 'post-format-video' );
					$operator = 'NOT IN';
				}

				$query_args['tax_query'] = array(
					array(
						'taxonomy'	=> 'post_format',
						'field'		=> 'slug',
						'terms'		=> $terms,
						'operator'	=> $operator,
					),
				);
			}

			// The Query.
			$posts_query = $this->get_query_projects_items( $query_args );

			$html = $this->render_projects_items( $posts_query, $settings );

			echo $html;

			exit();
		}
	}

	/**
	 * Get projects posts query.
	 *
	 * @since  1.0.0
	 * @param  array|string $query_args Arguments to be passed to the query.
	 * @return array|bool               Array if true, boolean if false.
	 */
	public function get_query_projects_items( $query_args = array() ) {

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$defaults_query_args = apply_filters( 'cherry_projects_default_query_args',
			array(
				'post_type'      => CHERRY_PROJECTS_NAME,
				'order'          => 'DESC',
				'orderby'        => 'date',
				'posts_per_page' => 9,
				'paged'          => $paged,
				'post_status'    => 'publish',
			)
		);

		$query_args = wp_parse_args( $query_args, $defaults_query_args );
		// The Query.
		$posts_query = new WP_Query( $query_args );

		$this->posts_query = $posts_query;

		if ( ! is_wp_error( $posts_query ) ) {
			return $posts_query;
		} else {
			return false;
		}
	}

	/**
	 * Render projects items.
	 *
	 * @param  object $posts_query    Query posts object.
	 * @param  string $listing_layout Listing layout type.
	 * @param  string $template       Template name.
	 * @return string
	 */
	public function render_projects_items( $posts_query, $settings = array() ) {
		$count = 1;
		$html = '';

		if ( $posts_query->have_posts() ) {

			// Item template.
			$template = $this->get_template_by_name( $settings['template'], 'projects' );

			$macros    = '/%%.+?%%/';
			$callbacks = $this->setup_template_data( $settings );

			while ( $posts_query->have_posts() ) : $posts_query->the_post();
				$post_id  = $posts_query->post->ID;
				$thumb_id = get_post_thumbnail_id();

				$template_content = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );

				$size_array	= cherry_projects()->projects_data->cherry_utility->satellite->get_thumbnail_size_array( 'large' );
				$data_attrs = '';
				if ( 'justified-layout' === $settings['list_layout'] || 'cascading-grid-layout' === $settings['list_layout']  ) {
					if ( has_post_thumbnail( $post_id ) ) {
						$attachment_image_src = wp_get_attachment_image_src( $thumb_id, 'large' );

					}
					$data_attrs = sprintf(
						'data-image-width="%1$s" data-image-height="%2$s"',
						isset( $attachment_image_src[1] ) ? $attachment_image_src[1] : $size_array['width'],
						isset( $attachment_image_src[2] ) ? $attachment_image_src[2] : $size_array['height']
					);
				}

				$html .= sprintf( '<div %1$s class="%2$s %3$s %4$s %5$s %6$s %7$s" %8$s>',
					'id="quote-' . $post_id .'"',
					'projects-item projects-item-instance cherry-animation-item',
					'item-' . $count,
					( ( $count++ % 2 ) ? 'odd' : 'even' ),
					'animate-cycle-show',
					$this->default_options['listing-layout'] . '-item',
					$this->default_options['hover-animation'] . '-hover',
					$data_attrs
				);
					$html .= '<div class="inner-wrapper">';
						$html .= $template_content;
					$html .= '</div>';
				$html .= '</div>';

				$callbacks->clear_data();
			endwhile;
		} else {
			echo '<h4>' . esc_html__( 'Posts not found', 'cherry-projects' ) . '</h4>';
		}

		// Reset the query.
		wp_reset_postdata();

		return $html;
	}

	/**
	 * Get ajax filter fot list items.
	 *
	 * @since  1.0.0
	 * @param  array $options Filters settings.
	 * @return string.
	 */
	public function render_ajax_filter( $options = array() ) {

		$tax_list = ( 'category' === $this->options['filter-type'] ) ? $this->options['category-list'] : $this->options['tags-list'];

		// $tax_list is array checking or convert to array.
		if ( ! is_array( $tax_list ) && is_string( $tax_list ) ) {
			$tax_list = explode( ',', $tax_list );
		}

		$args = array(
			'type'       => CHERRY_PROJECTS_NAME,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'taxonomy'   => CHERRY_PROJECTS_NAME . '_' . $this->options['filter-type'],
			'pad_counts' => false,
		);

		$order_array = array(
			'desc' => esc_html__( 'Desc', 'cherry-projects' ),
			'asc'  => esc_html__( 'Asc', 'cherry-projects' ),
		);

		$order_by_array = array(
			'date'          => esc_html__( 'Date', 'cherry-projects' ),
			'name'          => esc_html__( 'Name', 'cherry-projects' ),
			'modified'      => esc_html__( 'Modified', 'cherry-projects' ),
			'comment_count' => esc_html__( 'Comments', 'cherry-projects' ),
		);

		$terms = get_categories( $args );

		$html = '<div class="projects-filters with-ajax" data-order-default="' . $this->options['order-filter-default-value'] . '" data-orderby-default="' . $this->options['orderby-filter-default-value'] . '">';

			/**
			 * Filtered before terms list render
			 *
			 * @since 1.0.0
			 */
			$html .= apply_filters( 'cherry-projects-before-filters-html', '' );

			if ( empty( $this->options['single-term'] ) ) {
				$html .= '<div class="projects-filters-list-wrapper">';

					$html .= '<ul class="projects-filters-list filter-' . $this->options['filter-type'] . '">';

					if ( $terms ) {
						$show_all_text = apply_filters( 'cherry_projects_show_all_text', esc_html__( 'Show all', 'cherry-projects' ) );
						$html .= '<li class="active"><span data-cat-id="" data-slug="">'. $show_all_text .'</span></li>';

						foreach ( $terms as $term ) {

							if ( in_array( $term->slug, $tax_list ) || empty( $tax_list ) ) {
								$html .= '<li><span data-cat-id="' .  $term->cat_ID . '" data-slug="' .  $term->slug . '">'. $term->name .'</span></li>';
							}
						}
					}
					$html .= '</ul>';

				$html .= '</div>';
			}

			/**
			 * Filtered after terms list render
			 *
			 * @since 1.0.0
			 */
			$html .= apply_filters( 'cherry-projects-after-filters-html', '' );

			if ( 'true' == $this->options['order-filter-visible'] ) {
				$html .= '<div class="projects-order-filters-wrapper">';
					$html .= '<ul class="order-filters">';
						$html .= '<li data-filter-type="order" data-desc-label="' . esc_html__( 'Desc', 'cherry-projects' ) . '" data-asc-label="' . esc_html__( 'Asc', 'cherry-projects' ) . '">';

							/**
							 * Filter order label text
							 *
							 * @since 1.0.0
							 */
							$html .= apply_filters( 'cherry-projects-order-filter-label', esc_html__( 'Order:', 'cherry-projects' ) );

							$html .= '<span class="current">' . $order_array[ $this->options['order-filter-default-value'] ] . '</span>';

						$html .= '</li>';
						$html .= '<li data-filter-type="orderby">';

							/**
							 * Filter orderby label text
							 *
							 * @since 1.0.0
							 */
							$html .= apply_filters( 'cherry-projects-orderby-filter-label', esc_html__( 'Order by:', 'cherry-projects' ) );

							$html .= '<span class="current">' . $order_by_array[ $this->options['orderby-filter-default-value'] ] . '</span>';
								$html .= '<ul class="orderby-list">';

									foreach ( $order_by_array as $key => $value ) {
										$class = ( $key == $this->options['orderby-filter-default-value'] ) ? 'class="active"' : '';
										$html .= '<li data-orderby="' . $key . '" ' . $class . '><span>' . $value . '</span></li>';
									}

								$html .= '</ul>';
						$html .= '</li>';
					$html .= '</ul>';
				$html .= '</div>';
			}
		$html .= '</div>';

		return $html;
	}


	/**
	 * Get ajax pagination fot list items.
	 *
	 * @since  1.0.0
	 * @param  int $current_page_index Current page index.
	 * @param  int $post_per_page      Post per page value.
	 * @return string HTML-formatted.
	 */
	public function render_ajax_pagination( $current_page_index = 1, $page_count = -1 ) {

		if ( -1 == $page_count || 1 == $page_count ) {
			return '';
		}

		$html = '<div class="projects-pagination with-ajax">';
			$html .= '<ul class="page-link">';
				for ( $i = 0; $i < $page_count; $i++ ) {
					$counter = $i + 1;

					/**
					 * Filters HTML-formatted before pagination item text.
					 *
					 * @since 1.0.5
					 * @var string
					 */
					$before_pagination_item = apply_filters( 'cherry-projects-before-pagination-item', '', $counter );

					/**
					 * Filters HTML-formatted after pagination item text.
					 *
					 * @since 1.0.5
					 * @var string
					 */
					$after_pagination_item = apply_filters( 'cherry-projects-after-pagination-item', '', $counter );

					$class = ( $i == $current_page_index - 1 ) ? ' class="active"' : '' ;

					$html .= sprintf( '<li%4$s>%1$s<span>%2$s</span>%3$s</li>', $before_pagination_item, $counter, $after_pagination_item, $class );
				}
			$html .= '</ul>';
			$html .= '<div class="page-navigation">';

				/**
				 * Filters HTML-formatted prev-button text.
				 *
				 * @since 1.0.0
				 * @var string
				 */
				$prev_button_text = apply_filters( 'cherry-projects-prev-button-text', esc_html__( 'Prev', 'cherry-projects' ) );

				/**
				 * Filters HTML-formatted next-button text.
				 *
				 * @since 1.0.0
				 * @var string
				 */
				$next_button_text = apply_filters( 'cherry-projects-next-button-text', esc_html__( 'Next', 'cherry-projects' ) );

				if ( 1 !== $current_page_index ) {
					$html .= '<span class="prev-page">' . $prev_button_text . '</span>';
				}

				if ( $current_page_index < $page_count ) {
					$html .= '<span class="next-page">' . $next_button_text . '</span>';
				}
			$html .= '</div>';
		$html .= '</div>';


		return $html;
	}

	/**
	 * Prepare template data to replace.
	 *
	 * @since 1.0.2
	 * @param array $atts Output attributes.
	 */
	function setup_template_data( $atts ) {
		require_once( CHERRY_PROJECTS_DIR . 'public/includes/class-cherry-projects-template-callbacks.php' );

		$callbacks = new Cherry_Projects_Template_Callbacks( $atts );

		$data = array(
			'title'           => array( $callbacks, 'get_title' ),
			'featuredimage'   => array( $callbacks, 'get_featured_image' ),
			'content'         => array( $callbacks, 'get_content' ),
			'button'          => array( $callbacks, 'get_button' ),
			'date'            => array( $callbacks, 'get_date' ),
			'author'          => array( $callbacks, 'get_author' ),
			'comments'        => array( $callbacks, 'get_comments' ),
			'termslist'       => array( $callbacks, 'get_terms_list' ),
			'detailslist'     => array( $callbacks, 'get_details_list' ),
			'skillslist'      => array( $callbacks, 'get_skills_list' ),
			'imagelist'       => array( $callbacks, 'get_image_list' ),
			'slider'          => array( $callbacks, 'get_slider' ),
			'zoomlink'        => array( $callbacks, 'get_zoom_link' ),
			'externallink'    => array( $callbacks, 'get_external_link' ),
			'permalink'       => array( $callbacks, 'get_permalink' ),
			'videolist'       => array( $callbacks, 'get_video_list' ),
			'audiolist'       => array( $callbacks, 'get_audio_list' ),
			'termimage'       => array( $callbacks, 'get_term_image' ),
			'termname'        => array( $callbacks, 'get_term_name' ),
			'termdescription' => array( $callbacks, 'get_term_description' ),
			'termpermalink'   => array( $callbacks, 'get_term_permalink' ),
			'termattachments' => array( $callbacks, 'get_term_attachments' ),
		);

		/**
		 * Filters item data.
		 *
		 * @since 1.0.2
		 * @param array $data Item data.
		 * @param array $atts Attributes.
		 */
		$this->post_data = apply_filters( 'cherry_projects_data_callbacks', $data, $atts );

		return $callbacks;
	}

	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		// Check for existence.
		if ( ! $wp_filesystem->exists( $template ) ) {
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( ! $content ) {
			// Return error object.
			return new WP_Error( 'reading_error', 'Error when reading file' );
		}

		return $content;
	}

	/**
	 * Retrieve a *.tmpl file content.
	 *
	 * @since  1.0.0
	 * @param  string $template  File name.
	 * @param  string $shortcode Shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {
		$file       = '';
		$default    = CHERRY_PROJECTS_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
		$upload_dir = wp_upload_dir();
		$upload_dir = trailingslashit( $upload_dir['basedir'] );
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;
		/**
		 * Filters a default fallback-template.
		 *
		 * @since 1.0.0
		 * @param string $content.
		 */
		$content = apply_filters( 'cherry_projects_fallback_template', '<div class="inner-wrapper">%%title%%%%image%%%%content%%</div>' );

		if ( file_exists( $upload_dir . $subdir ) ) {
			$file = $upload_dir . $subdir;
		} elseif ( $theme_template = locate_template( array( 'cherry-projects/' . $template ) ) ) {
			$file = $theme_template;
		} elseif ( file_exists( CHERRY_PROJECTS_DIR . $subdir ) ) {
			$file = CHERRY_PROJECTS_DIR . $subdir;
		} else {
			$file = $default;
		}

		if ( ! empty( $file ) ) {
			$content = self::get_contents( $file );
		}

		return $content;
	}

	/**
	 * Callback to replace macros with data.
	 *
	 * @since 1.0.0
	 * @param array $matches Founded macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return;
		}

		if ( empty( $matches ) ) {
			return;
		}

		$item   = trim( $matches[0], '%%' );
		$arr    = explode( ' ', $item, 2 );
		$macros = strtolower( $arr[0] );
		$attr   = isset( $arr[1] ) ? shortcode_parse_atts( $arr[1] ) : array();

		$callback = $this->post_data[ $macros ];

		if ( ! is_callable( $callback ) || ! isset( $this->post_data[ $macros ] ) ) {
			return;
		}

		if ( ! empty( $attr ) ) {

			// Call a WordPress function.
			return call_user_func( $callback, $attr );
		}

		return call_user_func( $callback );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/magnific-popup.css', array(), '1.1.0' );
		wp_enqueue_style( 'cherry-projects-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/styles.css', array(), CHERRY_PROJECTS_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'waypoints', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.waypoints.min.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		wp_enqueue_script( 'imagesloaded', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/imagesloaded.pkgd.min.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
		wp_enqueue_script( 'cherry-projects-plugin', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-plugin.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
		wp_enqueue_script( 'cherry-projects-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );

		$cascading_list_map = apply_filters( 'cherry_projects_cascading_list_map', array( 1, 2, 2, 3, 3, 3, 4, 4, 4, 4 ) );

		// Ajax js object portfolio_type_ajax.
		wp_localize_script( 'cherry-projects-scripts', 'cherryProjectsObjects',
			array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'cascadingListMap' => $cascading_list_map,
			)
		);
	}

}
