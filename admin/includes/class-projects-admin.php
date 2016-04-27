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
	 * [$portfolio_meta_boxes description]
	 * @var null
	 */
	public $projects_meta_boxes = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		// Load post meta boxes on the post editing screen.
		add_action( 'load-post.php',     array( $this, 'load_post_meta_boxes' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post_meta_boxes' ) );

		add_action( 'wp_ajax_get_new_format_metabox', array( $this, 'load_post_meta_boxes' ), 10 );

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );

		// Modify the columns on the "Projects" screen.
		add_filter( 'manage_edit-' . CHERRY_PROJECTS_NAME . '_columns',        array( $this, 'edit_projects_columns' ) );
		add_action( 'manage_' . CHERRY_PROJECTS_NAME . '_posts_custom_column', array( $this, 'manage_projects_columns' ), 10, 2 );

		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts',  array( $this, 'enqueue_styles' ) );

		add_action( 'wp_ajax_get_new_format_metabox', array( $this, 'get_new_format_metabox' ), 20 );

	}

	/**
	 * Loads custom meta boxes
	 *
	 * @since 1.0.0
	 */
	public function load_post_meta_boxes() {

		if ( ( !empty( $screen->post_type ) && 'projects' === $screen->post_type ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			//require_once( trailingslashit( CHERRY_PROJECTS_DIR ) . 'admin/includes/class-projects-meta-boxes.php' );
			//$this->projects_meta_boxes = new Cherry_Projects_Meta_Boxes;
		}
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

		// Adds the checkbox column.
		$columns['cb'] = $post_columns['cb'];

		// Add custom columns and overwrite the 'title' column.
		$columns['title']								= __( 'Title', 'cherry-projects' );
		$columns[ CHERRY_PROJECTS_NAME . '_category' ]	= __( 'Projects category', 'cherry-projects' );
		$columns[ CHERRY_PROJECTS_NAME . '_tag' ]		= __( 'Projects tag', 'cherry-projects' );
		$columns['date']								= __( 'Date', 'cherry-projects' );
		$columns['preview']								= __( 'Preview', 'cherry-projects' );

		// Return the columns.
		return $columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column
	 * @param  int    $post_id
	 */
	public function manage_projects_columns( $column, $post_id ) {

		switch( $column ) {

			case CHERRY_PROJECTS_NAME . '_category' :

				$post_categories = is_wp_error( get_the_terms( $post_id, CHERRY_PROJECTS_NAME . '_category' ) ) ? '' : get_the_terms( $post_id, CHERRY_PROJECTS_NAME . '_category' );
				if ( $post_categories ) {
					$category_name_list = '';
					$count = 1;
						foreach ( $post_categories as $category => $category_value ) {
							$category_name_list .= $category_value->name;
							( $count < count( $post_categories ) ) ? $category_name_list .= ', ':'';
							$count++;

						}
					echo $category_name_list;
				}else{
					echo __( 'Project has no categories', 'cherry-projects' );
				}

			break;

			case CHERRY_PROJECTS_NAME . '_tag' :

				$post_tags = is_wp_error( get_the_terms( $post_id, CHERRY_PROJECTS_NAME . '_tag' ) ) ? '' : get_the_terms( $post_id, CHERRY_PROJECTS_NAME . '_tag' );
				if ( $post_tags ) {
					$tags_name_list = '';
					$count = 1;
						foreach ( $post_tags as $tag => $tag_value ) {
							$tags_name_list .= $tag_value->name;
							( $count < count( $post_tags ) ) ? $tags_name_list .= ', ' : '';
							$count++;

						}
					echo $tags_name_list;
				}else{
					echo __( 'Project has no tags', 'cherry-projects' );
				}

			break;

			case 'preview' :

				$thumb = get_the_post_thumbnail( $post_id, array( 75, 75 ) );
				echo !empty( $thumb ) ? $thumb : '&mdash;';

			break;
		}
	}

	/**
	 * [enqueue_scripts description]
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( ! empty( $screen->post_type ) && 'projects' === $screen->post_type ) {
			wp_enqueue_script( 'cherry-projects-admin-scripts', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/js/cherry-projects-admin-scripts.js', array( 'jquery' ), CHERRY_PROJECTS_VERSION );
			//$option_inteface_builder = new Cherry_Interface_Builder();
			//$option_inteface_builder->enqueue_builder_scripts();

			//ajax js object portfolio_type_ajax
			wp_localize_script( 'cherry-projects-admin-scripts', 'projects_post_format_ajax', array( 'url' => admin_url('admin-ajax.php') ) );
		}
	}

	/**
	 * [enqueue_styles description]
	 * @return [type] [description]
	 */
	public function enqueue_styles() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'projects' === $screen->post_type ) {
			//$option_inteface_builder = new Cherry_Interface_Builder();
			//$option_inteface_builder->enqueue_builder_styles();

			wp_enqueue_style( 'projects-admin-style', trailingslashit( CHERRY_PROJECTS_URI ) . 'admin/assets/css/admin-style.css', array(), CHERRY_PROJECTS_VERSION );
		}
	}

	/**
	 * [get_new_format_metabox description]
	 * @return [type] [description]
	 */
	public function get_new_format_metabox() {
		if ( !empty( $_POST ) && array_key_exists( 'post_format', $_POST ) && array_key_exists( 'post_id', $_POST ) ) {
			$post_format = $_POST['post_format'];
			$post_id = $_POST['post_id'];
			//$output = $this->projects_meta_boxes->format_metabox_builder( $post_id, $post_format );
			//echo $output;
			exit;
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

Cherry_Projects_Admin::get_instance();

