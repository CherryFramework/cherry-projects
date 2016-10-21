// cherryPortfolioPlugin plugin
(function($){
	var methods = {
		init : function( options ) {

			var settings = {
				call: function() {}
			}

			return this.each( function() {
				if ( options ) {
					$.extend( settings, options );
				}

				var $this                  = $( this ),
					$projectsContainer     = $( '.projects-container', $this ),
					$projectsList          = $( '.projects-list', $projectsContainer ),
					$projectsFilters       = $( '.projects-filters', $this ),
					$projectsTermsFilters  = $( 'ul.projects-filters-list', $projectsFilters ),
					$projectsOrderFilters  = $( 'ul.order-filters', $projectsFilters ),
					projectsSettings       = $projectsContainer.data( 'settings' ),
					$ajaxLoader            = null,
					$ajaxMoreLoader        = null,
					$projectsMoreButton    = null,
					orderSetting           = {
						order:   $projectsFilters.data( 'order-default' ) || 'DESC',
						orderby: $projectsFilters.data( 'orderby-default' ) || 'date'
					},
					pagesCount             = Math.ceil( parseInt( $projectsList.data( 'all-posts-count' ) ) / parseInt( projectsSettings['post-per-page'] ) ),
					currentTermSlug        = projectsSettings['single-term'],
					currentPage            = 1
					ajaxRequestSuccess     = true,
					ajaxRequestObject      = null;

				( function () {
					$ajaxLoader = $( '.cherry-projects-ajax-loader' , $this );

					$ajaxLoader.css( { 'display': 'block'} ).fadeTo( 500, 1 );

					$ajaxMoreLoader = $('.projects-end-line-spinner .cherry-spinner', $this );

					getNewProjectsList( currentTermSlug, currentPage, orderSetting );

					if ( $projectsFilters[0] ) {
						addTermsFiltersEventsFunction();
					}

					if ( $projectsFilters[0] && $projectsOrderFilters[0] ) {
						addOrderFiltersEventsFunction();
					}

					addEventsFunction();
				} )();

				/*
				 * Add events for terms filters
				 */
				function addTermsFiltersEventsFunction() {

					$( 'li span', $projectsTermsFilters ).on( 'click', function() {
						var $parent = $(this).parent(),
							slug = '';

						if ( ! $( this ).parent().hasClass( 'active' ) ) {
							$( 'li' , $projectsTermsFilters ).removeClass( 'active' );
							$( this ).parent().addClass( 'active' );

							currentTermSlug = $(this).data( 'slug' );
							currentPage = 1;

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

						$this.toggleClass( 'dropdown-state' );
					})

					$projectsOrderFilters.on( 'click', '[data-filter-type="order"]', function() {
						var $this         = $( this ),
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
					});


					$( '.orderby-list', $projectsOrderFilters ).on( 'mouseleave', function() {
						$( this ).closest( '.dropdown-state' ).removeClass( 'dropdown-state' );
					} );

					$( $projectsOrderFilters ).on( 'mouseleave', '.dropdown-state', function() {
						$( this ).removeClass( 'dropdown-state' );
					} );
				}

				/*
				 * Add events for pagination
				 */
				function addPaginationEventsFunction() {
					var $projectsPagination     = $( '.projects-pagination', $projectsContainer ),
						$pageNavigation         = $( '.page-navigation', $projectsPagination );

					if ( $projectsPagination[0] ) {
						$( '.page-link > li span', $projectsPagination ).on( 'click', function() {
							var $this = $(this);

							if ( ! $this.parent().hasClass( 'active' ) ) {
								$( '.page-link > li', $projectsPagination ).removeClass( 'active' );
								$this.parent().addClass( 'active' );
								currentPage = $this.parent().index() + 1;

								getNewProjectsList( currentTermSlug, currentPage, orderSetting );
							}

						});

						if ( $pageNavigation[0] ) {
							$( '.next-page', $pageNavigation ).on( 'click', function() {
								currentPage++;
								getNewProjectsList( currentTermSlug, currentPage, orderSetting );
							});
							$( '.prev-page', $pageNavigation ).on( 'click', function() {
								currentPage--;
								getNewProjectsList( currentTermSlug, currentPage, orderSetting );
							});
						}
					}
				}

				/*
				 * Waypoint event
				 */
				function addWaypointEvent() {

					$projectsContainer.waypoint( function( direction ) {

						if ( 'down' === direction ) {

							if ( currentPage < pagesCount ) {
								currentPage++;

								getMoreProjects( currentTermSlug, currentPage, orderSetting );
							}
						}
					}, {
						offset: 'bottom-in-view'
					} );
				}

				/*
				 * Add events
				 */
				function addEventsFunction() {
					$( $projectsContainer ).on( 'click', '.projects-ajax-button', function() {

						if ( currentPage < pagesCount ) {
							currentPage++;

							if ( currentPage == pagesCount) {
								$( '.projects-ajax-button', $projectsContainer ).addClass( 'disabled' ).remove();
							}

							getMoreProjects( currentTermSlug, currentPage, orderSetting );
						}
					});

					jQuery( window ).on( 'resize.projects_layout_resize', function() {
						switch ( projectsSettings['list-layout'] ) {
							case 'grid-layout':
								gridLayoutRender( getResponsiveColumn() );
							break;
							case 'masonry-layout':
								masonryLayoutRender( getResponsiveColumn() );
							break;
						}
					} );
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
							posts_format: projectsSettings['posts-format'],
							filter_type: projectsSettings['filter-type'],
							post_per_page: projectsSettings['post-per-page']
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
							$ajaxLoader.css( { 'display': 'block' } ).fadeTo( 500, 1 );

							hideAnimation( 50 );
						},
						success: function( response ){
							ajaxRequestSuccess = true;

							$projectsContainer.html( response );

							pagesCount = Math.ceil( parseInt( $( '.projects-list', $projectsContainer ).data( 'all-posts-count' ) ) / parseInt( projectsSettings['post-per-page'] ) ),

							addPaginationEventsFunction();

							if ( 'lazy-loading-mode' === projectsSettings['loading-mode'] ) {
								addWaypointEvent();
							}

							switch ( projectsSettings['list-layout'] ) {
								case 'grid-layout':
									gridLayoutRender( getResponsiveColumn() );
								break;
								case 'masonry-layout':
									masonryLayoutRender( getResponsiveColumn() );
								break;
								case 'justified-layout':
									justifiedLayoutRender();
								break;
								case 'cascading-grid-layout':
									cascadingGridLayoutRender();
								break;
								case 'list-layout':
									listLayoutRender();
								break;
							}

							$projectsContainer.imagesLoaded( function() {
								$ajaxLoader.fadeTo( 500, 0, function() { $( this ).css( { 'display': 'none' } ); } );

								showAnimation( 0, 100 );

								Waypoint.refreshAll();

								CherryJsCore.cherryProjectsFrontScripts.magnificIconInit();
							} );

						}
					});
				}

				/*
				 * Get new projects list
				 */
				function getMoreProjects( slug, page, order ) {
					var data = {
						action: 'get_more_projects',
						settings: {
							slug: slug,
							page: page,
							list_layout: projectsSettings['list-layout'],
							loading_mode: projectsSettings['loading-mode'],
							order_settings: order,
							template: projectsSettings['template'],
							posts_format: projectsSettings['posts-format'],
							filter_type: projectsSettings['filter-type'],
							post_per_page: projectsSettings['post-per-page']
						}
					}

					if ( ! ajaxRequestSuccess ) {
						return;
					}

					ajaxRequestObject = $.ajax( {
						type: 'POST',
						url: cherryProjectsObjects.ajax_url,
						data: data,
						cache: false,
						beforeSend: function() {
							ajaxRequestSuccess = false;
							$ajaxMoreLoader.css( { 'display': 'block' } ).fadeTo( 500, 1 );
						},
						success: function( response ){
							ajaxRequestSuccess = true;

							var $projectsItemLength = $('.projects-item', $projectsContainer).length;

							$('.projects-list', $projectsContainer).append( response );

							switch ( projectsSettings['list-layout'] ) {
								case 'grid-layout':
									gridLayoutRender( getResponsiveColumn() );
								break;
								case 'masonry-layout':
									masonryLayoutRender( getResponsiveColumn() );
								break;
								case 'justified-layout':
									justifiedLayoutRender();
								break;
								case 'cascading-grid-layout':
									cascadingGridLayoutRender();
								break;
								case 'list-layout':
									listLayoutRender();
								break;
							}

							$projectsContainer.imagesLoaded( function() {
								$ajaxMoreLoader.fadeTo( 500, 0, function() { $( this ).css( { 'display': 'none' } ); } );

								showAnimation( $projectsItemLength, 100 );

								Waypoint.refreshAll();

								CherryJsCore.cherryProjectsFrontScripts.magnificIconInit();
							} );

						}
					});
				}

				/*
				 * Render grid layout
				 */
				function gridLayoutRender( columnNumber ) {
					var projectsList = $('.projects-item', $projectsContainer );

					projectsList.each( function( index ) {

						var $this     = $( this ),
							itemWidth = ( 100 / columnNumber ).toFixed(5);

						$this.css( {
							'-webkit-flex-basis': itemWidth + '%',
							'flex-basis': itemWidth + '%',
							'width': itemWidth + '%',
							'margin-bottom': projectsSettings['item-margin'] + 'px'
						} );

						$('.inner-wrapper', $this ).css( {
							'margin': ( +projectsSettings['item-margin'] / 2 ).toFixed(2) + 'px'
						} );

					});
				}

				/*
				 * Masonry grid layout
				 */
				function masonryLayoutRender( columnNumber ) {
					var projectsListWrap = $('.projects-list', $projectsContainer ),
						projectsList = $('.projects-item', $projectsContainer );

					projectsListWrap.css( {
						'-webkit-column-count': columnNumber,
						'column-count': columnNumber,
						'-webkit-column-gap': +projectsSettings['item-margin'],
						'column-gap': +projectsSettings['item-margin'],
					} );

					$( '.inner-wrapper', projectsList ).css( {
						'margin-bottom': +projectsSettings['item-margin']
					} );
				}

				/*
				 * Justified grid layout
				 */
				function justifiedLayoutRender() {
					var projectsListWrap = $('.projects-list', $projectsContainer ),
						projectsList = $('.projects-item', $projectsContainer );

						projectsList.each( function() {
							var $this = $(this),
								imageWidth = $this.data( 'image-width' ),
								imageHeight = $this.data( 'image-height' ),
								imageRatio = +imageWidth / +imageHeight,
								flexValue = Math.round( imageRatio * 100 ),
								newWidth = Math.round( +projectsSettings['fixed-height'] * imageRatio ),
								newHeight = 'auto';

							$this.css( {
								'flex-grow': flexValue,
								'flex-basis': newWidth,
								'max-width': +imageWidth
							} );

							$('.inner-wrapper', $this ).css( {
								'margin': Math.ceil( +projectsSettings['item-margin'] / 2 ) + 'px'
							} );

						} );
				}

				/*
				 * Cascading grid layout
				 */
				function cascadingGridLayoutRender() {
					var projectsListWrap = $('.projects-list', $projectsContainer ),
						projectsList = $('.projects-item', $projectsContainer );

						projectsList.each( function( index ) {
							var $this = $(this),
								imageWidth = $this.data( 'image-width' ),
								imageHeight = $this.data( 'image-height' ),
								newWidth = ( 100 / getCascadingIndex( index ) ).toFixed(2),
								margin = Math.ceil( +projectsSettings['item-margin'] / 2 );

							$this.css( {
								'width': +newWidth + '%',
								'max-width': +newWidth + '%'
							} );

							$('.inner-wrapper', $this ).css( {
								'margin': margin + 'px'
							} );

							projectsListWrap.css( {
								'margin-left': -margin + 'px',
								'margin-right': -margin + 'px',
							} );
						} );
				}

				/*
				 * Render list layout
				 */
				function listLayoutRender() {
					var projectsListWrap = $('.projects-list', $projectsContainer ),
						projectsList = $('.projects-item', $projectsContainer );

						projectsList.css( {
							'margin-bottom': +projectsSettings['item-margin']
						} );
				}

				/**
				 * GetCascadingIndex
				 */
				function getCascadingIndex( index ) {
					var index = index || 0,
						map = cherryProjectsObjects.cascadingListMap || [ 1, 2, 2, 3, 3, 3, 4, 4, 4, 4 ],
						counter = 0,
						mapIndex = 0;

						for ( var i = 0; i < index; i++ ) {
							counter++;

							if ( counter === map.length ) {
								counter = 0;
							}

							mapIndex = counter;
						};

						return map[ mapIndex ];
				}

				/*
				 * Show listing animation
				 */
				function showAnimation( startIndex, delta ){
					var counter = 1;
					$( '.projects-item', $projectsContainer ).each( function() {
						if ( $( this ).index() >= startIndex ) {
							showProjectsItem( $( this ), delta * parseInt( counter ) );
							counter++;
						}
					} );

				}

				function showProjectsItem( item, delay ) {
					var timeOutInterval = setTimeout( function() {
						item.removeClass( 'animate-cycle-show' );
					}, delay );
				}

				/*
				 * Hide listing animation
				 */
				function hideAnimation( delta ) {
					$( '.projects-item', $projectsContainer ).each( function() {
						hideProjectsItem( $( this ), delta * parseInt( $( this ).index() + 1 ) );
					} )

				}

				function hideProjectsItem( item, delay ) {
					var timeOutInterval = setTimeout( function() {
						item.addClass( 'animate-cycle-hide' );
					}, delay );
				}

				function getResponsiveColumn() {
					var columnPerView = +projectsSettings['column-number'],
						widthLayout   = getResponsiveLayout();

					switch ( widthLayout ) {
						case 'xl':
							columnPerView = +projectsSettings['column-number'];
							break
						case 'lg':
							columnPerView = Math.ceil( +projectsSettings['column-number'] / 2 );
							break
						case 'md':
							columnPerView = Math.ceil( +projectsSettings['column-number'] / 3 );
							break
						case 'sm':
							columnPerView = Math.ceil( +projectsSettings['column-number'] / 4 );
							break
						case 'xs':
							columnPerView = 1;
							break
					}
					return columnPerView;
				}

				function getResponsiveLayout() {
					var windowWidth   = $( window ).width(),
						widthLayout   = 'xs';

					if ( windowWidth >= 544 ) {
						widthLayout = 'sm';
					}

					if ( windowWidth >= 768 ) {
						widthLayout = 'md';
					}

					if ( windowWidth >= 992 ) {
						widthLayout = 'lg';
					}

					if ( windowWidth >= 1200 ) {
						widthLayout = 'xl';
					}

					return widthLayout;
				}

			});
		},
		destroy: function() {},
		update: function( content ) {}
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
