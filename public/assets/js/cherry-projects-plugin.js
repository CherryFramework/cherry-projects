// cherryPortfolioPlugin plugin


(function($){
	var methods = {
		init : function( options ) {

			var settings = {
				call: function() {}
			}

			return this.each( function() {
				if ( options ){
					$.extend( settings, options );
				}

				var $this                  = $(this),
					$projectsContainer     = $( '.projects-container', $this ),
					$projectsList          = $( '.projects-list', $projectsContainer ),
					$projectsFilters       = $( '.projects-filters', $this ),
					$projectsTermsFilters  = $( 'ul.projects-filters-list', $projectsFilters ),
					$projectsOrderFilters  = $( 'ul.order-filters', $projectsFilters ),
					projectsSettings       = $projectsContainer.data( 'settings' ),
					ajaxLoader             = null,
					orderSetting           = {
						order:   $projectsFilters.data( 'order-default' ) || 'DESC',
						orderby: $projectsFilters.data( 'orderby-default' ) || 'date'
					},
					pagesCount             = Math.ceil( parseInt( $projectsList.data( 'all-posts-count' ) ) / parseInt( projectsSettings['post-per-page'] ) ),
					currentTermSlug        = '',
					currentPage            = 1
					ajaxRequestSuccess     = true,
					ajaxRequestObject      = null;


					/*column = portfolioContainer.data('column'),
					postPerPage = portfolioContainer.data('post-per-page'),
					itemMargin = parseInt( portfolioContainer.data('item-margin') ),
					loadingMode = portfolioContainer.data('loading-mode'),
					fixedHeight = portfolioContainer.data('fixed-height'),
					listLayout = portfolioContainer.data('list-layout'),
					template = portfolioContainer.data('template'),
					postsFormat = portfolioContainer.data('posts-format'),*/
					/*isotopeOptions = {
						itemSelector : '.portfolio-item',
						resizable: false,
						masonry: { columnWidth: Math.floor( $('.portfolio-list', portfolioContainer).width() / column ) }
					},
					currentSlug = '',
					currentPaginationPage = 1,
					allPageLenght = 0,
					allPageLenght_temp = 0,
					ajaxMoreClicked = false,
					orderSetting = {
						order: 'DESC',
						orderby: 'date'
					},
					ajaxGetNewRequest = null,
					ajaxGetMoreRequest = null,
					ajaxGetNewRequestSuccess = true;
					ajaxLoader = null,*/

					console.log(projectsSettings);

				(function () {
					if ( ! $('.cherry-projects-ajax-loader')[0] ) {
						$('body').append('<div class="cherry-projects-ajax-loader"><div class="cherry-spinner cherry-spinner-double-bounce"><div class="cherry-double-bounce1"></div><div class="cherry-double-bounce2"></div></div></div>');
						ajaxLoader = $('.cherry-projects-ajax-loader');
					}else{
						ajaxLoader = $('.cherry-projects-ajax-loader');
					}

					ajaxLoader.css( { 'display': 'block'} ).fadeTo( 500, 1 );

					getNewProjectsList( currentTermSlug, currentPage, orderSetting );

					if ( $projectsFilters[0] ) {
						addTermsFiltersEventsFunction();
					}

					if ( $projectsFilters[0] && $projectsOrderFilters[0] ) {
						addOrderFiltersEventsFunction();
					}
				})();

				/*
				 * Add events for terms filters
				 */
				function addTermsFiltersEventsFunction() {

					$( 'li span', $projectsTermsFilters ).on( 'click', function() {
						var $parent = $(this).parent()
							slug = '';

						if ( ! $( this ).parent().hasClass( 'active' ) ) {
							$( 'li' , $projectsTermsFilters ).removeClass( 'active' );
							$( this ).parent().addClass( 'active' );

							currentTermSlug = $(this).data('slug');

							getNewProjectsList( currentTermSlug, currentPage, orderSetting );
						}
					});
				}

				/*
				 * Add events for order filters
				 */
				function addOrderFiltersEventsFunction() {
					$projectsOrderFilters.on( 'click', '[data-filter-type="orderby"]', function() {
						var $this = $(this);

						$this.toggleClass('dropdown-state');
					})

					$projectsOrderFilters.on( 'click', '[data-filter-type="order"]', function() {
						var $this         = $( this ) ,
							$descLabel    = $this.data('desc-label'),
							$ascLabel     = $this.data('asc-label'),
							order         = '';

						if ( $descLabel === $( '.current', $this ).text() ) {
							$( '.current', $this ).html( $ascLabel );
							orderSetting.order = 'asc';
						} else {
							$( '.current', $this ).html( $descLabel );
							orderSetting.order = 'desc';
						}

						getNewProjectsList( currentTermSlug, currentPage, orderSetting );
					})

					$projectsOrderFilters.on( 'click', '.orderby-list li', function() {
						var $this      = $( this ),
							$parent    = $this.parents('[data-filter-type="orderby"]'),
							orderby    = $this.data('orderby');

						if ( $parent.hasClass( 'dropdown-state' ) ) {
							$parent.removeClass( 'dropdown-state' );
						}

						$( '.current', $parent ).html( $this.html() );
						$( 'li', $parent ).removeClass( 'active' );
						$this.addClass( 'active' );

						orderSetting.orderby = orderby;

						getNewProjectsList( currentTermSlug, currentPage, orderSetting );
					})

					/*switch( loadingMode ){
						case 'ajax-pagination':
							$('.portfolio-pagination > ul > li a', _this).on('click', function(e){
								ajaxPaginationLinkClickEventFunction( $(this) );
							})
							$('.portfolio-pagination .page-nav .next-page', _this).on('click', function(e){
								ajaxNavigationClickEvent( $(this), 'next' );
							})
							$('.portfolio-pagination .page-nav .prev-page', _this).on('click', function(e){
								ajaxNavigationClickEvent( $(this), 'prev' );
							})
						break
						case 'more-button':
							$('.portfolio-ajax-button .load-more-button a', _this).on('click', function(e){
								ajaxMoreButtonClickEventFunction();
							})
						break
					}
					// update columnWidth on window resize
					jQuery(window).on('resize.portfolio_layout_resize', function(){
						mainResizer();
					});*/
				}

				/*
				 * Get new projects list
				 */
				function getNewProjectsList( slug, page, order ) {
					var data = {
						action: 'get_new_projects',
						settings: {
							slug: slug,
							page: page,
							list_layout: projectsSettings['list-layout'],
							loading_mode: projectsSettings['loading-mode'],
							order_settings: order,
							template: projectsSettings['template'],
							posts_format: projectsSettings['posts-format']
						}
					}

					if ( ! ajaxRequestSuccess ) {
						ajaxRequestObject.abort();
					}

					ajaxRequestObject = $.ajax( {
						type: 'POST',
						url: cherryProjectsObjects.ajax_url,
						data: data,
						cache: false,
						beforeSend: function() {
							ajaxRequestSuccess = false;
							ajaxLoader.css( { 'display': 'block' } ).fadeTo( 500, 1 );
						},
						success: function( response ){
							ajaxRequestSuccess = true;
							ajaxLoader.fadeTo( 500, 0, function() { $( this ).css( { 'display': 'none' } ); } );

							$projectsContainer.html( response );

							switch ( projectsSettings['list-layout'] ) {
								case 'grid-layout':
									gridLayoutRender();
								break;
								case 'masonry-layout':

								break;
							}

						}
					});
				}

				/*
				 * Render grid layout
				 */
				function gridLayoutRender( response ) {
					var projectsList = $('.projects-item', $projectsContainer );

					projectsList.each( function( index ) {

						var $this     = $( this ),
							//itemWidth = Math.ceil( $projectsContainer.width() / +projectsSettings['column-number'] ) - Math.ceil( +projectsSettings['item-margin'] / 2 );
							itemSpace = $projectsContainer.width() - ( ( +projectsSettings['column-number'] -1 ) * +projectsSettings['item-margin'] ),
							itemWidth = Math.ceil( $projectsContainer.width() / +projectsSettings['column-number'] ) - ( itemSpace / ( +projectsSettings['column-number'] -1 ) );
							console.log(itemSpace);

						$this.css( {
							//'width': itemWidth + 'px',
							'-webkit-flex-basis': itemWidth + 'px',
							'flex-basis': itemWidth + 'px',
							'margin-bottom': projectsSettings['item-margin'] + 'px'
						} );

					});
				}

			});
		},
		destroy: function( ) { },
		update: function( content ) { }
	};

	$.fn.cherryProjectsPlugin = function( method ) {
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method with name ' + method + ' is not exist for jQuery.cherryProjectsPlugin' );
		}
	}//end plugin

})(jQuery)