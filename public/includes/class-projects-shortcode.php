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
 * Class for Portfolio shortcode.
 *
 * @since 1.0.0
 */
class Cherry_Portfolio_Shortcode extends Cherry_Portfolio_Data {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'portfolio';

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

		// Register shortcode and add it to the dialog.
		add_filter( 'cherry_shortcodes/data/shortcodes', array( $this, 'shortcodes' ) );
		add_filter( 'cherry_templater/data/shortcodes',  array( $this, 'shortcodes' ) );

		add_filter( 'cherry_templater_target_dirs', array( $this, 'add_target_dir' ), 11 );
		add_filter( 'cherry_templater_macros_buttons', array( $this, 'add_macros_buttons' ), 11, 2 );

		add_filter( 'cherry_editor_target_dirs', array( $this, 'add_target_dir' ), 11 );
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
	 * Filter to modify original shortcodes data and add [$this->name] shortcode.
	 *
	 * @since  1.0.0
	 * @param  array   $shortcodes Original plugin shortcodes.
	 * @return array               Modified array.
	 */
	public function shortcodes( $shortcodes ) {
		$shortcodes[ self::$name ] = array(
			'name'  => __( 'Portfolio', 'cherry-portfolio' ), // Shortcode name.
			'desc'  => 'This is a Portfolio Shortcode',
			'type'  => 'single', // Can be 'wrap' or 'single'. Example: [b]this is wrapped[/b], [this_is_single]
			'group' => 'content', // Can be 'content', 'box', 'media' or 'other'. Groups can be mixed, for example 'content box'.
			'atts'  => array( // List of shortcode params (attributes).
				'listing_layout' => array(
					'type'    => 'select',
					'values'  => array(
						'masonry-layout'	=> __('Masonry', 'cherry-portfolio'),
						'grid-layout'		=> __('Grid', 'cherry-portfolio'),
						'justified-layout'	=> __('Justified', 'cherry-portfolio'),
						'list-layout'		=> __('List', 'cherry-portfolio'),
					),
					'default' => 'masonry-layout',
					'name'    => __( 'Portfolio listing layout', 'cherry-portfolio' ),
					// 'desc'    => __( '', 'cherry-portfolio' ),
				),
				'loading_mode' => array(
					'type'    => 'select',
					'values'  => array(
						'portfolio-none-mode'				=> __( 'None', 'cherry-portfolio' ),
						'portfolio-ajax-pagination-mode'	=> __('Ajax pagination', 'cherry-portfolio'),
						'portfolio-more-button-mode'		=> __('More button', 'cherry-portfolio'),
					),
					'default' => 'portfolio-ajax-pagination-mode',
					'name'    => __( 'Portfolio pagination type', 'cherry-portfolio' ),
					// 'desc'    => __( '', 'cherry-portfolio' ),
				),
				'loading_animation' => array(
					'type'    => 'select',
					'values'  => array(
						'loading-animation-fade'				=> __('Fade animation', 'cherry-portfolio'),
						'loading-animation-scale'				=> __('Scale animation', 'cherry-portfolio'),
						'loading-animation-move-up'				=> __('Move Up animation', 'cherry-portfolio'),
						'loading-animation-flip'				=> __('Flip animation', 'cherry-portfolio'),
						'loading-animation-helix'				=> __('Helix animation', 'cherry-portfolio'),
						'loading-animation-fall-perspective'	=> __('Fall perspective animation', 'cherry-portfolio'),
					),
					'default' => 'loading-animation-move-up',
					'name'    => __( 'Portfolio items animation type', 'cherry-portfolio' ),
					// 'desc'    => __( '', 'cherry-portfolio' ),
				),
				'posts_format' => array(
					'type'    => 'select',
					'values'  => array(
						'post-format-all'		=> __('All formats', 'cherry-portfolio'),
						'post-format-standard'	=> __('Standard format', 'cherry-portfolio'),
						'post-format-image'		=> __('Image format', 'cherry-portfolio'),
						'post-format-gallery'	=> __('Gallery format', 'cherry-portfolio'),
						'post-format-audio'		=> __('Audio format', 'cherry-portfolio'),
						'post-format-video'		=> __('Video format', 'cherry-portfolio'),
					),
					'default' => 'post-format-all',
					'name'    => __( 'Post format', 'cherry-portfolio' ),
					'desc'    => __( 'Select post format', 'cherry-portfolio' ),
				),
				'filter_visible' => array(
					'type'    => 'bool',
					'default' => 'yes',
					'name'    => __( 'Filter', 'cherry-portfolio' ),
					'desc'    => __( 'Filter visible', 'cherry-portfolio' ),
				),
				'order_filter_visible' => array(
					'type'    => 'bool',
					'default' => 'no',
					'name'    => __( 'Order filter', 'cherry-portfolio' ),
					'desc'    => __( 'Order filter visible', 'cherry-portfolio' ),
				),
				'posts_per_page' => array(
					'type'    => 'number',
					'min'     => -1,
					'max'     => 50,
					'step'    => 1,
					'default' => 9,
					'name'    => __( 'Posts per page', 'cherry-portfolio' ),
					'desc'    => __( 'Specify number of posts that you want to show. Enter -1 to get all posts', 'cherry-portfolio' ),
				),
				'grid_col' => array(
					'type'    => 'number',
					'min'     => 2,
					'max'     => 10,
					'step'    => 1,
					'default' => 3,
					'name'    => __( 'Column number', 'cherry-portfolio' ),
					'desc'    => __( 'Set columns number (has effect only for columns layout type)', 'cherry-portfolio' ),
				),
				'item_margin' => array(
					'type'    => 'number',
					'min'     => 0,
					'max'     => 50,
					'step'    => 1,
					'default' => 9,
					'name'    => __( 'Gutter width', 'cherry-portfolio' ),
					'desc'    => __( 'Set gutter width (in px)', 'cherry-portfolio' ),
				),
				'template' => array(
					'default' => 'masonry-default.tmpl',
					'name'    => __( 'Template', 'cherry-portfolio' ),
					'desc'    => __( 'Content template', 'cherry-portfolio' )
				),
				'custom_class' => array(
					'default' => '',
					'name'    => __( 'Class', 'cherry-portfolio' ),
					'desc'    => __( 'Extra CSS class', 'cherry-portfolio' )
				),
			),
			'icon'     => 'h-square', // Custom icon (font-awesome).
			'function' => array( $this, 'do_shortcode' ) // Name of shortcode function.
		);

		return $shortcodes;
	}

	/**
	 * Adds team template directory to shortcodes templater
	 *
	 * @param array  $target_dirs  existing target dirs
	 */
	public function add_target_dir( $target_dirs ) {

		array_push( $target_dirs, CHERRY_PORTFOLIO_DIR );
		return $target_dirs;

	}

	/**
	 * Add team shortcode macros buttons to templater
	 *
	 * @since 1.0.0
	 *
	 * @param array  $macros    current buttons array
	 * @param string $shortcode shortcode name
	 */
	public function add_macros_buttons( $macros_buttons, $shortcode ) {

		if ( self::$name != $shortcode ) {
			return $macros_buttons;
		}

		$macros_buttons = array(
			'title' => array(
				'id'    => 'cherry_title',
				'value' => __( 'Title', 'cherry-portfolio' ),
				'open'  => '%%TITLE%%',
				'close' => '',
				'title' => __( 'Helper information for `Title` macros', 'cherry-portfolio' ),
			),
			'image' => array(
				'id'    => 'cherry_image',
				'value' => __( 'Image', 'cherry-portfolio' ),
				'open'  => '%%IMAGE%%',
				'close' => '',
				'title' => __( 'Helper information for `Image` macros', 'cherry-portfolio' ),
			),
			'content' => array(
				'id'    => 'cherry_content',
				'value' => __( 'Content', 'cherry-portfolio' ),
				'open'  => '%%CONTENT="25"%%',
				'close' => '',
				'title' => __( 'Helper information for `Content` macros', 'cherry-portfolio' ),
			),
			'taxonomy' => array(
				'id'    => 'cherry_taxonomy',
				'value' => __( 'Taxonomy', 'cherry-portfolio' ),
				'open'  => '%%TAXONOMY="category"%%',
				'close' => ''
			),
			'date' => array(
				'id'    => 'cherry_date',
				'value' => __( 'Date', 'cherry-portfolio' ),
				'open'  => '%%DATE="Y - M - d"%%',
				'close' => ''
			),
			'author' => array(
				'id'    => 'cherry_author',
				'value' => __( 'Author', 'cherry-portfolio' ),
				'open'  => '%%AUTHOR%%',
				'close' => ''
			),
			'comments' => array(
				'id'    => 'cherry_comments',
				'value' => __( 'Comments', 'cherry-portfolio' ),
				'open'  => '%%COMMENTS="25"%%',
				'close' => ''
			),
			'externallink' => array(
				'id'    => 'cherry_externallink',
				'value' => __( 'External link', 'cherry-portfolio' ),
				'open'  => '%%EXTERNALLINK%%',
				'close' => ''
			),
			'zoomlink' => array(
				'id'    => 'cherry_zoomlink',
				'value' => __( 'Zoom link', 'cherry-portfolio' ),
				'open'  => '%%ZOOMLINK="Zomm"%%',
				'close' => ''
			),
			'permalink' => array(
				'id'    => 'cherry_permalink',
				'value' => __( 'Permalink', 'cherry-portfolio' ),
				'open'  => '%%PERMALINK="Permalink"%%',
				'close' => ''
			),
			'permalinkurl' => array(
				'id'    => 'cherry_permalink_url',
				'value' => __( 'Permalink url', 'cherry-portfolio' ),
				'open'  => '%%URL%%',
				'close' => ''
			),
			'gallerythumbnails' => array(
				'id'    => 'cherry_gallery_thumbnails',
				'value' => __( 'Gallery thumbnail', 'cherry-portfolio' ),
				'open'  => '%%GALLERYTHUMBNAILS%%',
				'close' => ''
			),
			'thumbnailscount' => array(
				'id'    => 'cherry_gallery_thumbnails_count',
				'value' => __( 'Thumbnails count', 'cherry-portfolio' ),
				'open'  => '%%THUMBNAILSCOUNT%%',
				'close' => ''
			),
		);

		return $macros_buttons;
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
			'listing_layout'			=> 'masonry-layout',
			'loading_mode'				=> 'portfolio-ajax-pagination-mode',
			'loading_animation'			=> 'loading-animation-move-up',
			'filter_visible'			=> 'yes',
			'order_filter_visible'		=> 'no',
			'posts_per_page'			=> 9,
			'grid_col'					=> 3,
			'item_margin'				=> 4,
			'echo'						=> false,
			'template'					=> '',
			'posts_format'				=> 'post-format-all',
			'custom_class'				=> ''
		);

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */

		$atts = shortcode_atts( $defaults, $atts, $shortcode );

		$atts['filter_visible']	= ( bool ) ( $atts['filter_visible'] === 'yes' ) ? true : false;
		$atts['order_filter_visible']	= ( bool ) ( $atts['order_filter_visible'] === 'yes' ) ? true : false;

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		parent::__construct();
		return $this->the_portfolio( $atts );
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

Cherry_Portfolio_Shortcode::get_instance();