<?php
/**
 * Cherry Portfolio.
 *
 * @package   Cherry_Portfolio
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
class Cherry_Portfolio_Data {

	/**
	 * The array of arguments for query.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public static $options = array();

	public static $default_options = array();

	public static $postdata = array();

	private $posts_query = '';

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		self::$default_options = array(
			'post_type'							=> CHERRY_PORTFOLIO_NAME,
			CHERRY_PORTFOLIO_NAME.'_category'	=> '',
			'orderby'							=> 'date',
			'order'								=> 'DESC',
			'posts_per_page'					=> self::cherry_portfolio_get_option('portfolio-post-per-page', 6),
			'offset'							=> 0,
			'suppress_filters'					=> false,
			'posts_format'						=> 'post-format-all',
			//////////////////////////////////////////////
			'image_size'						=> 'full',
			'title'								=> '',
			'wrap_class'						=> 'portfolio-wrap',
			'before_title'						=> '<h3>',
			'after_title'						=> '</h3>',
			'masonry_template'					=> self::cherry_portfolio_get_option('portfolio-masonry-template', 'masonry-default.tmpl'),
			'grid_template'						=> self::cherry_portfolio_get_option('portfolio-grid-template', 'grid-default.tmpl'),
			'list_template'						=> self::cherry_portfolio_get_option('portfolio-list-template', 'list-default.tmpl'),
			'justified_template'				=> self::cherry_portfolio_get_option('portfolio-justified-template', 'justified-default.tmpl'),
			'listing_layout'					=> self::cherry_portfolio_get_option('portfolio-listing-layout', 'masonry-layout'),
			'grid_col'							=> self::cherry_portfolio_get_option('portfolio-column-number', 3),
			'loading_mode'						=> self::cherry_portfolio_get_option('portfolio-loading-mode', 'portfolio-ajax-pagination-mode'),
			'loading_animation'					=> self::cherry_portfolio_get_option('portfolio-loading-animation', 'loading-animation-fade'),
			'hover_layout'						=> self::cherry_portfolio_get_option('portfolio-hover-animation', 'simple-fade'),
			'more_button_label'					=> apply_filters( 'cherry_text_translate', self::cherry_portfolio_get_option('portfolio-more-button-text', 'Read more'), 'portfolio_more_button_text' ) ,
			'filter_visible'					=> self::cherry_portfolio_get_option('portfolio-filter-visible', 'true'),
			'order_filter_visible'				=> self::cherry_portfolio_get_option('portfolio-order-filter-visible', 'false'),
			'order_filter_default'				=> self::cherry_portfolio_get_option('portfolio-order-filter-default-value', 'desc'),
			'orderby_filter_default'			=> self::cherry_portfolio_get_option('portfolio-orderby-filter-default-value', 'date'),
			'is_image_crop'						=> self::cherry_portfolio_get_option('portfolio-is-crop-image', false),
			'image_crop_width'					=> self::cherry_portfolio_get_option('portfolio-crop-image-width', 500),
			'image_crop_height'					=> self::cherry_portfolio_get_option('portfolio-crop-image-height', 350),
			'number_trim_words'					=> self::cherry_portfolio_get_option('portfolio-content-trim-words', 25),
			'tiles_mode'						=> false,
			'linked_title'						=> false,
			'custom_class'						=> '',
			'image_class'						=> '',
			'echo'								=> true,
			'item_margin'						=> self::cherry_portfolio_get_option('portfolio-item-margin', 4),
			'fixed_height'						=> self::cherry_portfolio_get_option('portfolio-justified-fixed-height', 300),
			'filter_type'						=> 'category',
			'category_list'						=> self::cherry_portfolio_get_option('portfolio-category-list', array()),
			'tag_list'							=> self::cherry_portfolio_get_option('portfolio-tags-list', array()),
			'post_format_standart_template'		=> self::cherry_portfolio_get_option('portfolio-single-standart-template', 'post-format-standart-template.tmpl'),
			'post_format_image_template'		=> self::cherry_portfolio_get_option('portfolio-single-image-template', 'post-format-image-template.tmpl'),
			'post_format_gallery_template'		=> self::cherry_portfolio_get_option('portfolio-single-gallery-template', 'post-format-gallery-template.tmpl'),
			'post_format_audio_template'		=> self::cherry_portfolio_get_option('portfolio-single-audio-template', 'post-format-audio-template.tmpl'),
			'post_format_video_template'		=> self::cherry_portfolio_get_option('portfolio-single-video-template', 'post-format-video-template.tmpl'),
			'template'							=> '',
		);

		switch ( self::cherry_portfolio_get_option('portfolio-filter-type', 'portfolio-filter-type-category') ) {
			case 'portfolio-filter-type-category':
				self::$default_options['filter_type'] = 'category';
				break;
			case 'portfolio-filter-type-tag':
				self::$default_options['filter_type'] = 'tag';
				break;
		}

		switch ( self::$default_options['listing_layout'] ) {
			case 'masonry-layout':
				self::$default_options['image_class'] = 'masonry-image';
				self::$default_options['template'] = self::$default_options['masonry_template'];
				break;
			case 'grid-layout':
				self::$default_options['image_class'] = 'grid-image';
				//self::$default_options['is_image_crop'] = true;
				self::$default_options['template'] = self::$default_options['grid_template'];
				break;
			case 'justified-layout':
				self::$default_options['image_class'] = 'justified-image';
				//self::$default_options['is_image_crop'] = false;
				self::$default_options['template'] = self::$default_options['justified_template'];
				break;
			case 'list-layout':
				self::$default_options['image_class'] = 'list-image';
				//self::$default_options['is_image_crop'] = true;
				self::$default_options['template'] = self::$default_options['list_template'];
				break;
		}

		self::$options = wp_parse_args( self::$options, self::$default_options );

		$this->enqueue_styles();
		$this->enqueue_scripts();
		/**
		 * Fires when you need to display portfolio.
		 *
		 * @since 1.0.0
		 */
		//add_action( 'cherry_get_portfolio', array( $this, 'the_portfolio' ) );
	}

	/**
	 * Display or return HTML-formatted portfolio items.
	 *
	 * @since  1.0.0
	 * @param  string|array $args Arguments.
	 * @return string
	 */
	public function the_portfolio( $options = '' ) {
		/**
		 * Filter the array of default options.
		 *
		 * @since 1.0.0
		 * @param array options.
		 * @param array The 'the_portfolio_items' function argument.
		 */

		$default_options = apply_filters( 'cherry_the_portfolio_default_options', self::$default_options );

		// default options marge
		self::$options = wp_parse_args( $options, $default_options );

		$output = '';
		// The Query.
		$posts_query = $this->get_query_portfolio_items( self::$options );
		// The Display.
		if ( !is_wp_error( $posts_query ) ) {

			$css_class = '';

			if ( !empty( self::$options['wrap_class'] ) ) {
				$css_class .= sanitize_html_class( self::$options['wrap_class'] ) . ' ';
			}

			if ( !empty( self::$options['custom_class'] ) ) {
				$css_class .= sanitize_html_class( self::$options['custom_class'] );
			}

			// Open wrapper.
			$output .= sprintf( '<div class="%s">', trim( $css_class ) );

			if ( !empty( self::$options['title'] ) ) {
				$output .= self::$options['before_title'] . __( esc_html( self::$options['title'] ), 'cherry-portfolio' ) . self::$options['after_title'];
			}

			if( self::$options['filter_visible'] == 'true' && $posts_query->have_posts() ){
				$output .= $this->build_ajax_filter( self::$options );
			}

			switch ( self::$options['loading_mode'] ) {
				case 'portfolio-ajax-pagination-mode':
						$loading_mode = 'ajax-pagination';
					break;
				case 'portfolio-more-button-mode':
						$loading_mode = 'more-button';
					break;
				case 'portfolio-none-mode':
						$loading_mode = 'none';
					break;
			}

			$container_attr = '';
			$container_attr .= ' data-post-per-page="' . self::$options['posts_per_page'] . '"';
			$container_attr .= ' data-column="' . self::$options['grid_col'] .'"';
			$container_attr .= ' data-list-layout="' . self::$options['listing_layout'] .'"';
			$container_attr .= ' data-loading-mode="' . $loading_mode .'"';
			$container_attr .= ' data-item-margin="' . self::$options['item_margin'] . '"';
			$container_attr .= ' data-fixed-height="' . self::$options['fixed_height'] . '"';
			$container_attr .= ' data-template="' . self::$options['template'] . '"';
			$container_attr .= ' data-posts-format="' . self::$options['posts_format'] . '"';

			$output .= '<div class="portfolio-container ' . self::$options['listing_layout'] . ' ' . self::$options['loading_animation'] . '" ' . $container_attr . '>';
				$output .= '<div class="portfolio-list"  data-all-posts-count="' . $this->posts_query->found_posts . '">';
				$output .= '</div>';
			$output .= '</div>';

			// Close wrapper.
			$output .= '</div>';
		}

		/**
		 * Filters HTML-formatted portfolio items before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted portfolio items.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'cherry_portfolio_html', $output, $posts_query, $options );

		if ( self::$options['echo'] != true ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;

		/**
		 * Fires after the portfolio.
		 *
		 * This hook fires only when "echo" is set to true.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_portfolio_after', $options );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'magnific-popup', plugins_url( 'public/assets/css/magnific-popup.css', __FILE__ ), array(), CHERRY_PORTFOLIO_VERSION );
		wp_enqueue_style( 'swiper', plugins_url( 'public/assets/css/swiper.css', __FILE__ ), array(), CHERRY_PORTFOLIO_VERSION );
		//wp_enqueue_style( 'cherry-portfolio', plugins_url( 'public/assets/css/style.css', __FILE__ ), array(), CHERRY_PORTFOLIO_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.0.0', true );
		wp_enqueue_script( 'imagesloaded', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/imagesloaded.pkgd.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'isotope', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/isotope.pkgd.min.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'cherry-portfolio-layout-plugin', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/cherry-portfolio-layout-plugin.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'swiper', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/swiper.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );
		wp_enqueue_script( 'cherry-portfolio-script', trailingslashit( CHERRY_PORTFOLIO_URI ) . 'public/assets/js/cherry-portfolio-scripts.js', array( 'jquery' ), CHERRY_PORTFOLIO_VERSION, true );

		//ajax js object portfolio_type_ajax
		wp_localize_script( 'cherry-portfolio-script', 'portfolio_type_ajax', array( 'url' => admin_url('admin-ajax.php') ) );
	}

	/**
	 * Get portfolio items.
	 *
	 * @since  1.0.0
	 * @param  array|string $args Arguments to be passed to the query.
	 * @return array|bool         Array if true, boolean if false.
	 */
	public function get_query_portfolio_items( $query_args = '' ) {
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

		$defaults_query_args = apply_filters( 'cherry_the_portfolio_default_query_args',
			array(
				'post_type'                       => CHERRY_PORTFOLIO_NAME,
				CHERRY_PORTFOLIO_NAME.'_category' => '',
				'order'                           => 'DESC',
				'orderby'                         => 'date',
				'posts_per_page'                  => -1,
				'paged'                           => $paged,
				'post_status'                     => 'publish',
			)
		);

		$query_args = wp_parse_args( $query_args, $defaults_query_args );

		// The Query.
		$posts_query = new WP_Query( $query_args );
		$this->posts_query = $posts_query;

		if ( !is_wp_error( $posts_query ) ) {
			return $posts_query;
		} else {
			return false;
		}
	}

	/**
	 * Get portfolio items.
	 *
	 * @since  1.0.0
	 * @param  array         $posts_query      List of WP_Post objects.
	 * @return string
	 */
	public function get_portfolio_items_loop( $posts_query, $listing_layout = 'masonry-layout', $template = 'masonry-default.tmpl' ) {
		$count  = 1;
		$output = '';

			if( empty( $template ) ){
				switch ( $listing_layout ) {
					case 'masonry-layout':
						$template = self::$options['masonry_template'];
						break;
					case 'grid-layout':
						$template = self::$options['grid_template'];
						break;
					case 'justified-layout':
						$template = self::$options['justified_template'];
						break;
					case 'list-layout':
						$template = self::$options['list_template'];
						break;
				}
			}

			if ( $posts_query->have_posts() ) {

				// Item template's file.
				$template = self::get_template_by_name( $template, Cherry_Portfolio_Shortcode::$name );

				if ( false == $template ) {
					return '<h4>' . __( 'Template file (*.tmpl) not found', 'cherry-portfolio' ) . '</h4>';
				}

				// Temp array for post data.
				$_postdata = array();

				// Date format.
				$date_format = get_option( 'date_format' );
				preg_match_all( '/DATE=".+?"/', $template, $match, PREG_SET_ORDER );
				if ( is_array( $match ) && !empty( $match ) ) {
					$_atts       = shortcode_parse_atts( $match[0][0] );
					$date_format = $_atts['date'];
				}

				// Taxonomy.
				$tax = array();
				preg_match_all( '/TAXONOMY=".+?"/', $template, $match, PREG_SET_ORDER );
				if ( is_array( $match ) && !empty( $match ) ) {
					foreach ( $match as $m ) {
						$_atts = shortcode_parse_atts( $m[0] );
						$tax[] = $_atts['taxonomy'];
					}
				}

				// ZOOMLINK text.
				$zoomlink_text = '';
				preg_match_all( '/ZOOMLINK=".+?"/', $template, $match, PREG_SET_ORDER );
				if ( is_array( $match ) && !empty( $match ) ) {
					$_atts       = shortcode_parse_atts( $match[0][0] );
					$zoomlink_text= $_atts['zoomlink'];
				}
				// ZOOMLINK text.
				$permalink_text = '';
				preg_match_all( '/PERMALINK=".+?"/', $template, $match, PREG_SET_ORDER );
				if ( is_array( $match ) && !empty( $match ) ) {
					$_atts       = shortcode_parse_atts( $match[0][0] );
					$permalink_text= $_atts['permalink'];
				}
				// content
				$number_trim_words = 25;
				preg_match_all( '/CONTENT=".+?"/', $template, $match, PREG_SET_ORDER );
				if ( is_array( $match ) && !empty( $match ) ) {
					$_atts       = shortcode_parse_atts( $match[0][0] );
					$number_trim_words = $_atts['content'];
				}
				// content
				$number_gallery_thumbnails = 99;
				preg_match_all( '/GALLERYTHUMBNAILS=".+?"/', $template, $match, PREG_SET_ORDER );
				if ( is_array( $match ) && !empty( $match ) ) {
					$_atts       = shortcode_parse_atts( $match[0][0] );
					$number_gallery_thumbnails = $_atts['gallerythumbnails'];
				}

				while ( $posts_query->have_posts() ) : $posts_query->the_post();

				$tpl          = $template;
				$post_id      = $posts_query->post->ID;
				$post_meta    = get_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, true );
				$externallink = isset( $post_meta['external-link-url'] ) ? $post_meta['external-link-url'] : '' ;
				$date         = get_the_date( $date_format );
				$post_type    = get_post_type( $post_id );
				$permalink    = get_permalink();
				$title        = get_the_title( $post_id );
				$author       = get_the_author();
				$author_url   = get_author_posts_url( get_the_author_meta( 'ID' ) );
				$thumb_id     = get_post_thumbnail_id();
				$format       = get_post_format( $post_id );
				$format       = (empty( $format )) ? 'post-format-standart' : 'post-format-' . $format;
				$justified_attrs = '';
				$placeholder_arg = apply_filters( 'cherry_portfolio_placeholder_args',
					array(
						'width'			=> self::$default_options['image_crop_width' ],
						'height'		=> self::$default_options['image_crop_height'],
						'background'	=> '282828',
						'foreground'	=> 'EAE0D0',
						'title'			=> $title,
						'class'			=> self::$default_options['image_class'],
					)
				);
				// Excerpt.
				if ( post_type_supports( $post_type, 'excerpt' ) ) {
					$excerpt = has_excerpt( $post_id ) ? apply_filters( 'the_excerpt', get_the_excerpt() ) : '';
				}
				// Comments.
				if ( post_type_supports( $post_type, 'comments' ) ) {
					$comments = ( comments_open() || get_comments_number() ) ? get_comments_number() : '';
				}
				// Image
				if(	"true" == self::$default_options['is_image_crop'] || 'grid-layout' == $listing_layout ){
					$img_url = wp_get_attachment_url( $thumb_id ,'full'); //get img URL
					$image = $this->get_crop_image( $img_url, $thumb_id, self::$default_options['image_crop_width' ], self::$default_options['image_crop_height' ] );
				}else{
					$image = $this->get_image( $post_id, 'large' );
				}
				//check the attached image, if not attached - function replaces on the placeholder
				if( !$image ){
					$image = $this->get_placeholder( $placeholder_arg );
				}

				if( $listing_layout === 'justified-layout' ){
					if ( has_post_thumbnail( $post_id ) ) {
						$attachment_image = wp_get_attachment_image_src( $thumb_id, 'large' );
						$image_ratio = $attachment_image[1] / $attachment_image[2];
						$justified_attrs = 'data-image-src="' . $attachment_image[0] . '"';
						$justified_attrs .= ' data-image-width="' . $attachment_image[1] . '"';
						$justified_attrs .= ' data-image-height="' . $attachment_image[2] . '"';
						$justified_attrs .= ' data-image-ratio="' . $image_ratio . '"';
					}else{
						$placeholder_link = 'http://fakeimg.pl/' . $placeholder_arg['width'] . 'x' . $placeholder_arg['height'] . '/'. $placeholder_arg['background'] .'/'. $placeholder_arg['foreground'] ;
						$image_ratio = $placeholder_arg['width'] / $placeholder_arg['height'];
						$justified_attrs = 'data-image-src="' . $placeholder_link . '"';
						$justified_attrs .= ' data-image-width="' . $placeholder_arg['width'] . '"';
						$justified_attrs .= ' data-image-height="' . $placeholder_arg['height'] . '"';
						$justified_attrs .= ' data-image-ratio="' . $image_ratio . '"';
					}
				}

				$attachment_image_data = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), self::$default_options['image_size' ], false );

				$figure_image = ( 'justified-layout' == $listing_layout ) ? '<div class="justified-image"></div>' : $image ;

				$content = apply_filters( 'cherry_portfolio_content', get_the_content(), $posts_query->post );

				$content = wp_trim_words( $content, $number_trim_words, '...' );

				$category_name_list = $this->get_taxonomy_list( $post_id, 'category' );

				$gallery_thumbnails = '';

				$thumbnails_count = 0;

				if ( !empty( $title_text ) ) {
					$title = ( $linked_title ) ? sprintf( '<a href="%1$s" title="%2$s" class="%3$s">%4$s</a>', esc_url( $permalink ), esc_attr( $title_attr ), 'post-title-link', esc_attr( $title_text ) ) : sprintf( '%s', esc_attr( $title_text ) );
				}

				switch ($format) {
					case 'post-format-standart':
							$format = __('Standart post', 'cherry-portfolio');
						break;
					case 'post-format-image':
							$format = __('Image post', 'cherry-portfolio');
						break;
					case 'post-format-gallery':
							$format = __('Gallery post', 'cherry-portfolio');
							if ( isset( $post_meta['portfolio-gallery-attachments-ids'] ) ) {
								$attachments_ids_array = explode( ",", $post_meta['portfolio-gallery-attachments-ids'] );
								$gallery_thumbnails .= '<ul class="thumbnailset">';
								$counter = 0;
								foreach ( $attachments_ids_array as $attachment_id ) {
									if( intval($number_gallery_thumbnails) == $counter ){
										break;
									}

									$attachment_url = wp_get_attachment_image_src( $attachment_id, apply_filters('cherry-portfolio-thumbnails-size', 'thumbnail' ) );
									$attachment_full_url = wp_get_attachment_image_src( $attachment_id, apply_filters('cherry-portfolio-attachment-full-size', 'cherry-thumb-xl' ) );
									$thumbnail_classes =  apply_filters('cherry-portfolio-thumbnail-classes', 'thumbnail-link');
									$before_thumbnail = apply_filters('cherry-portfolio-before-thumbnail', '<a class="' . $thumbnail_classes . '" href="' . $attachment_full_url[0] .'" data-effect="mfp-zoom-in">');
									$after_thumbnail = '</a>';
									$thumbnail = sprintf( '%5$s<img src="%1$s" width="%2$s" height="%3$s" alt="%4$s">%6$s', $attachment_url[0], $attachment_url[1], $attachment_url[2], get_the_title( $attachment_id ), $before_thumbnail, $after_thumbnail );
									$gallery_thumbnails .= '<li class="item-' . $counter . '">' . $thumbnail . '</li>';
									$counter++;
								}
								$gallery_thumbnails .= '<div class="clear"></div>';
								$gallery_thumbnails .= '</ul>';

								$thumbnails_count = count( $attachments_ids_array );
							}
						break;
					case 'post-format-audio':
							$format = __('Audio post', 'cherry-portfolio');
						break;
					case 'post-format-video':
							$format = __('Video post', 'cherry-portfolio');
						break;
					default:
						break;
				}

				$comments = ( !empty( $comments ) ) ? sprintf( '<span class="post-comments-link"><a href="%1$s">%2$s</a></span>', esc_url( get_comments_link() ), $comments ) : '';
				$date     = sprintf( '<time class="post-date" datetime="%1$s">%2$s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( $date ) );
				$author   = sprintf( '<span class="post-author vcard"><a href="%1$s" rel="author">%2$s</a></span>', esc_url( $author_url ), $author );
				$excerpt  = ( !empty( $excerpt ) ) ? sprintf( '<div class="post-excerpt">%s</div>', $excerpt ) : '';

				$link_target = ( isset( $post_meta['external-link-target'] ) ) ? $post_meta['external-link-target'] : '_black';
				$externallink  = ( !empty( $externallink ) ) ? sprintf( '<a class="item-link externallink" href="%1$s" target="%3$s">%2$s</a>', $externallink, $post_meta['external-link-text'], $link_target ) : '';

				$permalink  = sprintf( '<a class="item-link permalink" href="%1$s">%2$s</a>', $permalink, $permalink_text );
				$zoomlink  = sprintf( '<a class="item-link zoomlink magnific-popup-link" href="%1$s">%2$s</a>', $attachment_image_data[0], $zoomlink_text );
				$postformat = sprintf( '<span class="post-format">%s</span>', $format );
				$thumbnails_count = apply_filters('cherry-portfolio-thumbnails-count-html', ( 0 !== $thumbnails_count ) ? sprintf( _n( '%s image', '%s images', $thumbnails_count, 'cherry-portfolio' ), $thumbnails_count ) : __('No image', 'cherry-portfolio') );

				// Prepare a current post data array.
				$_postdata['title']    = $title;
				$_postdata['image']    = $figure_image;
				$_postdata['taxonomy']    = $category_name_list;
				$_postdata['content']    = $content;
				$_postdata['date']    = $date;
				$_postdata['author']    = $author;
				$_postdata['comments']    = $comments;
				$_postdata['externallink']    = $externallink;
				$_postdata['zoomlink']    = $zoomlink;
				$_postdata['permalink']    = $permalink;
				$_postdata['url']    = get_permalink();
				$_postdata['postformat']    = $postformat;
				$_postdata['gallerythumbnails']    = $gallery_thumbnails;
				$_postdata['thumbnailscount']    = $thumbnails_count;

				/**
				 * Filters the array with a current post data.
				 *
				 * @since 1.0.0
				 * @param array  $_postdata Array with a current post data.
				 * @param int    $post_id   Post ID.
				 * @param array  $atts      Shortcode attributes.
				 */
				$_postdata = apply_filters( 'cherry-shortcode-portfolio-postdata', $_postdata, $post_id );

				// Init a `postdata` array.
				self::$postdata = $_postdata;

				$tpl = preg_replace_callback( "/%%.+?%%/", array( $this, 'replace_callback' ), $tpl );

				self::$default_options['tiles_mode']? $tile_item_class = 'tile-item' : $tile_item_class = '' ;

				$list_item_attrs = '';
				$list_item_attrs .= 'id="quote-' . $post_id . '" class="portfolio-item item-' . $count . ( ( $count++ % 2 ) ? ' odd' : ' even' ) . ' animate-cycle-show ' . $tile_item_class . ' ' . $listing_layout . '-item ' . self::$default_options['hover_layout'] . '-hover clearfix"' . $justified_attrs ;

				$output .= '<div ' . $list_item_attrs . '>';
					$tpl = apply_filters( 'cherry_get_portfolio_loop', $tpl, $post_meta );
					$output .= $tpl;
				$output .= '</div>';

				endwhile;
			} else {
				echo '<h4>' . __( 'Posts not found', 'cherry-portfolio' ) . '</h4>';
			}

		// Reset the query.
		wp_reset_postdata();

		// Reset the `postdata`.
		self::reset_postdata();

		return $output;
	}

	/**
	 * Display or return HTML-formatted portfolio single post page.
	 *
	 * @since  1.0.0
	 * @return string(html)
	 */
	public function portfolio_single_data() {
		$post_id = get_the_ID();
		$format = get_post_format( $post_id );
		$format = (empty( $format )) ? 'post-format-standart' : 'post-format-' . $format;
		echo $this->build_single_post_page( $post_id, $format );
	}

	/**
	 * Build portfilio single post page.
	 *
	 * @since  1.0.0
	 * @param  int|string $args.
	 * @return string|bool.
	 */
	public function build_single_post_page( $post_id, $format ) {
		$template_file = false;
		switch ($format) {
			case 'post-format-standart':
				$template = self::get_template_by_name( self::$default_options['post_format_standart_template'], Cherry_Portfolio_Shortcode::$name );
				break;
			case 'post-format-image':
				$template = self::get_template_by_name( self::$default_options['post_format_image_template'], Cherry_Portfolio_Shortcode::$name );
				break;
			case 'post-format-gallery':
				$template = self::get_template_by_name( self::$default_options['post_format_gallery_template'], Cherry_Portfolio_Shortcode::$name );
				break;
			case 'post-format-audio':
				$template = self::get_template_by_name( self::$default_options['post_format_audio_template'], Cherry_Portfolio_Shortcode::$name );
				break;
			case 'post-format-video':
				$template = self::get_template_by_name( self::$default_options['post_format_video_template'], Cherry_Portfolio_Shortcode::$name );
				break;
			default:
				break;
		}

		if ( false == $template ) {
			return '<h4>' . __( 'Template file (*.tmpl) not found', 'tm' ) . '</h4>';
		}

		// Temp array for post data.
		$_postdata = array();
		// Date format.
		$date_format = get_option( 'date_format' );
		preg_match_all( '/DATE=".+?"/', $template, $match, PREG_SET_ORDER );
		if ( is_array( $match ) && !empty( $match ) ) {
			$_atts       = shortcode_parse_atts( $match[0][0] );
			$date_format = $_atts['date'];
		}

		// Taxonomy.
		$tax = array();
		preg_match_all( '/TAXONOMY=".+?"/', $template, $match, PREG_SET_ORDER );
		if ( is_array( $match ) && !empty( $match ) ) {
			foreach ( $match as $m ) {
				$_atts = shortcode_parse_atts( $m[0] );
				$tax[] = $_atts['taxonomy'];
			}
		}

		$tpl        = $template;
		// Comments.
		if ( post_type_supports( get_post_type( $post_id ), 'comments' ) ) {
			$comments = ( comments_open() || get_comments_number() ) ? get_comments_number() : '';
		}
		// get post title
		$title = get_the_title( $post_id );
		// get category list
		$taxonomy_name_list = ( $tax[0] == 'category' ) ? $this->get_taxonomy_list( $post_id, 'category' ) : $this->get_taxonomy_list( $post_id, 'tag' );
		// get post content
		$content = apply_filters( 'the_content', get_the_content('') );

		// get post meta
		$post_meta  = get_post_meta( $post_id, CHERRY_PORTFOLIO_POSTMETA, true );

		$gallery_type = isset( $post_meta['portfolio-gallery-type'] ) ? $post_meta['portfolio-gallery-type'] : 'slider' ;
		$video_type = isset( $post_meta['portfolio-video-type'] ) ? $post_meta['portfolio-video-type'] : false;
		$embed_video_src = isset( $post_meta['portfolio-embed-video-src'] ) ? $post_meta['portfolio-embed-video-src'] : false;

		$mp4_video_id = isset( $post_meta['portfolio-mp4-video-id'] ) ? $post_meta['portfolio-mp4-video-id'] : false;
		$webm_video_id = isset( $post_meta['portfolio-webm-video-id'] ) ? $post_meta['portfolio-webm-video-id'] : false;
		$ogv_video_id = isset( $post_meta['portfolio-ogv-video-id'] ) ? $post_meta['portfolio-ogv-video-id'] : false;
		$audio_id = isset( $post_meta['portfolio-audio-src'] ) ? $post_meta['portfolio-audio-src'] : false;
		//$standart_zoom_image = isset( $post_meta['portfolio-standart-zoom-image'] ) ? $post_meta['portfolio-standart-zoom-image'] : false;

		global $content_width;
		$tmp_content_width = $content_width;
		$content_width = 1170;

		switch ( $video_type ) {
			case 'portfolio-video-type-html5':
				$mp4_video_src =  wp_get_attachment_url( $mp4_video_id );
				$webm_video_src =  wp_get_attachment_url( $webm_video_id );
				$ogv_video_src =  wp_get_attachment_url( $ogv_video_id );
				$videoplayer = do_shortcode('[video mp4="' . $mp4_video_src . '" webm="' . $webm_video_src . '" ogv="' . $ogv_video_src . '" width="1170" height="720"]');
				break;
			case 'portfolio-video-type-embed':
				$videoplayer = sprintf( '<div class="embed-container">%1$s</div>', wp_oembed_get( $embed_video_src, array('width' => '100%') ) );
				break;
		}

		$content_width = $tmp_content_width;

		if ( isset( $post_meta['portfolio-gallery-attachments-ids'] ) ) {
			$attachments_ids_array = explode( ",", $post_meta['portfolio-gallery-attachments-ids'] );

			$slides_per_view = isset( $post_meta['portfolio-gallery-swiper-slides-per-view'] ) ? $post_meta['portfolio-gallery-swiper-slides-per-view'] : 1 ;
			$slides_per_column = isset( $post_meta['portfolio-gallery-swiper-slides-per-column'] ) ? $post_meta['portfolio-gallery-swiper-slides-per-column'] : 1 ;
			$space_between_slides = isset( $post_meta['portfolio-gallery-swiper-space-between'] ) ? $post_meta['portfolio-gallery-swiper-space-between'] : 10 ;
			$swiper_pagination = isset( $post_meta['portfolio-gallery-swiper-pagination'] ) ? $post_meta['portfolio-gallery-swiper-pagination'] : 'true' ;
			$swiper_navigation = isset( $post_meta['portfolio-gallery-swiper-navigation'] ) ? $post_meta['portfolio-gallery-swiper-navigation'] : 'true' ;
			$swiper_loop = isset( $post_meta['portfolio-gallery-swiper-loop'] ) ? $post_meta['portfolio-gallery-swiper-loop'] : 'true' ;
			$swiper_duration_speed = isset( $post_meta['portfolio-gallery-swiper-duration-speed'] ) ? $post_meta['portfolio-gallery-swiper-duration-speed'] : 300 ;
			$swiper_free_mode = isset( $post_meta['portfolio-gallery-swiper-free-mode'] ) ? $post_meta['portfolio-gallery-swiper-free-mode'] : 'false' ;
			$swiper_grab_cursor = isset( $post_meta['portfolio-gallery-swiper-grab-cursor'] ) ? $post_meta['portfolio-gallery-swiper-grab-cursor'] : 'true' ;
			$swiper_mouse_wheel = isset( $post_meta['portfolio-gallery-swiper-mouse-wheel'] ) ? $post_meta['portfolio-gallery-swiper-mouse-wheel'] : 'false' ;
			$swiper_crop_image = isset( $post_meta['portfolio-gallery-swiper-crop-image'] ) ? $post_meta['portfolio-gallery-swiper-crop-image'] : 'false' ;
			$swiper_crop_width = isset( $post_meta['portfolio-gallery-swiper-crop-width'] ) ? $post_meta['portfolio-gallery-swiper-crop-width'] : 1024 ;
			$swiper_crop_height = isset( $post_meta['portfolio-gallery-swiper-crop-height'] ) ? $post_meta['portfolio-gallery-swiper-crop-height'] : 576 ;
			$swiper_effect = isset( $post_meta['portfolio-gallery-swiper-effect'] ) ? $post_meta['portfolio-gallery-swiper-effect'] : 'slide' ;

			$thumbnail_classes = apply_filters('cherry-portfolio-gallery-image-classes', 'image-link');

			$slider_html = '';

				switch ( $gallery_type ) {
					case 'slider':
						$uniqId = 'swiper-carousel-' . uniqid() . '';
						$data_attr_line = '';
						$data_attr_line .= 'data-slides-per-view="' . $slides_per_view . '"';
						$data_attr_line .= 'data-slides-per-column="' . $slides_per_column . '"';
						$data_attr_line .= 'data-space-between-slides="' . $space_between_slides . '"';
						$data_attr_line .= 'data-duration-speed="' . $swiper_duration_speed . '"';
						$data_attr_line .= 'data-swiper-loop="' . $swiper_loop . '"';
						$data_attr_line .= 'data-free-mode="' . $swiper_free_mode . '"';
						$data_attr_line .= 'data-grab-cursor="' . $swiper_grab_cursor . '"';
						$data_attr_line .= 'data-mouse-wheel="' . $swiper_mouse_wheel . '"';
						$data_attr_line .= 'data-swiper-effect="' . $swiper_effect . '"';
						$data_attr_line .= 'data-uniq-id="' . $uniqId . '"';


						$slider_html .= '<div id="' . $uniqId . '" class="swiper-container" ' . $data_attr_line . ' >';
							$slider_html .= '<div class="swiper-wrapper">';
								foreach ( $attachments_ids_array as $attachment_id) {
									if ( $swiper_crop_image == 'false'){
										$attachment_url = wp_get_attachment_image_src( $attachment_id, 'large' );
										$slider_html .= '<div class="swiper-slide"><img class="swiper-slide-image" src="' . $attachment_url[0] . '" width="' . $attachment_url[1] . '" height="' . $attachment_url[2] . '" alt="' . get_the_title( $attachment_id ) . '"></div>';
									}else{
										$attachment_url = wp_get_attachment_image_src( $attachment_id, 'full' );
										$croped_image = $this->get_crop_image( $attachment_url[0], $attachment_id, $swiper_crop_width, $swiper_crop_height, 'swiper-slide-image', get_the_title( $attachment_id ) );
										$slider_html .= '<div class="swiper-slide">' . $croped_image . '</div>';
									}
								}
							$slider_html .= '</div>';
							if( 'true' == $swiper_pagination ){
								$slider_html .= '<div id="' . $uniqId . '-pagination" class="swiper-pagination"></div>';
							}
							if( 'true' == $swiper_navigation ){
								$slider_html .= '<div id="' . $uniqId . '-next" class="swiper-button-next"></div>';
								$slider_html .= '<div id="' . $uniqId . '-prev" class="swiper-button-prev"></div>';
							}
						$slider_html .= '</div>';
						break;
					case 'masonry':
						$data_attr_line = '';
						$data_attr_line = 'data-columns="' . apply_filters('cherry-portfolio-gallery-masonry-column', 3 ) . '"';
						$data_attr_line .= 'data-gutter="' . apply_filters('cherry-portfolio-gallery-masonry-gutter', 10 ) . '"';
						$slider_html .= '<section class="gallery-list masonry-list" ' . $data_attr_line . ' >';
							if( !empty($attachments_ids_array) ){
								foreach ( $attachments_ids_array as $attachment_id) {
									$slider_html .= '<section class="gallery-item masonry-item">';
										$attachment_image = wp_get_attachment_image_src( $attachment_id, 'large' );
										$slider_html .= sprintf('<a class="' . $thumbnail_classes . '" href="' . $attachment_image[0] .'" data-effect="mfp-zoom-in">%1$s<img class="masonry-image" src="' . $attachment_image[0] . '" width="' . $attachment_image[1] . '" height="' . $attachment_image[2] . '" alt="' . get_the_title( $attachment_id ) . '"></a>', '<span class="cover"></span>');
									$slider_html .= '</section>';
								}
							}
						$slider_html .= '</section>';
						break;
					case 'justified':
						$slider_html .= '<section class="gallery-list justified-list">';
							if( !empty($attachments_ids_array) ){
								foreach ( $attachments_ids_array as $attachment_id) {
									$attachment_image = wp_get_attachment_image_src( $attachment_id, 'large' );
									$image_ratio = $attachment_image[1] / $attachment_image[2];
									$justified_attrs = 'data-image-src="' . $attachment_image[0] . '"';
									$justified_attrs .= ' data-image-width="' . $attachment_image[1] . '"';
									$justified_attrs .= ' data-image-height="' . $attachment_image[2] . '"';
									$justified_attrs .= ' data-image-ratio="' . $image_ratio . '"';

									$slider_html .= '<section class="gallery-item justified-item" ' . $justified_attrs . '>';
										$slider_html .= sprintf('<a class="' . $thumbnail_classes . '" href="' . $attachment_image[0] .'" data-effect="mfp-zoom-in">%1$s<div class="justified-image"></div></a>', '<span class="cover"></span>');
									$slider_html .= '</section>';
								}
							}
						$slider_html .= '</section>';
						break;
				}

		}

		$image = $this->get_image( $post_id, 'large' );

		$full_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id) , 'full', false );

		if( 'post-format-image' === $format && 'true' === $post_meta['portfolio-image-format-crop-image'] ){
			$image = $this->get_crop_image( $full_image_url[0], get_post_thumbnail_id( $post_id), $post_meta['portfolio-image-format-crop-width'], $post_meta['portfolio-image-format-crop-height'] );
		}

		$audio_ids = explode(',', $audio_id);
		$audioplayer = '';
		foreach ($audio_ids as $value) {
			$audio_src = wp_get_attachment_url( $value );
			$audioplayer .= do_shortcode('[audio src="' . $audio_src . '"]');
		}

		$externallink = isset( $post_meta[ 'external-link-url' ] ) ? $post_meta[ 'external-link-url' ] : '' ;

		$figure_image = sprintf( '<figure class="post-featured-image"><a class="magnific-popup-zoom" href="%1$s">%2$s</a></figure>', $full_image_url[0], $image );
		$title_comments =  sprintf( _n( 'Comment', '%s comments', $comments, 'cherry-portfolio' ), $comments );
		$comments = ( !empty( $comments ) ) ? sprintf( '<span class="post-comments-link"><i class="dashicons dashicons-format-status"></i><a href="%1$s">%2$s</a></span>', esc_url( get_comments_link() ), $title_comments ) : __( 'No comments', 'cherry-portfolio' );
		$date = sprintf( '<time class="post-date" datetime="%1$s"><i class="dashicons dashicons-calendar-alt"></i>%2$s</time>', esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date( $date_format ) ) );
		$author = sprintf( '<span class="post-author vcard"><i class="dashicons dashicons-admin-users"></i>by <a href="%1$s" rel="author">%2$s</a></span>', esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), get_the_author() );
		$taxonomy_list_text = sprintf( '%s', $taxonomy_name_list );
		$videoplayer = ( isset($videoplayer) ) ? $videoplayer : '';
		$slider_html = ( isset( $slider_html ) ) ? $slider_html : '';
		$link_target = ( isset( $post_meta['external-link-target'] ) ) ? $post_meta['external-link-target'] : '_black';
		$externallink  = ( !empty( $externallink ) ) ? sprintf( '<a class="external-link-button" href="%1$s" target="%3$s"><span class="dashicons dashicons-admin-links"></span>%2$s</a>', $externallink, $post_meta['external-link-text'], $link_target ) : '';

		// Prepare a current post data array.
		$_postdata['title']    = $title;
		$_postdata['taxonomy']    = $taxonomy_list_text;
		$_postdata['content']    = $content;
		$_postdata['date']    = $date;
		$_postdata['author']    = $author;
		$_postdata['comments']    = $comments;
		$_postdata['videoplayer']    = $videoplayer;
		$_postdata['audioplayer']    = $audioplayer;
		$_postdata['image']    = $figure_image;
		$_postdata['slider']    = $slider_html;
		$_postdata['externallink']    = $externallink;

		/**
		 * Filters the array with a current post data.
		 *
		 * @since 1.0.0
		 * @param array  $_postdata Array with a current post data.
		 * @param int    $post_id   Post ID.
		 * @param array  $atts      Shortcode attributes.
		 */
		$_postdata = apply_filters( 'cherry-portfolio-standart-format-postdata', $_postdata, $post_id );

		// Init a `postdata` array.
		self::$postdata = $_postdata;

		$tpl = preg_replace_callback( "/%%.+?%%/", array( $this, 'replace_callback' ), $tpl );
		$output = '';
		$output .= $tpl;

		return $output;
	}

	/**
	 * Get taxonomy list.
	 *
	 * @since  1.0.0
	 * @param  int|string $args  post_id, taxonomy type(category).
	 * @return string.
	 */
	public function get_taxonomy_list( $post_id, $tax = 'category, tag' ) {
		$taxonomy_name_list = '';
		switch ($tax) {
			case 'category':
					$taxonomy_name_list .= '<span>' . __( 'Category: ', 'cherry-portfolio' ) . '</span>';
					$post_taxonomy = is_wp_error( get_the_terms($post_id, CHERRY_PORTFOLIO_NAME.'_category') ) ?'': get_the_terms($post_id, CHERRY_PORTFOLIO_NAME.'_category');
				break;
			case 'tag':
					$taxonomy_name_list .= '<span>' . __( 'Tags: ', 'cherry-portfolio' ) . '</span>';
					$post_taxonomy = is_wp_error( get_the_terms($post_id, CHERRY_PORTFOLIO_NAME.'_tag') ) ?'': get_the_terms($post_id, CHERRY_PORTFOLIO_NAME.'_tag');
				break;
		}

		$taxonomy_name_list = apply_filters( 'cherry-portfolio-taxonomy-name-list', $taxonomy_name_list );

		if( $post_taxonomy ){
			$count = 1;
				foreach ($post_taxonomy as $taxonomy => $taxonomy_value) {
					$taxonomy_name_list .= $taxonomy_value->name;
					( $count < count( $post_taxonomy ) ) ? $taxonomy_name_list .= ', ':'';
					$count++;
				}
			return $taxonomy_name_list;
		}else{
			return __( 'Post has no taxonomies', 'cherry-portfolio' );
		}
	}

	/**
	 * Get ajax filter fot list items.
	 *
	 * @since  1.0.0
	 * @param  string $arg taxonomy type(category).
	 * @return string.
	 */
	public function build_ajax_filter( $options ) {
		$html = '';

		$filter_type = $options['filter_type'];

		$args = array(
			'type'        => CHERRY_PORTFOLIO_NAME,
			'orderby'     => 'name',
			'order'       => 'ASC',
			'taxonomy'    => CHERRY_PORTFOLIO_NAME . '_' . $filter_type,
			'pad_counts'  => false
		);

		$order_array = array(
			'desc'	=> __('Desc', 'cherry-portfolio'),
			'asc'	=> __('Asc', 'cherry-portfolio'),
		);
		$order_by_array = array(
			'date'			=> __('Date', 'cherry-portfolio'),
			'name'			=> __('Name', 'cherry-portfolio'),
			'modified'		=> __('Modified', 'cherry-portfolio'),
			'comment_count'	=> __('Comments', 'cherry-portfolio'),
		);

		$categories = get_categories( $args );
		$tax_list = ( 'category' === $filter_type ) ? $options['category_list'] : $options['tag_list'];

		$html .= '<div class="portfolio-filter with-ajax" data-order-default="' . $options['order_filter_default'] . '" data-orderby-default="' . $options['orderby_filter_default'] . '">';
			$html .= apply_filters('cherry-portfolio-before-filters-html', '');
			$html .= '<ul class="filter filter-' . $filter_type . '">';
			if( $categories ){
				$html .= '<li class="active"><a href="javascript:void(0)" data-cat-id="" data-slug="">'. apply_filters( 'cherry_portfolio_show_all_text', __( 'Show all', 'cherry-portfolio' ) ) .'</a></li>';
				foreach( $categories as $category ){
					if( in_array($category->slug, $tax_list) || empty( $tax_list ) ){
						$html .= '<li><a href="javascript:void(0)" data-cat-id="' .  $category->cat_ID . '" data-slug="' .  $category->slug . '">'. $category->name .'</a></li>';
					}
				}
			}
			$html .= '</ul>';
			$html .= apply_filters('cherry-portfolio-after-filters-html', '');
			if( 'true' == self::$options['order_filter_visible'] ){
				$html .= '<ul class="order-filter">';
					$class = ( $options['order_filter_default'] == 'asc' ) ? 'class="dropdown-state"' : '' ;
					$html .= '<li data-order="order" data-desc-label="' . __('Desc', 'cherry-portfolio') . '" data-asc-label="' . __('Asc', 'cherry-portfolio') . '" ' . $class . '>';
						$html .= apply_filters( 'cherry-portfolio-order-filter-label', __('Order', 'cherry-portfolio') );
						$html .= '<span class="current">' . $order_array[ $options['order_filter_default'] ] . '</span>';
						$html .= '<span class="marker"></span>';
					$html .= '</li>';
					$html .= '<li data-orderby="orderby">';
						$html .=  apply_filters( 'cherry-portfolio-orderby-filter-label', __('Order by', 'cherry-portfolio') );
						$html .= '<span class="current">' . $order_by_array[ $options['orderby_filter_default'] ] . '</span>';
						$html .= '<ul class="orderby-list">';
							foreach ( $order_by_array as $key => $value ) {
								$class = ( $key == $options['orderby_filter_default'] ) ? 'class="active"' : '';
								$html .= '<li data-orderby="' . $key . '" ' . $class . '>' . $value . '</li>';
							}
						$html .= '</ul>';
					$html .= '</li>';
				$html .= '</ul>';
			}
			$html .= '<div class="clear"></div>';
		$html .= '</div>';
		return $html;
	}

	/**
	 * Get ajax pagination fot list items.
	 *
	 * @since  1.0.0
	 * @param  int $arg $current page index value.
	 * @return string(HTML-formatted).
	 */
	public function build_ajax_pagination( $current_page_index = 1, $post_per_page = -1 ) {
		if( -1 !== $post_per_page ){
			$page_count = intval( ceil( $this->posts_query->found_posts / $post_per_page ) );
			$dom_part = '';
				$dom_part .= '<div class="portfolio-pagination with-ajax">';
					if( $page_count > 1 ){
						$dom_part .= '<ul class="page-link">';
							for ($i=0; $i < $page_count; $i++) {
								$counter = $i+1;
								if( $i != $current_page_index-1 ){
									$dom_part .= '<li><a href="javascript:void(0)">' . $counter . '</a></li>';
								}else{
									$dom_part .= '<li class="active"><a href="javascript:void(0)">' . $counter . '</a></li>';
								}
							}
						$dom_part .= '</ul>';
						$dom_part .= '<div class="page-nav">';
							if( $current_page_index != 1){
								$dom_part .= '<a class="prev-page" href="javascript:void(0)">' . __( 'Prev page', 'cherry-portfolio' ) . '</a>';
							}
							if( $current_page_index < $page_count){
								$dom_part .= '<a class="next-page" href="javascript:void(0)">' . __( 'Next page', 'cherry-portfolio' ) . '</a>';
							}
						$dom_part .= '</div>';
					}
				$dom_part .= '<div class="clear"></div>';
				$dom_part .= '</div>';
			return $dom_part;
		}else{
			return '';
		}
	}

	/**
	 * Get ajax "more button" fot list items.
	 *
	 * @since  1.0.0
	 * @param  int $arg $current page index value.
	 * @return string(HTML-formatted).
	 */
	public function build_ajax_more_button( $current_page_index = 1, $post_per_page = -1 ){
		if( -1 !== $post_per_page ){
			$page_count = intval( ceil( $this->posts_query->found_posts / $post_per_page ) );
			$dom_part = '';
				if( $page_count > 1 ){
					$dom_part .= '<div class="portfolio-ajax-button">';
						$dom_part .= '<div class="load-more-button"><a href="javascript:void(0)">' . __( self::$default_options['more_button_label'], 'cherry-portfolio' )  . '</a></div>';
					$dom_part .= '</div>';
				}
			return $dom_part;
		}else{
			return '';
		}
	}

	/**
	 * Get post attached image.
	 *
	 * @since  1.0.0
	 * @param  int $id post id.
	 * @param  array, string
	 * @return string(HTML-formatted).
	 */
	public function get_image( $id, $size ) {
		$image = '';

		$attr = array(
			'class' => self::$default_options['image_class']
		);
		if ( has_post_thumbnail( $id ) ) {
			$image = get_the_post_thumbnail( intval( $id ), $size, $attr );
		}else{
			$image = false;
		}
		return $image;
	}

	/**
	 * Get placeholder image.
	 *
	 * @since  1.0.0
	 * @param  array, string
	 * @return string(HTML-formatted).
	 */
	public function get_placeholder( $args ) {
		$default_args = apply_filters( 'cherry_portfolio_placeholder_default_args',
			array(
				'width'			=> 500,
				'height'		=> 300,
				'background'	=> '000',
				'foreground'	=> 'fff',
				'title'			=> '',
				'class'			=> '',
			)
		);

		$args = wp_parse_args( $args, $default_args );

		$placeholder_link = 'http://fakeimg.pl/' . $args['width'] . 'x' . $args['height'] . '/'. $args['background'] .'/'. $args['foreground'] . '/?text=' . $args['title'] . '';
		$image = '<img class="' . $args['class'] . '" src="' . $placeholder_link . '" alt="" title="' . $args['title'] . '">';
		return $image;
	}

	/**
	 * Get cropped image.
	 *
	 * @since  1.0.0
	 * @param  string|int|int|string|string $args image url, cropped width value, cropped height value, custom class name, image alt name.
	 * @return string(HTML-formatted).
	 */
	public function get_crop_image( $img_url = '', $attachment_id = null, $width = 100, $height = 100, $custom_class = "", $alt_value="" ) {
		// check if $attachment_id exist
		if($attachment_id == null){
			return false;
		}

		$image = '';
		//resize & crop img
		$croped_image_url = aq_resize( $img_url, $width, $height, true );

		if( !$croped_image_url ){
			$croped_image_url = $img_url;
		}

		// get $pathinfo
		$pathinfo = pathinfo( $croped_image_url );
		//get $attachment metadata
		$attachment_metadata = wp_get_attachment_metadata( $attachment_id );
		// create new custom size
		$attachment_metadata['sizes']['croped-image-' . $width . '-' . $height] = array(
			'file'			=> $pathinfo['basename'],
			'width'			=> $width,
			'height'		=> $height,
			'mime-type'		=> get_post_mime_type($attachment_id)
		);
		// wp update attachment metadata
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		$ratio_value = $height / $width;
		$image .= '<img class="image croped-image ' . $custom_class . '" data-ratio="' . $ratio_value . '" width="' . $width . '" height="' . $height .'" src="' . $croped_image_url . '" alt="'. $alt_value .'">';
		return $image;
	}

	/**
	 * Get option by name from theme options
	 *
	 * @since  1.0.0
	 *
	 * @uses   cherry_get_option  use cherry_get_option from Cherry framework if exist
	 *
	 * @param  string  $name    option name to get
	 * @param  mixed   $default default option value
	 * @return mixed            option value
	 */
	public static function cherry_portfolio_get_option( $name , $default = false ) {
		if ( function_exists( 'cherry_get_option' ) ) {
			$result = cherry_get_option( $name , $default );
		return $result;
		}
		return $default;
	}

	/**
	 * Callback-function for `preg_replace_callback`.
	 *
	 * @since  1.0.0
	 * @param  array|null $matches
	 * @return string
	 */
	public static function replace_callback( $matches ) {
		if ( !is_array( $matches ) ) {
			return '';
		}
		if ( empty( $matches ) ) {
			return '';
		}
		$key = strtolower( trim( $matches[0], '%%' ) );
		$pos = strpos( $key, '=' );
		if ( false !== $pos ) {
			$_key = explode( '=', $key );
			$key1 = $_key[0];
			$key2 = trim( $_key[1], '"' );
			if ( !isset( self::$postdata[ $key1 ] ) ) {
				return '';
			}
			if ( is_array( self::$postdata[ $key1 ] ) ) {
				if ( !isset( self::$postdata[ $key1 ][ $key2 ] ) ) {
					return '';
				}
				return self::$postdata[ $key1 ][ $key2 ];
			}
			return self::$postdata[ $key1 ];
		}
		return self::$postdata[ $key ];
	}


	/**
	 * Retrieve a template's file content.
	 *
	 * @since  1.0.0
	 * @param  string $template_name  Template's file name.
	 * @param  string $shortcode      Shortcode's name.
	 * @return bool|string            Template's content.
	 */
	public static function get_template_path( $template_name, $shortcode ) {
		$file    = false;
		$subdir  = 'templates/shortcodes/' . $shortcode . '/' . $template_name;
		$default = CHERRY_PORTFOLIO_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $subdir ) ) {
			$file = trailingslashit( get_stylesheet_directory() ) . $subdir;
		} elseif ( file_exists( CHERRY_PORTFOLIO_DIR . $subdir ) ) {
			$file = CHERRY_PORTFOLIO_DIR . $subdir;
		} elseif ( file_exists( $default ) ) {
			$file = $default;
		}
		$file = apply_filters( 'cherry_shortcodes_get_template_path', $file, $template_name, $shortcode );
		return $file;

	}

	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( !function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		if ( !$wp_filesystem->exists( $template ) ) { // Check for existence.
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( !$content ) {
			return new WP_Error( 'reading_error', 'Error when reading file' ); // Return error object.
		}

		return $content;
	}

	public static function get_template_by_name( $template, $shortcode ) {
		$file       = '';
		$default    = CHERRY_PORTFOLIO_DIR . 'templates/shortcodes/' . $shortcode . '/default.tmpl';
		$upload_dir = wp_upload_dir();
		$upload_dir = trailingslashit( $upload_dir['basedir'] );
		$subdir     = 'templates/shortcodes/' . $shortcode . '/' . $template;

		$content = apply_filters( 'cherry_testimonials_fallback_template', '%%avatar%%<blockquote>%%content%% %%author%%</blockquote>' );

		if ( file_exists( $upload_dir . $subdir ) ) {
			$file = $upload_dir . $subdir;
		} elseif ( file_exists( CHERRY_PORTFOLIO_DIR . $subdir ) ) {
			$file = CHERRY_PORTFOLIO_DIR . $subdir;
		} else {
			$file = $default;
		}

		if ( !empty( $file ) ) {
			$content = self::get_contents( $file );
		}

		return $content;
	}

	/**
	 * Restores the static $postdata.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public static function reset_postdata() {
		self::$postdata = array();
	}

}

add_action( 'wp_ajax_get_new_items', 'get_new_items' );
add_action( 'wp_ajax_nopriv_get_new_items', 'get_new_items' );

function get_new_items() {
	if (
		!empty($_POST)
		&& array_key_exists('value_pagination_page', $_POST)
		&& array_key_exists('value_slug', $_POST)
		&& array_key_exists('post_per_page', $_POST)
		&& array_key_exists('loading_mode', $_POST)
		&& array_key_exists('list_layout', $_POST)
		&& array_key_exists('order_settings', $_POST)
		&& array_key_exists('template', $_POST)
		&& array_key_exists('posts_format', $_POST)
		) {
		$value_slug = $_POST['value_slug'];
		$value_pagination_page = $_POST['value_pagination_page'];
		$post_per_page = $_POST['post_per_page'];
		$loading_mode = $_POST['loading_mode'];
		$list_layout = $_POST['list_layout'];
		$order_settings = $_POST['order_settings'];
		$template = $_POST['template'];
		$posts_format = $_POST['posts_format'];

		($value_slug !== 'all') ? $_POST['value_slug'] : $value_slug = '';

		$data = new Cherry_Portfolio_Data;

		$query_args = array(
			( Cherry_Portfolio_Data::$default_options['filter_type'] == 'category' ) ? CHERRY_PORTFOLIO_NAME.'_category' : CHERRY_PORTFOLIO_NAME.'_tag' => $value_slug,
			'posts_per_page' => $post_per_page,
			'order' => $order_settings['order'],
			'orderby' => $order_settings['orderby'],
			'paged' => $value_pagination_page,
		);

		if( 'post-format-all' !== $posts_format ){
			$terms = array( $posts_format );
			$operator = 'IN';

			if( 'post-format-standard' == $posts_format ){
				$terms = array( 'post-format-gallery', 'post-format-image', 'post-format-audio', 'post-format-video');
				$operator = 'NOT IN';
			}

			$query_args['tax_query'] = array(
				array(
					'taxonomy'	=> 'post_format',
					'field'		=> 'slug',
					'terms'		=> $terms,
					'operator'	=> $operator,
				)
			);
		}

		$posts_query =  $data->get_query_portfolio_items( $query_args );

		$html = '';
		$html .= '<div class="response">';
			$html .= '<div class="portfolio-list " data-all-posts-count="' . $posts_query->found_posts . '">';
				$html .= $data->get_portfolio_items_loop( $posts_query, $list_layout, $template );
			$html .= '</div>';

			switch ( $loading_mode ) {
				case 'ajax-pagination':
						$html .= $data->build_ajax_pagination( $posts_query->query_vars['paged'], $post_per_page );
					break;
				case 'more-button':
						$html .= $data->build_ajax_more_button( $posts_query->query_vars['paged'], $post_per_page );
					break;
				case 'none':
						$html .= '';
					break;
			}
		$html .= '</div>';
		echo $html;
		exit;
	}
}


add_action( 'wp_ajax_get_more_items', 'get_more_items' );
add_action( 'wp_ajax_nopriv_get_more_items', 'get_more_items' );

function get_more_items() {
	if (
		!empty($_POST)
		&& array_key_exists('value_pagination_page', $_POST)
		&& array_key_exists('value_slug', $_POST)
		&& array_key_exists('post_per_page', $_POST)
		&& array_key_exists('list_layout', $_POST)
		&& array_key_exists('order_settings', $_POST)
		&& array_key_exists('template', $_POST)
		&& array_key_exists('posts_format', $_POST)
		) {
		$value_pagination_page = $_POST['value_pagination_page'];
		$value_slug = $_POST['value_slug'];
		$post_per_page = $_POST['post_per_page'];
		$list_layout = $_POST['list_layout'];
		$order_settings = $_POST['order_settings'];
		$template = $_POST['template'];
		$posts_format = $_POST['posts_format'];

		$data = new Cherry_Portfolio_Data;
		$query_args = array(
			( Cherry_Portfolio_Data::$default_options['filter_type'] == 'category' ) ? CHERRY_PORTFOLIO_NAME.'_category' : CHERRY_PORTFOLIO_NAME.'_tag' => $value_slug,
			'posts_per_page' => $post_per_page,
			'order' => $order_settings['order'],
			'orderby' => $order_settings['orderby'],
			'paged' => intval( $value_pagination_page ),
		);

		if( 'post-format-all' !== $posts_format ){
			$terms = array( $posts_format );
			$operator = 'IN';

			if( 'post-format-standard' == $posts_format ){
				$terms = array( 'post-format-gallery', 'post-format-image', 'post-format-audio', 'post-format-video');
				$operator = 'NOT IN';
			}

			$query_args['tax_query'] = array(
				array(
					'taxonomy'	=> 'post_format',
					'field'		=> 'slug',
					'terms'		=> $terms,
					'operator'	=> $operator,
				)
			);
		}

		$posts_query =  $data->get_query_portfolio_items( $query_args );

		$html = '<div class="response" data-all-posts-count="' . $posts_query->found_posts . '">';
			$html .= $data->get_portfolio_items_loop( $posts_query, $list_layout, $template );
		$html .= '</div>';

		echo $html;
		exit;
	}
}