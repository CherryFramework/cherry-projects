<?php
/**
 * Cherry Projects Shortcode.
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Portfolio shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Projects_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'projects';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Register shortcode on 'init'.
		add_action( 'init', array( $this, 'register_shortcode' ) );
	}

	/**
	 * Registers the [$this->name] shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {
		/**
		 * Filters a shortcode name.
		 *
		 * @since 1.0.0
		 * @param string $this->name Shortcode name.
		 */
		$tag = apply_filters( self::$name . '_shortcode_name', self::$name );

		add_shortcode( 'cherry_' . $tag, array( $this, 'do_shortcode' ) );
	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.0.0
	 * @param  array  $atts      The user-inputted arguments.
	 * @param  string $content   The enclosed content (if the shortcode is used in its enclosing form).
	 * @param  string $shortcode The shortcode tag, useful for shared callback functions.
	 * @return string
	 */
	public function do_shortcode( $atts, $content = null, $shortcode = '' ) {
		// Set up the default arguments.
		$defaults = array(
			'projects-listing-layout'				=> 'grid-layout',
			'projects-loading-mode'					=> 'ajax-pagination-mode',
			'projects-loading-animation'			=> 'loading-animation-move-up',
			'projects-hover-animation'				=> 'simple-scale',
			'projects-filter-visible'				=> 'true',
			'projects-filter-type'					=> 'category',
			'projects-category-list'				=> array(),
			'projects-tags-list'					=> array(),
			'projects-order-filter-visible'			=> 'false',
			'projects-order-filter-default-value'	=> 'desc',
			'projects-orderby-filter-default-value'	=> 'date',
			'projects-posts-format'					=> 'post-format-all',
			'projects-column-number'				=> 3,
			'projects-post-per-page'				=> 9,
			'projects-item-margin'					=> 4,
			'projects-justified-fixed-height'		=> 300,
			'projects-masonry-template'				=> 'masonry-default.tmpl',
			'projects-grid-template'				=> 'grid-default.tmpl',
			'projects-justified-template'			=> 'justified-default.tmpl',
			'projects-cascading-grid-template'		=> 'cascading-grid-default.tmpl',
			'projects-list-template'				=> 'list-default.tmpl',
		);

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */

		$atts = shortcode_atts( $defaults, $atts, $shortcode );

		return cherry_projects()->projects_data->render_projects( $atts );
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

}

Cherry_Projects_Shortcode::get_instance();