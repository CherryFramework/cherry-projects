<?php
/**
 * Cherry Projects
 *
 * @package   Cherry_Projects
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
class Cherry_Projects_Registration {

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
		add_action( 'after_setup_theme', array( __CLASS__, 'register' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'register_taxonomy' ) );

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
			'name'               => __( 'Projects', 'cherry-projects' ),
			'singular_name'      => __( 'Projects list', 'cherry-projects' ),
			'add_new'            => __( 'Add Project', 'cherry-projects' ),
			'add_new_item'       => __( 'Add Project Item', 'cherry-projects' ),
			'edit_item'          => __( 'Edit Project Item', 'cherry-projects' ),
			'new_item'           => __( 'New Project Item', 'cherry-projects' ),
			'view_item'          => __( 'View Project Item', 'cherry-projects' ),
			'search_items'       => __( 'Search Project Items', 'cherry-projects' ),
			'not_found'          => __( 'No Project Items found', 'cherry-projects' ),
			'not_found_in_trash' => __( 'No Project Items found in trash', 'cherry-projects' ),
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
			'rewrite'         => array( 'slug' => 'projects-archive', ), // Permalinks format
			'menu_position'   => null,
			'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-portfolio' : '',
			'can_export'      => true,
			'has_archive'     => true,
			'taxonomies'      => array( 'post_format' )
		);


		$args = apply_filters( 'cherry_projects_post_type_args', $args );

		register_post_type( CHERRY_PROJECTS_NAME, $args );
	}

	/**
	 * Post formats.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public function add_post_formats_support() {
		global $typenow;

		if ( CHERRY_PROJECTS_NAME != $typenow ) {
			return;
		}

		$args = apply_filters( 'cherry_projects_add_post_formats_support', array( 'image', 'gallery', 'audio', 'video', ) );

		add_post_type_support( CHERRY_PROJECTS_NAME, 'post-formats', $args );
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
			'name'          => __( 'Projects Categories', 'cherry-projects' ),
			'label'         => __( 'Categories', 'cherry-projects' ),
			'singular_name' => __( 'Category', 'cherry-projects' ),
			'menu_name'     => __( 'Categories', 'cherry-projects' ),
		);
		$category_taxonomy_args = array(
			'labels'		=> $category_taxonomy_labels,
			'hierarchical'	=> true,
			'rewrite'		=> true,
			'query_var'		=> true
		);
		register_taxonomy( CHERRY_PROJECTS_NAME . '_category', CHERRY_PROJECTS_NAME, $category_taxonomy_args );
		//Register the tag taxonomy
		$tag_taxonomy_labels = array(
			'name'          => __( 'Projects Tags', 'cherry-projects' ),
			'label'         => __( 'Tags', 'cherry-projects' ),
			'singular_name' => __( 'Tag', 'cherry-projects' ),
			'menu_name'     => __( 'Tags', 'cherry-projects' ),
		);
		$tag_taxonomy_args = array(
			'labels'		=> $tag_taxonomy_labels,
			'hierarchical'	=> false,
			'rewrite'		=> true,
			'query_var'		=> true
		);
		register_taxonomy( CHERRY_PROJECTS_NAME . '_tag', CHERRY_PROJECTS_NAME, $tag_taxonomy_args );
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

Cherry_Projects_Registration::get_instance();