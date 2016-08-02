<?php
/**
 * Cherry Projects page template
 *
 * @package   Cherry_Projects
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for including page templates.
 *
 * @since 1.0.0
 */
class Cherry_Projects_Page_Template {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * The array of templates that this plugin tracks.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $templates = array();

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add a filter to the page attributes metabox to inject our template into the page template cache.
		add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register_templates' ) );

		// Add a filter to the save post in order to inject out template into the page cache.
		add_filter( 'wp_insert_post_data', array( $this, 'register_templates' ) );

		// Add a filter to the template include in order to determine if the page has our template assigned and return it's path.
		add_filter( 'template_include', array( $this, 'view_template' ) );

		// Add your templates to this array.
		$this->templates = array(
			'template-projects.php' => __( 'Projects', 'cherry-projects' ),
		);
		// Adding support for theme templates to be merged and shown in dropdown.
		$templates = wp_get_theme()->get_page_templates();

		$templates = array_merge( $templates, $this->templates );

	}


	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 *
	 * @since  1.0.0
	 * @param  array $atts The attributes for the page attributes dropdown.
	 * @return array $atts The attributes for the page attributes dropdown.
	 */
	public function register_templates( $atts ) {

		// Create the key used for the themes cache.
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. If it doesn't exist, or it's empty prepare an array.
		$templates = wp_cache_get( $cache_key, 'themes' );

		if ( empty( $templates ) ) {
			$templates = array();
		}

		// Since we've updated the cache, we need to delete the old cache.
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing available templates.
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;
	}

	/**
	 * Checks if the template is assigned to the page.
	 *
	 * @since  1.0.0
	 * @param  string $template current template name.
	 * @return string
	 */
	public function view_template( $template ) {
		global $post;

		$find = array();

		$file = '';


		if ( $post ) {
			$page_template_meta = get_post_meta( $post->ID, '_wp_page_template', true );
		} else {
			$page_template_meta = 'default';
		}

		if ( is_single() && CHERRY_PROJECTS_NAME === get_post_type() ) {

			$file   = 'single-projects.php';
			$find[] = $file;
			$find[] = cherry_projects()->template_path() . $file;

		} elseif ( is_post_type_archive( CHERRY_PROJECTS_NAME ) || is_tax( array( CHERRY_PROJECTS_NAME . '_category', CHERRY_PROJECTS_NAME . '_tag' ) ) ) {
			$file 	= 'archive-projects.php';
			$find[] = $file;
			$find[] = cherry_projects()->template_path() . $file;

		} elseif ( 'template-projects.php' === $page_template_meta ) {
			$file 	= 'template-projects.php';
			$find[] = $file;
			$find[] = cherry_projects()->template_path() . $file;

		}

		if ( $file ) {
			$template = locate_template( array_unique( $find ) );

			if ( ! $template ) {
				$template = trailingslashit( CHERRY_PROJECTS_DIR ) . 'templates/' . $file;;
			}
		}
		return $template;
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

Cherry_Projects_Page_Template::get_instance();