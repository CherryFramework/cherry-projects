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

		// Shortcode insert module registration
		add_action( 'after_setup_theme', array( $this, 'shortcode_registration' ), 10 );
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
	 * Shortcode registration
	 *
	 * @return void
	 */
	public function shortcode_registration() {
		cherry_projects()->get_core()->init_module( 'cherry5-insert-shortcode', array() );

		$utility = cherry_projects()->get_core()->modules['cherry-utility']->utility;

		$category_list =  $utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_category', 'slug' );
		$tag_list      = $utility->satellite->get_terms_array( CHERRY_PROJECTS_NAME . '_tag', 'slug' );

		cherry5_register_shortcode(
				array(
					'title'       => esc_html__( 'Projects', 'cherry-projects' ),
					'description' => esc_html__( 'Showcase your projects using a variety of layouts with Cherry Projects plugin', 'cherry-projects' ),
					'icon'        => '<span class="dashicons dashicons-layout"></span>',
					'slug'        => 'cherry-prijects-plugin',
					'shortcodes'  => array(
						array(
							'title'       => esc_html__( 'Projects', 'cherry-projects' ),
							'description' => esc_html__( 'Shortcode is used to display the projects list with set parameters.', 'cherry-projects' ),
							'icon'        => '<span class="dashicons dashicons-layout"></span>',
							'slug'        => 'cherry_projects',
							'options'     => array(

								'listing_layout' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Projects listing layout', 'cherry-projects' ),
									'description'   => esc_html__( 'Choose projects listing view layout.', 'cherry-projects' ),
									'value'         => 'grid-layout',
									'display_input' => false,
									'options'       => array(
										'grid-layout' => array(
											'label'   => esc_html__( 'Grid', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-grid.svg',
											'slave'   => 'projects-listing-layout-grid-layout',
										),
										'masonry-layout' => array(
											'label'   => esc_html__( 'Masonry', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-masonry.svg',
											'slave'   => 'projects-listing-layout-masonry-layout',
										),
										'justified-layout' => array(
											'label'   => esc_html__( 'Justified', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-justified.svg',
											'slave'   => 'projects-listing-layout-justified-layout',
										),
										'cascading-grid-layout' => array(
											'label'   => esc_html__( 'Cascading grid', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-cascading-grid.svg',
											'slave'   => 'projects-listing-layout-cascading-grid-layout',
										),
										'list-layout' => array(
											'label'   => esc_html__( 'List', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-listing.svg',
											'slave'   => 'projects-listing-layout-list-layout',
										),
									),
								),

								'loading_mode' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Pagination mode', 'cherry-projects' ),
									'description'   => esc_html__( 'Choose projects pagination mode', 'cherry-projects' ),
									'value'         => 'ajax-pagination-mode',
									'class'         => '',
									'display_input' => false,
									'options'       => array(
										'ajax-pagination-mode' => array(
											'label'   => esc_html__( 'Ajax pagination', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-pagination.svg',
										),
										'more-button-mode' => array(
											'label'   => esc_html__( 'More button', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-ajax-more-button.svg',
										),
										'lazy-loading-mode' => array(
											'label'   => esc_html__( 'Lazy loading', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-lazy-loading.svg',
										),
										'none-mode' => array(
											'label'   => esc_html__( 'None', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/loading-mode-none.svg',
										),
									),
								),

								'load_animation' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Loading animation', 'cherry-projects' ),
									'description'   => esc_html__( 'Choose posts loading animation', 'cherry-projects' ),
									'value'         => 'loading-animation-move-up',
									'class'         => '',
									'display_input' => false,
									'options'       => array(
										'loading-animation-fade' => array(
											'label'   => esc_html__( 'Fade animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fade.svg',
										),
										'loading-animation-scale' => array(
											'label'   => esc_html__( 'Scale animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-scale.svg',
										),
										'loading-animation-move-up' => array(
											'label'   => esc_html__( 'Move Up animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-move-up.svg',
										),
										'loading-animation-flip' => array(
											'label'   => esc_html__( 'Flip animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-flip.svg',
										),
										'loading-animation-helix' => array(
											'label'   => esc_html__( 'Helix animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-helix.svg',
										),
										'loading-animation-fall-perspective' => array(
											'label'   => esc_html__( 'Fall perspective animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fall-perspective.svg',
										),
									),
								),

								'hover_animation' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Hover animation', 'cherry-projects' ),
									'description'   => esc_html__( 'Choose posts images hover animation', 'cherry-projects' ),
									'value'         => 'simple-scale',
									'class'         => '',
									'display_input' => false,
									'options'       => array(
										'simple-fade' => array(
											'label'   => esc_html__( 'Fade', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/hover-fade.svg',
										),
										'simple-scale' => array(
											'label'   => esc_html__( 'Scale', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/hover-scale.svg',
										),
										'custom' => array(
											'label'   => esc_html__( 'Custom', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/inherit.svg',
										),
									),
								),

								'filter_visible' => array(
									'type'        => 'switcher',
									'title'       => esc_html__( 'Filters', 'cherry-projects' ),
									'description' => esc_html__( 'Enable/disable listing filters', 'cherry-projects' ),
									'value'       => 'true',
									'toggle' => array(
										'true_toggle'  => esc_html__( 'Show', 'cherry-projects' ),
										'false_toggle' => esc_html__( 'Hide', 'cherry-projects' ),
									),
								),

								'filter_type' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Filter type', 'cherry-projects' ),
									'description'   => esc_html__( 'Select if you want to filter posts by tag or by category.', 'cherry-projects' ),
									'value'         => 'category',
									'display-input' => true,
									'options'       => array(
										'category' => array(
											'label' => esc_html__( 'Category', 'cherry-projects' ),
											'slave' => 'projects-filter-type-category',
										),
										'tag' => array(
											'label' => esc_html__( 'Tag', 'cherry-projects' ),
											'slave' => 'projects-filter-type-tag',
										),
									),
								),

								'category_list' => array(
									'type'     => 'select',
									'title'    => esc_html__( 'Projects filter categories list', 'cherry-projects' ),
									'multiple' => true,
									'value'    => array(),
									'class'    => 'cherry-multi-select',
									'options'  => $category_list,
									'master'   => 'projects-filter-type-category',
								),

								'tags_list' => array(
									'type'             => 'select',
									'title'            => esc_html__( 'Projects filter tags list', 'cherry-projects' ),
									'multiple'         => true,
									'value'            => array(),
									'class'            => 'cherry-multi-select',
									'options'          => $tag_list,
									'master'           => 'projects-filter-type-tag',
								),

								'order_filter_visible' => array(
									'type'        => 'switcher',
									'title'       => esc_html__( 'Order filters', 'cherry-projects' ),
									'description' => esc_html__( 'Enable/disable order filters', 'cherry-projects' ),
									'value'       => 'false',
									'toggle'      => array(
										'true_toggle'  => 'On',
										'false_toggle' => 'Off',
										'true_slave'   => 'projects-order-filter-visible-true',
										'false_slave'  => 'projects-order-filter-visible-false',
									),
								),

								'order_filter_default_value' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Order filter default value', 'cherry-projects' ),
									'value'         => 'desc',
									'display-input' => true,
									'options'       => array(
										'desc' => array(
											'label' => esc_html__( 'DESC', 'cherry-projects' ),
										),
										'asc' => array(
											'label' => esc_html__( 'ASC', 'cherry-projects' ),
										),
									),
									'master'		=> 'projects-order-filter-visible-true',
								),

								'orderby_filter_default_value' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Order by filter default value', 'cherry-projects' ),
									'value'         => 'date',
									'display-input' => true,
									'options'       => array(
										'date' => array(
											'label' => esc_html__( 'Date', 'cherry-projects' ),
										),
										'name' => array(
											'label' => esc_html__( 'Name', 'cherry-projects' ),
										),
										'modified' => array(
											'label' => esc_html__( 'Modified', 'cherry-projects' ),
										),
										'comment_count' => array(
											'label' => esc_html__( 'Comments', 'cherry-projects' ),
										),
									),
									'master'		=> 'projects-order-filter-visible-true',
								),

								'posts_format' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Post Format', 'cherry-projects' ),
									'value'         => 'post-format-all',
									'display-input' => true,
									'options'       => array(
										'post-format-all' => array(
											'label' => esc_html__( 'All formats', 'cherry-projects' ),
										),
										'post-format-standard' => array(
											'label' => esc_html__( 'Standard', 'cherry-projects' ),
										),
										'post-format-image' => array(
											'label' => esc_html__( 'Image', 'cherry-projects' ),
										),
										'post-format-gallery' => array(
											'label' => esc_html__( 'Gallery', 'cherry-projects' ),
										),
										'post-format-audio' => array(
											'label' => esc_html__( 'Audio', 'cherry-projects' ),
										),
										'post-format-video' => array(
											'label' => esc_html__( 'Video', 'cherry-projects' ),
										),
									),
								),

								'single_term' => array(
									'type'            => 'text',
									'title'           => esc_html__( 'Single term slug', 'cherry-projects' ),
									'value'           => '',
								),

								'column_number' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Column number', 'cherry-projects' ),
									'description' => esc_html__( 'Select number of columns for masonry and grid projects layouts. (Min 2, max 6)', 'cherry-projects' ),
									'max_value'   => 6,
									'min_value'   => 2,
									'value'       => 3,
								),

								'post_per_page' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Posts per page', 'cherry-projects' ),
									'description' => esc_html__( 'Select how many posts per page do you want to display(-1 means that will show all projects)', 'cherry-projects' ),
									'max_value'   => 50,
									'min_value'   => -1,
									'value'       => 9,
								),

								'item_margin' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Item margin', 'cherry-projects' ),
									'description' => esc_html__( 'Select projects item margin (outer indent) value.', 'cherry-projects' ),
									'max_value'   => 50,
									'min_value'   => 0,
									'value'       => 4,
								),

								'justified_fixed_height' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Justified fixed height', 'cherry-projects' ),
									'description' => esc_html__( 'Select projects item justified height value.', 'cherry-projects' ),
									'max_value'   => 1000,
									'min_value'   => 50,
									'value'       => 300,
									'master'      => 'projects-listing-layout-justified-layout',
								),

								'grid_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Grid template', 'cherry-projects' ),
									'description' => esc_html__( 'Grid content template', 'cherry-projects' ),
									'value'       => 'grid-default.tmpl',
									'master'      => 'projects-listing-layout-grid-layout',
								),

								'masonry_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Masonry template', 'cherry-projects' ),
									'description' => esc_html__( 'Masonry content template', 'cherry-projects' ),
									'value'       => 'masonry-default.tmpl',
									'master'      => 'projects-listing-layout-masonry-layout',
								),

								'justified_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Justified template', 'cherry-projects' ),
									'description' => esc_html__( 'Justified content template', 'cherry-projects' ),
									'value'       => 'justified-default.tmpl',
									'master'      => 'projects-listing-layout-justified-layout',
								),

								'cascading_grid_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Cascading grid template', 'cherry-projects' ),
									'description' => esc_html__( 'Cascading grid template', 'cherry-projects' ),
									'value'       => 'cascading-grid-default.tmpl',
									'master'      => 'projects-listing-layout-cascading-grid-layout',
								),

								'list_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'List template', 'cherry-projects' ),
									'description' => esc_html__( 'List content template', 'cherry-projects' ),
									'value'       => 'list-default.tmpl',
									'master'      => 'projects-listing-layout-list-layout',
								),
							),
						),
						array(
							'title'       => esc_html__( 'Projects Terms', 'cherry-projects' ),
							'description' => esc_html__( 'The shortcode displays Category and Tag sections content listing with set parameters.', 'cherry-projects' ),
							'icon'        => '<span class="dashicons dashicons-category"></span>',
							'slug'        => 'cherry_projects_terms',
							'options'     => array(

								'term_type' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Filter type', 'cherry-projects' ),
									'description'   => esc_html__( 'Select if you want to filter posts by tag or by category.', 'cherry-projects' ),
									'value'         => 'category',
									'display-input' => true,
									'options'       => array(
										'category' => array(
											'label' => esc_html__( 'Category', 'cherry-projects' ),
										),
										'tag' => array(
											'label' => esc_html__( 'Tag', 'cherry-projects' ),
										),
									),
								),

								'listing_layout' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Terms listing layout', 'cherry-projects' ),
									'description'   => esc_html__( 'Choose terms listing view layout.', 'cherry-projects' ),
									'value'         => 'grid-layout',
									'display_input' => false,
									'options'       => array(
										'grid-layout' => array(
											'label'   => esc_html__( 'Grid', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-grid.svg',
											'slave'   => 'projects-listing-layout-grid-layout',
										),
										'masonry-layout' => array(
											'label'   => esc_html__( 'Masonry', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-masonry.svg',
											'slave'   => 'projects-listing-layout-masonry-layout',
										),
										'cascading-grid-layout' => array(
											'label'   => esc_html__( 'Cascading grid', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-cascading-grid.svg',
											'slave'   => 'projects-listing-layout-cascading-grid-layout',
										),
										'list-layout' => array(
											'label'   => esc_html__( 'List', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/list-layout-listing.svg',
											'slave'   => 'projects-listing-layout-list-layout',
										),
									),
								),

								'load_animation' => array(
									'type'          => 'radio',
									'title'         => esc_html__( 'Loading animation', 'cherry-projects' ),
									'description'   => esc_html__( 'Choose terms loading animation', 'cherry-projects' ),
									'value'         => 'loading-animation-move-up',
									'class'         => '',
									'display_input' => false,
									'options'       => array(
										'loading-animation-fade' => array(
											'label'   => esc_html__( 'Fade animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fade.svg',
										),
										'loading-animation-scale' => array(
											'label'   => esc_html__( 'Scale animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-scale.svg',
										),
										'loading-animation-move-up' => array(
											'label'   => esc_html__( 'Move Up animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-move-up.svg',
										),
										'loading-animation-flip' => array(
											'label'   => esc_html__( 'Flip animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-flip.svg',
										),
										'loading-animation-helix' => array(
											'label'   => esc_html__( 'Helix animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-helix.svg',
										),
										'loading-animation-fall-perspective' => array(
											'label'   => esc_html__( 'Fall perspective animation', 'cherry-projects' ),
											'img_src' => CHERRY_PROJECTS_URI . 'public/assets/images/svg/animation-fall-perspective.svg',
										),
									),
								),

								'column_number' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Column number', 'cherry-projects' ),
									'description' => esc_html__( 'Select number of columns for masonry and grid projects layouts. (Min 2, max 6)', 'cherry-projects' ),
									'max_value'   => 6,
									'min_value'   => 2,
									'value'       => 3,
								),

								'post_per_page' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Posts per page', 'cherry-projects' ),
									'description' => esc_html__( 'Select how many posts per page do you want to display(-1 means that will show all projects)', 'cherry-projects' ),
									'max_value'   => 50,
									'min_value'   => -1,
									'value'       => 6,
								),

								'item_margin' => array(
									'type'        => 'slider',
									'title'       => esc_html__( 'Item margin', 'cherry-projects' ),
									'description' => esc_html__( 'Select projects item margin (outer indent) value.', 'cherry-projects' ),
									'max_value'   => 50,
									'min_value'   => 0,
									'value'       => 4,
								),

								'grid_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Grid template', 'cherry-projects' ),
									'description' => esc_html__( 'Grid content template', 'cherry-projects' ),
									'value'       => 'terms-grid-default.tmpl',
								),

								'masonry_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Masonry template', 'cherry-projects' ),
									'description' => esc_html__( 'Masonry content template', 'cherry-projects' ),
									'value'       => 'terms-masonry-default.tmpl',
								),

								'cascading_grid_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'Cascading grid template', 'cherry-projects' ),
									'description' => esc_html__( 'Cascading grid template', 'cherry-projects' ),
									'value'       => 'terms-cascading-grid-default.tmpl',
								),

								'list_template' => array(
									'type'        => 'text',
									'title'       => esc_html__( 'List template', 'cherry-projects' ),
									'description' => esc_html__( 'List content template', 'cherry-projects' ),
									'value'       => 'terms-list-default.tmpl',
								),
							),
						),
					),
				)
			);
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
