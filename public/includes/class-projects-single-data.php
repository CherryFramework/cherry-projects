<?php
/**
 * Cherry Project Single
 *
 * @package   Cherry_Project
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Portfolio Single data.
 *
 * @since 1.0.0
 */
class Cherry_Project_Single_Data extends Cherry_Project_Data {


	/**
	 * Post query object.
	 *
	 * @var null
	 */
	private $posts_query = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->set_default_options();
		$this->set_cherry_utility();

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_single_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_single_scripts' ) );

	}

	/**
	 * [render_projects description]
	 *
	 * @return [type] [description]
	 */
	public function render_projects_single() {

		$post_id = get_the_ID();
		$format = get_post_format( $post_id );
		$format = ( empty( $format ) ) ? 'post-format-standard' : 'post-format-' . $format;


		switch ( $format ) {
			case 'post-format-standard':
				$template = $this->get_template_by_name( $this->default_options['standard-post-template'], 'projects' );
				break;

			case 'post-format-image':
				$template = $this->get_template_by_name( $this->default_options['image-post-template'], 'projects' );
				break;

			case 'post-format-gallery':
				$template = $this->get_template_by_name( $this->default_options['gallery-post-template'], 'projects' );
				break;

			case 'post-format-audio':
				$template = $this->get_template_by_name( $this->default_options['audio-post-template'], 'projects' );
				break;

			case 'post-format-video':
				$template = $this->get_template_by_name( $this->default_options['video-post-template'], 'projects' );
				break;
		}

		$macros    = '/%%.+?%%/';
		$callbacks = $this->setup_template_data( $this->default_options );
		$template_content = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $template );

		$html = sprintf( '<div class="cherry-projects-single cherry-projects-single-%1$s">', $format );
			$html .= '<div class="cherry-projects-single-post">';
				$html .= $template_content;
			$html .= '</div>';
		$html .= '</div>';

		echo $html;

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_single_styles() {
		if ( is_single() ) {
			wp_enqueue_style( 'slider-pro', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/slider-pro.min.css', array(), '1.2.4' );
		}

		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/magnific-popup.css', array(), '1.1.0' );
		wp_enqueue_style( 'cherry-projects-styles', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/css/styles.css', array(), CHERRY_PROJECTS_VERSION );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_single_scripts() {
		if ( is_single() ) {
			wp_enqueue_script( 'slider-pro', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.sliderPro.min.js', array( 'jquery' ), '1.2.4', true );
		}

		wp_enqueue_script( 'magnific-popup', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), '1.1.0', true );
		wp_enqueue_script( 'cherry-projects-single-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'public/assets/js/cherry-projects-single-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION, true );
	}

}
