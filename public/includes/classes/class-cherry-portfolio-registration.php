<?php
/**
 * Cherry Portfolio
 *
 * @package   Cherry_Portfolio
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for register post types.
 *
 * @since 1.0.0
 */
class Cherry_Portfolio_Registration {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Adds the testimonials post type.
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );

		add_action( 'post.php',          array( $this, 'add_post_formats_support' ) );
		add_action( 'load-post.php', array( $this, 'add_post_formats_support' ) );
		add_action( 'load-post-new.php', array( $this, 'add_post_formats_support' ) );

		// Removes rewrite rules and then recreate rewrite rules.
		// add_action( 'init', array( $this, 'rewrite_rules' ) );
	}

	public function rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * Register the custom post type.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function register() {

		$labels = array(
			'name'               => __( 'Portfolio', 'cherry-portfolio' ),
			'singular_name'      => __( 'Portfolio list', 'cherry-portfolio' ),
			'add_new'            => __( 'Add Item', 'cherry-portfolio' ),
			'add_new_item'       => __( 'Add Portfolio Item', 'cherry-portfolio' ),
			'edit_item'          => __( 'Edit Portfolio Item', 'cherry-portfolio' ),
			'new_item'           => __( 'New Portfolio Item', 'cherry-portfolio' ),
			'view_item'          => __( 'View Portfolio Item', 'cherry-portfolio' ),
			'search_items'       => __( 'Search Portfolio Items', 'cherry-portfolio' ),
			'not_found'          => __( 'No Portfolio Items found', 'cherry-portfolio' ),
			'not_found_in_trash' => __( 'No Portfolio Items found in trash', 'cherry-portfolio' ),
		);

		$supports = array(
			'title',
			'editor',
			'thumbnail',
			'revisions',
			'page-attributes',
			'post-formats',
			'comments',
			'cherry-layouts',
			'page-attributes',
			'cherry-grid-type',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'capability_type' => 'post',
			'rewrite'         => array( 'slug' => 'portfolio-archive', ), // Permalinks format
			'menu_position'   => null,
			'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-portfolio' : '',
			'can_export'      => true,
			'has_archive'     => true,
			'taxonomies'      => array( 'post_format' )
		);


		$args = apply_filters( 'cherry_portfolio_post_type_args', $args );

		register_post_type( CHERRY_PORTFOLIO_NAME, $args );
	}

	/**
	 * Post formats.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public function add_post_formats_support() {
		global $typenow;

		if ( CHERRY_PORTFOLIO_NAME != $typenow ) {
			return;
		}

		$args = apply_filters( 'cherry_portfolio_add_post_formats_support', array( 'image', 'gallery', 'audio', 'video', ) );

		add_post_type_support( CHERRY_PORTFOLIO_NAME, 'post-formats', $args );
		add_theme_support( 'post-formats', $args );
	}

	/**
	 * Register the custom taxonomy.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function register_taxonomy() {
		//Register the category taxonomy
		$category_taxonomy_labels = array(
			'name'          => __( 'Portfolio Categories', 'cherry-portfolio' ),
			'label'         => __( 'Categories', 'cherry-portfolio' ),
			'singular_name' => __( 'Category', 'cherry-portfolio' ),
			'menu_name'     => __( 'Categories', 'cherry-portfolio' ),
		);
		$category_taxonomy_args = array(
			'labels'		=> $category_taxonomy_labels,
			'hierarchical'	=> true,
			'rewrite'		=> true,
			'query_var'		=> true
		);
		register_taxonomy( CHERRY_PORTFOLIO_NAME.'_category', CHERRY_PORTFOLIO_NAME, $category_taxonomy_args );
		//Register the tag taxonomy
		$tag_taxonomy_labels = array(
			'name'          => __( 'Portfolio Tags', 'cherry-portfolio' ),
			'label'         => __( 'Tags', 'cherry-portfolio' ),
			'singular_name' => __( 'Tag', 'cherry-portfolio' ),
			'menu_name'     => __( 'Tags', 'cherry-portfolio' ),
		);
		$tag_taxonomy_args = array(
			'labels'		=> $tag_taxonomy_labels,
			'hierarchical'	=> false,
			'rewrite'		=> true,
			'query_var'		=> true
		);
		register_taxonomy( CHERRY_PORTFOLIO_NAME.'_tag', CHERRY_PORTFOLIO_NAME, $tag_taxonomy_args );
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

Cherry_Portfolio_Registration::get_instance();