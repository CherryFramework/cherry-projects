<?php
/**
 * Define callback functions for templater.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-3.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2012 - 2015, Cherry Team
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Callbacks for Projects shortcode templater.
 *
 * @since 1.0.0
 */
class Cherry_Projects_Template_Callbacks {

	/**
	 * Shortcode attributes array.
	 *
	 * @var array
	 */
	public $atts = array();

	/**
	 * Current post meta.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $post_meta = null;

	/**
	 * Term data meta.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $term_data = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 * @param array $atts Set of attributes.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * Get post meta.
	 *
	 * @since 1.1.0
	 */
	public function get_meta() {
		if ( null === $this->post_meta ) {
			global $post;
			$this->post_meta = get_post_meta( $post->ID, '', true );
		}

		return $this->post_meta;
	}

	/**
	 * Clear post data after loop iteration.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function clear_data() {
		$this->post_meta = null;
	}

	/**
	 * Set current term data.
	 *
	 * @since  1.0.0
	 */
	public function set_term_data( $term_data = null ) {
		$this->term_data = $term_data;
	}

	/**
	 * Clear current term data.
	 *
	 * @since  1.0.0
	 */
	public function clear_term_data() {
		$this->term_data = null;
	}

	/**
	 * Get post title.
	 *
	 * @since 1.0.0
	 */
	public function get_title( $attr = array() ) {

		$default_attr = array( 'number_of_words' => 10 );

		$attr = wp_parse_args( $attr, $default_attr );

		$html = '<h3 %1$s><a href="%2$s" %3$s rel="bookmark">%4$s</a></h3>';

		if ( is_single() ) {
			$html = '<h3 %1$s>%4$s</h3>';
		}

		$settings = array(
			'visible'		=> true,
			'length'		=> $attr['number_of_words'],
			'trimmed_type'	=> 'word',
			'ending'		=> '&hellip;',
			'html'			=> $html,
			'class'			=> '',
			'title'			=> '',
			'echo'			=> false,
		);

		/**
		 * Filter post title settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-title-settings', $settings );

		$title = cherry_projects()->projects_data->cherry_utility->attributes->get_title( $settings );

		return $title;
	}

	/**
	 * Get post title.
	 *
	 * @since 1.0.0
	 */
	public function get_featured_image( $attr = array() ) {
		global $post;

		$default_attr = array( 'size' => 'large', 'crop' => 'false', 'crop_width' => '500', 'crop_height' => '350' );

		$attr = wp_parse_args( $attr, $default_attr );

		$attachment_id = get_post_thumbnail_id();
		$image_src = $this->get_image_src_by_id( $attachment_id, $attr['size'] );
		$image_html = '<figure class="featured-image"><a href="' . $image_src . '" %2$s><span class="cover"></span><img src="%3$s" alt="%4$s" %5$s></a></figure>';

		if ( filter_var( $attr['crop'], FILTER_VALIDATE_BOOLEAN ) ) {
			$image_width = (int)$attr['crop_width'];
			$image_height = (int)$attr['crop_height'];

			$image_tag = $this->get_cropped_image_url( $attachment_id, 'full', $image_width, $image_height );

			$image_html = '<figure class="featured-image"><a href="' . $image_src . '" %2$s><span class="cover"></span>' . $image_tag . '</a></figure>';
		}

		$settings = array(
			'visible'                => true,
			'size'                   => $attr['size'],
			'mobile_size'            => apply_filters( 'cherry_mobile_image_size', 'post-thumbnail' ),
			'html'                   => $image_html,
			'class'                  => 'wp-image',
			'placeholder'            => true,
			'placeholder_background' => '000',
			'placeholder_foreground' => 'fff',
			'placeholder_title'      => '',
			'html_tag_suze'          => true,
			'echo'                   => false,
		);

		/**
		 * Filter post featured image settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-featured-image-settings', $settings );

		$image = cherry_projects()->projects_data->cherry_utility->media->get_image( $settings );

		return $image;
	}

	/**
	 * Get post content.
	 *
	 * @since 1.0.0
	 */
	public function get_content( $attr = array() ) {
		global $post;

		$default_attr = array( 'number_of_words' => -1, 'ending' => '&hellip;' );

		$attr = wp_parse_args( $attr, $default_attr );

		$settings = array(
			'visible'		=> true,
			'content_type'	=> 'post_content',
			'length'		=> (int)$attr['number_of_words'],
			'trimmed_type'	=> 'word',
			'ending'		=> $attr['ending'],
			'html'			=> '<p %1$s>%2$s</p>',
			'class'			=> '',
			'echo'			=> false,
		);

		/**
		 * Filter post content settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-content-settings', $settings );

		if ( ! is_single() ) {
			$content = cherry_projects()->projects_data->cherry_utility->attributes->get_content( $settings );
		} else {
			ob_start();
			the_content( '' );
			$content = ob_get_contents();
			ob_end_clean();
		}

		return $content;
	}

	/**
	 * Get post button.
	 *
	 * @since 1.0.0
	 */
	public function get_button( $attr = array() ) {

		$settings = array(
			'visible'	=> true,
			'text'		=> esc_html__( 'More', 'cherry-projects' ),
			'icon'		=> '',
			'html'		=> '<a href="%1$s" %2$s %3$s><span class="btn__text">%4$s</span>%5$s</a>',
			'class'		=> 'more-button',
			'title'		=> '',
			'echo'		=> false,
		);

		/**
		 * Filter post button settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-button-settings', $settings );

		$button = cherry_projects()->projects_data->cherry_utility->attributes->get_button( $settings );

		return $button;
	}

	/**
	 * Get post date.
	 *
	 * @since 1.0.0
	 */
	public function get_date( $attr = array() ) {

		$default_attr = array( 'format' => 'F, j Y', 'human_time' => false );

		$attr = wp_parse_args( $attr, $default_attr );

		$settings = array(
			'visible'		=> true,
			'icon'			=> '',
			'prefix'		=> '',
			'html'			=> '%1$s<a href="%2$s" %3$s %4$s ><time datetime="%5$s" title="%5$s">%6$s%7$s</time></a>',
			'title'			=> '',
			'class'			=> 'post-date',
			'date_format'	=> $attr['format'],
			'human_time'	=> filter_var( $attr['human_time'] , FILTER_VALIDATE_BOOLEAN ),
			'echo'			=> false,
		);

		/**
		 * Filter post date settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-date-settings', $settings );

		$date = cherry_projects()->projects_data->cherry_utility->meta_data->get_date( $settings );

		return $date;
	}

	/**
	 * Get post author.
	 *
	 * @since 1.0.0
	 */
	public function get_author( $attr = array() ) {

		$default_attr = array( 'prefix' => esc_html__( 'Posted by ', 'cherry-projects' ) );

		$attr = wp_parse_args( $attr, $default_attr );

		$settings = array(
			'visible'	=> 'true',
			'icon'		=> '',
			'prefix'	=> $attr['prefix'],
			'html'		=> '%1$s<a href="%2$s" %3$s %4$s rel="author">%5$s%6$s</a>',
			'title'		=> '',
			'class'		=> 'post-author',
			'echo'		=> false,
		);

		/**
		 * Filter post author settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-author-settings', $settings );

		$author = cherry_projects()->projects_data->cherry_utility->meta_data->get_author( $settings );

		return $author;
	}

	/**
	 * Get post comments.
	 *
	 * @since 1.0.0
	 */
	public function get_comments( $attr = array() ) {

		$default_attr = array( 'prefix' => esc_html__( 'Posted by ', 'cherry-projects' ) );

		$attr = wp_parse_args( $attr, $default_attr );

		$settings = array(
			'visible'		=> true,
			'icon'			=> '',
			'prefix'		=> $attr['prefix'],
			'sufix'			=> _n_noop( '%s comment', '%s comments', 'cherry-projects' ),
			'html'			=> '%1$s<a href="%2$s" %3$s %4$s>%5$s%6$s</a>',
			'title'			=> '',
			'class'			=> 'post-comments-count',
			'echo'			=> false,
		);

		/**
		 * Filter post comments settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-comments-settings', $settings );

		$comment_count = cherry_projects()->projects_data->cherry_utility->meta_data->get_comment_count( $settings );

		return $comment_count;
	}

	/**
	 * Get post zoom link button.
	 *
	 * @since 1.0.0
	 */
	public function get_zoom_link( $attr = array() ) {
		$default_attr = array( 'text_visible' => false );

		$attr = wp_parse_args( $attr, $default_attr );

		/**
		 * Filter zoom link text.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$zoom_link_text = apply_filters( 'cherry-projects-zoom-link-text', esc_html__( 'Zoom', 'cherry-projects' ) );

		$icon_content = ( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? '<span>' . $zoom_link_text . '</span>' : '<span class="dashicons dashicons-search"></span>';

		$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		$image_src = $this->get_image_src_by_id( $thumbnail_id, 'large' );

		$html = sprintf( '<a class="zoom-link %3$s" href="%1$s">%2$s</a>',
			$image_src,
			$icon_content,
			( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? 'simple-button' :'simple-icon' );

		return $html;
	}

	/**
	 * Get post external link button.
	 *
	 * @since 1.0.0
	 */
	public function get_external_link( $attr = array() ) {
		$default_attr = array( 'text_visible' => false );

		$attr = wp_parse_args( $attr, $default_attr );

		$external_link_text = get_post_meta( get_the_ID(), 'cherry_projects_external_link_text', true );
		$external_link = get_post_meta( get_the_ID(), 'cherry_projects_external_link', true );
		$external_target = get_post_meta( get_the_ID(), 'cherry_projects_external_link_target', true );

		$html = '';

		if ( ! empty( $external_link ) ) {
			$icon_content = ( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? '<span>' . $external_link_text . '</span>' : '<span class="dashicons dashicons-admin-site"></span>';

			$html = sprintf( '<a class="external-link %3$s" href="%1$s" target="%4$s">%2$s</a>',
				! empty( $external_link ) ? $external_link : '#',
				$icon_content,
				( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? 'simple-button' :'simple-icon',
				! empty( $external_target ) ? '_' . $external_target : '_blank'
			);
		}

		return $html;
	}

	/**
	 * Get post permalink button.
	 *
	 * @since 1.0.0
	 */
	public function get_permalink( $attr = array() ) {
		$default_attr = array( 'text_visible' => false );

		$attr = wp_parse_args( $attr, $default_attr );

		/**
		 * Filter permalink text.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$zoom_link_text = apply_filters( 'cherry-projects-permalink-text', esc_html__( 'Permalink', 'cherry-projects' ) );

		$icon_content = ( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? '<span>' . $zoom_link_text . '</span>' : '<span class="dashicons dashicons-admin-links"></span>';

		$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		$permalink = cherry_projects()->projects_data->cherry_utility->satellite->get_post_permalink();

		$html = sprintf( '<a class="permalink %3$s" href="%1$s">%2$s</a>',
			$permalink,
			$icon_content,
			( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? 'simple-button' :'simple-icon' );

		return $html;
	}

	/**
	 * Get post termslist.
	 *
	 * @since 1.0.0
	 */
	public function get_terms_list( $attr = array() ) {

		$default_attr = array( 'delimiter' => ', ', 'type' => CHERRY_PROJECTS_NAME .'_category' );

		$attr = wp_parse_args( $attr, $default_attr );

		$type = ( 'category' === cherry_projects()->projects_data->default_options['filter-type'] ) ? CHERRY_PROJECTS_NAME .'_category' : CHERRY_PROJECTS_NAME .'_tag';

		$settings = array(
			'visible'	=> true,
			'type'		=> $attr['type'],
			'icon'		=> '',
			'prefix'	=> '',
			'delimiter'	=> $attr['delimiter'],
			'before'	=> '<div class="post-terms">',
			'after'		=> '</div>',
			'class'		=> 'post-term',
			'echo'		=> false,
		);

		/**
		 * Filter post terms list settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$settings = apply_filters( 'cherry-projects-terms-list-settings', $settings );

		$termslist = cherry_projects()->projects_data->cherry_utility->meta_data->get_terms( $settings );

		return $termslist;
	}

	/**
	 * Get post details list.
	 *
	 * @since 1.0.0
	 */
	public function get_details_list( $attr = array() ) {
		$default_attr = array( 'delimiter' => ': ' );

		$attr = wp_parse_args( $attr, $default_attr );

		$details_list = get_post_meta( get_the_ID(), 'cherry_projects_details', true );

		$html = '<div class="cherry-projects-single-details-list">';
			/**
			 * Filter post terms list settings.
			 *
			 * @since 1.0.0
			 * @var array
			 */
			$details_list_text = apply_filters( 'cherry-projects-details-list-text', esc_html__( 'Project details', 'cherry-projects' ) );

			if ( is_array( $details_list ) && !empty( $details_list ) ) {
				if ( ! empty( $details_list_text ) ) {
					$html .= '<h4 class="cherry-projects-details-list-title">' . $details_list_text . '</h4>';
				}
				$html .= '<ul>';
					foreach ( $details_list as $item => $item_info ) {
						if ( ! empty( $details_list[ $item ]['detail_label'] ) ) {
							$html .= sprintf( '<li class="%1$s"><span>%2$s%3$s</span>%4$s</li>',
								$item,
								$details_list[ $item ]['detail_label'],
								$attr['delimiter'],
								$details_list[ $item ]['detail_info']
							);
						}
					}
				$html .= '</ul>';
			}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get post skills list.
	 *
	 * @since 1.0.0
	 */
	public function get_skills_list( $attr = array() ) {
		$default_attr = array( 'unit' => '%' );

		$attr = wp_parse_args( $attr, $default_attr );

		$skills_list = get_post_meta( get_the_ID(), 'cherry_projects_skills', true );

		$html = '<div class="cherry-projects-single-skills-list">';
			if ( is_array( $skills_list ) && !empty( $skills_list ) ) {
				$html .= '<ul>';
					foreach ( $skills_list as $item => $item_info ) {
						if ( ! empty( $skills_list[ $item ]['skill_label'] ) ) {
							$html .= sprintf( '<li class="cherry-skill-item %1$s"><div class="skill-label">%2$s</div><div class="skill-bar" data-skill-value="%3$s"><span><em>%4$s</em></span></div></li>',
								$item,
								$skills_list[ $item ]['skill_label'],
								$skills_list[ $item ]['skill_value'],
								$skills_list[ $item ]['skill_value'] . $attr['unit']
							);
						}
					}
				$html .= '</ul>';
			}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get post image list.
	 *
	 * @since 1.0.0
	 */
	public function get_image_list( $attr = array() ) {
		$default_attr = array(
			'size'        => 'large',
			'crop'        => 'false',
			'crop_width'  => '500',
			'crop_height' => '350'
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$attachments_list = explode( ',', get_post_meta( get_the_ID(), 'cherry_projects_image_attachments_ids', true ) );
		$listing_layout = get_post_meta( get_the_ID(), 'cherry_projects_listing_layout', true );
		$column_number = get_post_meta( get_the_ID(), 'cherry_projects_column_number', true );
		$image_margin = get_post_meta( get_the_ID(), 'cherry_projects_image_margin', true );

		$html = '<div class="cherry-projects-additional-image-list">';
			if ( is_array( $attachments_list) && ! empty( $attachments_list ) ) {
				$html .= sprintf( '<ul class="additional-image-list %1$s" data-listing-layout="%1$s" data-column-number="%2$s" data-image-margin="%3$s">',
					isset( $listing_layout ) ? $listing_layout : 'grid-layout',
					isset( $column_number ) ? $column_number : 3,
					isset( $image_margin ) ? $image_margin : 4
				);

				foreach ( $attachments_list as $attachment_id ) {
					$image_html = '<figure class="additional-image"><a href="%1$s" %2$s ><span class="cover"></span><img src="%3$s" alt="%4$s" %5$s ></a></figure>';

					if ( filter_var( $attr['crop'], FILTER_VALIDATE_BOOLEAN ) && 'grid-layout' === $listing_layout ) {
						$image_width = (int)$attr['crop_width'];
						$image_height = (int)$attr['crop_height'];
						$image_tag = $this->get_cropped_image_url( $attachment_id, 'full', $image_width, $image_height );
						$image_html = '<figure class="additional-image"><a href="%1$s" %2$s ><span class="cover"></span>' . $image_tag . '</a></figure>';
					}
					$settings = array(
						'visible'                => true,
						'size'                   => $attr['size'],
						'html'                   => $image_html,
						'class'                  => 'wp-image',
						'placeholder'            => true,
						'placeholder_background' => '000',
						'placeholder_foreground' => 'fff',
						'placeholder_title'      => '',
						'html_tag_suze'          => true,
						'echo'                   => false,
					);

					$html .= '<li class="image-item"><div class="inner-wrapper">' . cherry_projects()->projects_data->cherry_utility->media->get_image( $settings, 'attachment', $attachment_id ) . '</div></li>';
				}
				$html .= '</ul>';
			}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get post image list.
	 *
	 * @since 1.0.0
	 */
	public function get_slider( $attr = array() ) {
		$default_attr = array(
			'size'              => 'large',
			'width'             => '100%',
			'height'            => '800',
			'distance'          => '10',
			'duration'          => '500',
			'autoplay'          => false,
			'thumbnails'        => true,
			'thumbnails-width'  => '100',
			'thumbnails-height' => '100',
			'scale-mode'        => 'contain',
			'force-size'        => 'none',
			'visible-size'      => '100%',
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$attachments_list           = explode( ',', get_post_meta( get_the_ID(), 'cherry_projects_slider_attachments_ids', true ) );
		$uniq_id = 'slider-pro-' . uniqid();
		$slider_navigation          = get_post_meta( get_the_ID(), 'cherry_projects_slider_navigation', true );
		$slider_loop                = get_post_meta( get_the_ID(), 'cherry_projects_slider_loop', true );
		$slider_thumbnails_position = get_post_meta( get_the_ID(), 'cherry_projects_slider_thumbnails_position', true );

		$settings = array(
			'id'                  => $uniq_id,
			'width'               => $attr['width'],
			'height'              => $attr['height'],
			'navigation'          => filter_var( $slider_navigation, FILTER_VALIDATE_BOOLEAN ),
			'loop'                => filter_var( $slider_loop, FILTER_VALIDATE_BOOLEAN ),
			'thumbnails-position' => !empty( $slider_thumbnails_position ) ? $slider_thumbnails_position : 'bottom',
			'thumbnails-width'    => $attr['thumbnails-width'],
			'thumbnails-height'   => $attr['thumbnails-height'],
			'distance'            => $attr['distance'],
			'duration'            => $attr['duration'],
			'autoplay'            => $attr['autoplay'],
			'scale-mode'          => $attr['scale-mode'],
			'force-size'          => $attr['force-size'],
			'visible-size'        => $attr['visible-size'],
		);

		$settings = json_encode( $settings );

		if ( is_array( $attachments_list) && ! empty( $attachments_list ) ) {
			$html = sprintf( '<div class="cherry-projects-slider__instance" data-settings=\'%1$s\'>', $settings );
				$html .= '<div id="' . $uniq_id . '" class="slider-pro">';
					$html .= '<div class="projects-slider__items sp-slides">';
						foreach ( $attachments_list as $attachment_id ) {
							$html .= '<div class="projects-slider__item sp-slide">';
								$settings = array(
									'visible'                => true,
									'size'                   => $attr['size'],
									'html'                   => '<img %2$s src="%3$s" alt="%4$s" %5$s >',
									'class'                  => 'sp-image',
									'placeholder'            => true,
									'placeholder_background' => '000',
									'placeholder_foreground' => 'fff',
									'placeholder_title'      => '',
									'html_tag_suze'          => true,
									'echo'                   => false,
								);
								$html .=  cherry_projects()->projects_data->cherry_utility->media->get_image( $settings, 'attachment', $attachment_id );
							$html .= '</div>';
						}
					$html .= '</div>';

					if ( filter_var( $attr['thumbnails'], FILTER_VALIDATE_BOOLEAN ) ) {
						$html .= '<div class="projects-slider__thumbnails sp-thumbnails">';

							foreach ( $attachments_list as $attachment_id ) {
								$image_width = (int)$attr['thumbnails-width'];
								$image_height = (int)$attr['thumbnails-height'];
								$image_html = $this->get_cropped_image_url( $attachment_id, 'full', $image_width, $image_height );

								$html .= '<div class="sp-thumbnail">';
									$settings = array(
										'visible'                => true,
										'size'                   => 'post-thumbnail',
										'html'                   => $image_html,
										'placeholder'            => true,
										'placeholder_background' => '000',
										'placeholder_foreground' => 'fff',
										'placeholder_title'      => '',
										'html_tag_suze'          => true,
										'echo'                   => false,
									);

									$html .=  cherry_projects()->projects_data->cherry_utility->media->get_image( $settings, 'attachment', $attachment_id );
								$html .= '</div>';
							}

						$html .= '</div>';
					}
				$html .= '</div>';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Get videolist.
	 *
	 * @since 1.0.0
	 */
	public function get_video_list( $attr = array() ) {
		$default_attr = array(
			'width'  => 800,
			'height' => 600,
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$video_list_data = get_post_meta( get_the_ID(), 'cherry_projects_video_list', true );

		$html = '<div class="cherry-projects-video-list">';
			if ( ! empty( $video_list_data ) ) {
				foreach ( $video_list_data as $item => $video_settings ) {

					if ( empty( $video_settings['video_type'] ) || 'embed' === $video_settings['video_type'] ) {
						$html .= wp_oembed_get(
							$video_settings['video_embed'],
							array(
								'width'  => $attr['width'],
								'height' => $attr['height'],
							)
						);
					} else {
						$shortcode_attr = array(
							'width'  => $attr['width'],
							'height' => $attr['height'],
							'src'    => wp_get_attachment_url( $video_settings['video_src'] ),
							'poster' => wp_get_attachment_image_url( $video_settings['poster_src'], 'large' ),
						);

						$html .= '<div class="video-item">';
							$html .= wp_video_shortcode( $shortcode_attr );
						$html .= '</div>';
					}
				}
			}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Get audio list.
	 *
	 * @since 1.0.0
	 */
	public function get_audio_list( $attr = array() ) {
		$default_attr = array(
			'width'  => 800,
			'height' => 600,
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$audio_list_data = get_post_meta( get_the_ID(), 'cherry_projects_audio_attachments_ids', true );

		$html = '<div class="cherry-projects-audio-list">';

			$attr = array(
				'type'  => 'audio',
				'ids'   => $audio_list_data,
			);

			$html .=  wp_playlist_shortcode( $attr );

		$html .= '</div>';

		return $html;
	}

	/**
	 * Get term image
	 *
	 * @since 1.0.0
	 */
	public function get_term_image( $attr = array() ) {
		$default_attr = array( 'size' => 'large', 'crop' => 'false', 'crop_width' => '500', 'crop_height' => '350' );

		$attr = wp_parse_args( $attr, $default_attr );

		$attachment_id = get_term_meta( $this->term_data->term_id, 'cherry_terms_thumbnails' , true );
		$image_src = $this->get_image_src_by_id( $attachment_id, $attr['size'] );
		$image_html = '<figure class="featured-image"><a href="' . $image_src . '" %2$s><span class="cover"></span><img src="%3$s" alt="%4$s" %5$s></a></figure>';

		if ( filter_var( $attr['crop'], FILTER_VALIDATE_BOOLEAN ) ) {
			$image_width = (int)$attr['crop_width'];
			$image_height = (int)$attr['crop_height'];

			$image_tag = $this->get_cropped_image_url( $attachment_id, 'full', $image_width, $image_height );

			$image_html = '<figure class="featured-image"><a href="' . $image_src . '" %2$s><span class="cover"></span>' . $image_tag . '</a></figure>';
		}

		$html = cherry_projects()->projects_data->cherry_utility->media->get_image(
			array(
				'html'  => $image_html,
				'class' => 'term-img',
				'size'  => $attr['size'],
			),
			'term',
			$this->term_data->term_id
		);

		return $html;

	}

	/**
	 * Get term name
	 *
	 * @since 1.0.0
	 */
	public function get_term_name( $attr = array() ) {
		$default_attr = array( 'number_of_words' => 10 );

		$attr = wp_parse_args( $attr, $default_attr );

		$html = cherry_projects()->projects_data->cherry_utility->attributes->get_title(
			array(
				'html'   => '<h5 %1$s><a href="%2$s" %3$s>%4$s</a></h5>',
				'length' => $attr['number_of_words'],
			),
			'term',
			$this->term_data->term_id
		);

		return $html;
	}

	/**
	 * Get term permalink
	 *
	 * @since 1.0.0
	 */
	public function get_term_permalink( $attr = array() ) {
		$default_attr = array(
			'text_visible' => false
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$permalink = cherry_projects()->projects_data->cherry_utility->attributes->get_term_permalink( $this->term_data->term_id );

		/**
		 * Filter permalink text.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		$permalink_text = apply_filters( 'cherry-projects-terms-permalink-text', esc_html__( 'More', 'cherry-projects' ) );

		$icon_content = ( filter_var( $attr['text_visible'], FILTER_VALIDATE_BOOLEAN ) ) ? '<span>' . $permalink_text . '</span>' : '<span class="dashicons"></span>';

		$html = sprintf( '<a class="term-permalink simple-icon" href="%1$s">%2$s</a>',
			$permalink,
			$icon_content
		);

		return $html;
	}

	/**
	 * Get term description
	 *
	 * @since 1.0.0
	 */
	public function get_term_description( $attr = array() ) {
		$default_attr = array(
			'number_of_words' => '15',
			'ending'          => '&hellip;'
		);

		$attr = wp_parse_args( $attr, $default_attr );

		$html = cherry_projects()->projects_data->cherry_utility->attributes->get_content(
			array(
				'content_type'	=> 'term',
				'length'		=> (int)$attr['number_of_words'],
				'ending'		=> $attr['ending'],
			),
			'term',
			$this->term_data->term_id
		);

		return $html;
	}

	/**
	 * Get term attachments
	 *
	 * @since 1.0.0
	 */
	public function get_term_attachments( $attr = array() ) {
		$default_attr = array( 'prefix' => esc_html__( 'Posted by ', 'cherry-projects' ) );

		$attr = wp_parse_args( $attr, $default_attr );

		$html = cherry_projects()->projects_data->cherry_utility->meta_data->get_post_count_in_term(
			array(
				'icon'      => apply_filters( 'cherry_projects_term_attachments_icon', '' ),
				'sufix'     => array(
					'singular' => '%s project',
					'plural'   => '%s projects',
					'domain'   => 'cherry-projects',
					'context'  => false,
				),
				'echo'      => false,
			),
			$this->term_data->term_id
		);

		return $html;
	}

	/**
	 * Get image tag by attachment id
	 *
	 * @param  int $attachment_id Attachment id value
	 * @param  string $size       Image size
	 * @return string
	 */
	public function get_image_by_id( $attachment_id, $size = 'large', $class = '' ) {

		$image_data = wp_get_attachment_image_src( $attachment_id, $size );

		$img_tag = sprintf( '<img class="" src="%1$s" alt="" width="%2$s" height="%3$s">', $image_data[0], $image_data[1], $image_data[2], $class );

		return $img_tag;
	}

	/**
	 * Get image src by id
	 *
	 * @param  int $attachment_id Attachment id value
	 * @param  string $size       Image size
	 * @return string
	 */
	public function get_image_src_by_id( $attachment_id, $size = 'large' ) {

		$image_data = wp_get_attachment_image_src( $attachment_id, $size );

		$img_src = $image_data[0];

		return $img_src;
	}

	/**
	 * Get cropped image url.
	 *
	 * @param  integer $width         Cropped width value.
	 * @param  integer $height        Cropped height value.
	 * @return string
	 */
	public function get_cropped_image_url( $attachment_id, $size = 'full', $width = 500, $height = 300 ) {

		// Check if $attachment_id exist.
		if ( null == $attachment_id ) {
			return false;
		}

		$title = get_the_title( $attachment_id );

		$img_url = wp_get_attachment_url( $attachment_id , $size );

		// Resize & crop image.
		$croped_image_url = aq_resize( $img_url, $width, $height, true );

		// Get $pathinfo.
		$pathinfo = pathinfo( $croped_image_url );

		// Get $attachment metadata.
		$attachment_metadata = wp_get_attachment_metadata( $attachment_id );

		// Create new custom size.
		$attachment_metadata['sizes'][ 'croped-image-' . $width . '-' . $height ] = array(
			'file'		=> $pathinfo['basename'],
			'width'		=> $width,
			'height'	=> $height,
			'mime-type'	=> get_post_mime_type( $attachment_id ),
		);

		// WP update attachment metadata.
		wp_update_attachment_metadata( $attachment_id, $attachment_metadata );

		$img_tag = sprintf( '<img src="%1$s" alt="%2$s" width="%3$s" height="%4$s">', $croped_image_url, $title, $width, $height );

		return $img_tag;
	}

}
