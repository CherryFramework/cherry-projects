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

			$this->post_meta = get_post_meta( $post->ID, CHERRY_PROJECTS_POSTMETA, true );
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
	 * Get post title.
	 *
	 * @since 1.0.0
	 */
	public function get_image() {
		$image = cherry_projects()->projects_data->cherry_utility->media->get_image( array(
			'html' => '<figure><a href="%1$s" %2$s ><img src="%3$s" alt="%4$s" %5$s ></a></figure>',
			'size' => 'large',
		) );

		return $image;
	}

	/**
	 * Get post title.
	 *
	 * @since 1.0.0
	 */
	public function get_title() {
		$title = cherry_projects()->projects_data->cherry_utility->attributes->get_title();

		return $title;
	}

	/**
	 * Get post content.
	 *
	 * @since 1.0.0
	 */
	public function get_content() {
		$content = wp_trim_words( get_the_content(), 30, '...' );

		return $content;
	}


	/**
	 * Get testimonial's email.
	 *
	 * @since 1.0.2
	 */
	/*public function get_email() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['email'] ) ) {
			return;
		}

		$email = '<a href="mailto:' . antispambot( $post_meta['email'], 1 ) .'" class="testimonials-item_email">' . antispambot( $post_meta['email'] ) .'</a>';

		return apply_filters( 'cherry_testimonials_email_template_callbacks', $email, $post->ID, $this->atts );
	}*/


}
