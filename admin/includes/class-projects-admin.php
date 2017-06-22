<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package   Cherry_Projects_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

class Cherry_Projects_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );

		// Modify the columns on the "Projects" screen.
		add_filter( 'manage_edit-' . CHERRY_PROJECTS_NAME . '_columns',        array( $this, 'edit_projects_columns' ) );
		add_action( 'manage_' . CHERRY_PROJECTS_NAME . '_posts_custom_column', array( $this, 'manage_projects_columns' ), 10, 2 );

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Adds a custom filter on 'request'
	 *
	 * @since 1.0.0
	 */
	public function load_edit() {
		$screen = get_current_screen();

		if ( !empty( $screen->post_type ) && 'projects' === $screen->post_type ) {
			add_action( 'admin_head', array( $this, 'print_styles' ) );
		}
	}

	/**
	 * Style adjustments for the manage menu items screen.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() { ?>
		<style type="text/css">
		.edit-php .wp-list-table td.thumbnail.column-thumbnail,
		.edit-php .wp-list-table th.manage-column.column-thumbnail,
		.edit-php .wp-list-table td.author_name.column-author_name,
		.edit-php .wp-list-table th.manage-column.column-author_name {
			text-align: center;
		}
		</style>
	<?php }

	/**
	 * Filters the columns on the "Projects list" screen.
	 *
	 * @since  1.0.0
	 * @param  array $post_columns
	 * @return array
	 */
	public function edit_projects_columns( $post_columns ) {

		// Add custom columns and overwrite the 'title' column.
		$post_columns['title']                              = esc_html__( 'Title', 'cherry-projects' );
		$post_columns[ CHERRY_PROJECTS_NAME . '_category' ] = esc_html__( 'Projects category', 'cherry-projects' );
		$post_columns[ CHERRY_PROJECTS_NAME . '_tag' ]      = esc_html__( 'Projects tag', 'cherry-projects' );
		$post_columns['date']                               = esc_html__( 'Date', 'cherry-projects' );
		$post_columns['preview']                            = esc_html__( 'Preview', 'cherry-projects' );

		// Return the columns.
		return $post_columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_projects_columns( $column, $post_id ) {

		$post_terms = get_the_terms( $post_id, $column );

		switch( $column ) {

			case CHERRY_PROJECTS_NAME . '_category' :
				if ( ! is_wp_error( $post_terms ) && $post_terms ) {
					$category_name_list = '';
					$count = 1;
						foreach ( $post_terms as $category => $category_value ) {
							$category_name_list .= $category_value->name;
							( $count < count( $post_terms ) ) ? $category_name_list .= ', ':'';
							$count++;

						}
					echo $category_name_list;
				}else{
					echo esc_html__( 'Project has no categories', 'cherry-projects' );
				}

			break;

			case CHERRY_PROJECTS_NAME . '_tag' :

				if ( ! is_wp_error( $post_terms ) && $post_terms ) {
					$tags_name_list = '';
					$count = 1;
						foreach ( $post_terms as $tag => $tag_value ) {
							$tags_name_list .= $tag_value->name;
							( $count < count( $post_terms ) ) ? $tags_name_list .= ', ' : '';
							$count++;

						}
					echo $tags_name_list;
				}else{
					echo esc_html__( 'Project has no tags', 'cherry-projects' );
				}

			break;

			case 'preview' :

				$thumb = get_the_post_thumbnail( $post_id, array( 75, 75 ) );
				echo !empty( $thumb ) ? $thumb : '&mdash;';

			break;
		}
	}

	/**
	 * Enqueue admin scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'projects' === $screen->post_type ) {

		}
	}

	/**
	 * Enqueue admin styles
	 *
	 * @return void
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'projects' === $screen->post_type ) {
			wp_enqueue_style( 'projects-admin-style', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/css/admin-style.css', array(), CHERRY_PROJECTS_VERSION );
		}
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

/**
 * Returns instanse of main theme configuration class.
 *
 * @since  1.0.0
 * @return object
 */
function cherry_projects_admin() {
	return Cherry_Projects_Admin::get_instance();
}

cherry_projects_admin();
