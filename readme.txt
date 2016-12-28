=== Cherry Projects ===

Contributors: TemplateMonster 2002
Tags: custom post type, projects, portfolio, cherry framework
Requires at least: 4.5
Tested up to: 4.5.3
Stable tag: 1.2.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Showcase your projects using a variety of layouts with Cherry Projects plugin.

== Description ==

Cherry Projects is a portfolio management system that lets you create your projects and display them using a large number of options. Use ready-made templates for taking control over each element of your project.

Cherry Projects is a standalone plugin, however it depends on the Cherry Framework package that comes with the plugin by default. You don't need to download any additional modules manually, just be aware of this dependency.

= Display settings =

* Project listing layout. Choose from 5 different variations on how to display your project listing (grid, masonry, justified, cascading grid, and list).
* Pagination mode. If the number of portfolio items is larger than the maximum number of projects per page, the pagination will be applied. Pick the pagination mode you'd like to use from the available: ajax pagination, more button, or lazy load.
* Loading animation. Select which animation to use when the posts appear on the page during the loading.
* Hover animation. The animation used on hover for each portfolio item.

= Sorting & Filtering =

Choose whether to display filtering for your projects or not. If you choose to display it, there's a number of options.

* Filter type. Choose whether you want to display projects based on their categories, or tags.
* Filter order. You can also decide in which order to display your projects. Select the sorting order from ASC and DESC values, or output them based on the date of creation, name, last modified date or number of comments.
* Post format. Select which projects to output based on their post format. You can output all formats, or choose from standard, image, gallery, audio, or video formats.

= Additional Settings =

In addition there are three more settings you can adjust:

* Column number. Select the number of columns for masonry and grid project layouts.
* Posts per page. Choose the maximum number of post per page you want to showcase.
* Item margin. Adjust the margin size between separate portfolio items.

= Customizable Templates =

Cherry Projects is using a simplified templating system with .tmpl files. Out-of-the-box the plugin comes with templates for each of the project listing layouts and available post formats, which are used for displaying project single pages. Create the unique look and feel of your portfolio by customizing existing templates, or creating your own.

== Installation ==

1. Upload "Cherry Projects" folder to the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Navigate to the "Cherry Projects" page available through the left menu

== Screenshots ==
1. Settings page.
2. Post edit page.

== Configuration ==

= Plugin Options =
All plugin options are gathered in Projects -> Settings

* Select team archive page - Choose the archive page for Team posts.
* Set posts number per archive page - Set the number of posts to display on the archive page and on the Team category pages. This option is not included into the shortcode.
* Select archive page columns number - Number of columns for the posts on the archive page and Team  category pages.  This option is not included into the shortcode (4 max).
* Select template for single team member page - Choose a proper template for a single Team member page.
* Select image size for single team member page - Choose a featured image size for a single team member page. In the dropdown menu you can choose from all available sizes. It is strongly recommended to use the Regenerate Thumbnails plugin before changing this option.
* Select template for team listing page - Choose a proper template for displaying Team posts items. (Works for archives page and category pages).
* Select image size for listing team member page - Choose featured image size for items in Team posts listing type. (Works for archives page and category pages). In the dropdown menu you can choose from all available sizes. It is strongly recommended to use the Regenerate Thumbnails plugin before changing this option.


= Shortcode cherry_projects =
Shortcode is used to display the projects list with set parameters. Shortcode attributes:

* listing_layout (default = 'grid-layout') - Select listing layout type
* loading_mode (default = 'ajax-pagination-mode') - Select project posts loading mode type
* loading_animation (default = 'loading-animation-move-up') - Select loading animation type
* hover_animation (default = 'simple-scale') - Select hover animation type
* filter_visible (default = true) - Show/hide filters for selected terms type
* filter_type (default = 'category') - Select posts filter type
* category_list (default = '') - Categories list for  ajax-filter
* tags_list (default = '') - Tag list for ajax-filter
* order_filter_visible (default = false) -  Show/hide order(desc/asc) and  order by(date/name/modified/comments) filters
* order_filter_default_value (default = 'desc') - Specify the order filter default value (asc/desc)
* orderby_filter_default_value (default = 'date') - Specify the order by filter default value(date/name/modified/comments)
* posts_format (default = 'post-format-all') - Choose a proper post format
* single_term (default = '') - Choose a particular term
* column_number (default = 3) - Choose columns number for masonry and grid layouts
* post_per_page (default = 9) - Number of posts per page
* item_margin (default = 4) - Margin between projects
* justified_fixed_height (default = 300) - Fixed height for images in justified listing layout
* masonry_template, grid_template, justified_template, cascading_grid_template, list_template - Template for changing the layout type of a project.

= Shortcode cherry_projects_terms =
The shortcode displays Category and Tag sections content listing with set parameters.

* term_type (default = 'category') - Select term type for listing category and tag sections.
* listing_layout (default = 'grid-layout') - Select listing layout type.
* loading_animation (default = 'loading-animation-fade') -Select loading animation type.
* column_number (default = 3) - Number of columns for masonry and grid layouts.
* post_per_page (default = 6) - Number of posts displayed on a page.
* item_margin (default = 10) - Margin between categories in the listing.
* masonry_template, grid_template, list_template - Choose a proper template for a specific project type.

= Templates =
The plugin offers simplified templating system for .tmpl files. 13 templates are available by default:

* grid-default.tmpl - Main template for displaying projects grid layout on the archive page and in the shortcode.
* masonry-default.tmpl - Main template for displaying projects masonry layout on the archive page and in the shortcode.
* justified-default.tmpl - Main template for displaying projects justified layout on the archive page and in the shortcode.
* cascading-default.tmpl - Main template for displaying projects cascading layout on the archive page and in the shortcode.
* list-default.tmpl - Main template for displaying projects list layout on the archive page and in the shortcode.
* standard-post-template.tmpl - Single post page template standard post.
* image-post-template.tmpl - Single post page template image post.
* gallery-post-template.tmpl - Single post page template gallery post.
* audio-post-template.tmpl - Single post page template audio post.
* video-post-template.tmpl - Single post page template video post.
* terms-grid-default.tmpl - Main template for displaying terms grid layout in the shortcode.
* terms-masonry-default.tmpl - Main template for displaying terms grid layout in the shortcode.
* terms-list-default.tmpl - Main template for displaying terms grid layout in the shortcode.

Standard templates can be rewritten in the theme. For that you need to create cherry-projects folder in the root catalog of the theme and copy the necessary templates in there. You can also add your own templates. For that you need to create a file with .tmpl extension in the same folder.

== Changelog ==

= 1.0.0 =

* Initial release

= 1.1.0 =

* Updated editing interface types of projects
* Fixed archive template
* Fixed bugs
* Added new filters
* Add new macros "termattachments" for projects_terms shortcode

= 1.2.0 =

* Added shortcodes generator
* Added new filters
* Fixed bugs
