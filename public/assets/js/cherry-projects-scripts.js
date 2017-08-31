var cherryProjectsFrontScripts = null;

( function( $, elementor ) {
	"use strict";

	cherryProjectsFrontScripts = {
		init: function () {
			$( document ).on( 'ready', this.readyRender.bind( this ) );
		},

		elementorInit: function () {
			// Elementor compatibility hooks init
			if ( elementor ) {
				elementor.hooks.addAction(
					'frontend/element_ready/cherry_projects.default',
					function( $scope ) {
						$( '.cherry-projects-wrapper', $scope ).cherryProjectsPlugin();
					}
				);

				elementor.hooks.addAction(
					'frontend/element_ready/cherry_projects_terms.default',
					function( $scope ) {
						cherryProjectsFrontScripts.projectsTermsInit( $( '.cherry-projects-terms-wrapper', $scope ) );
					}
				);
			}
		},

		readyRender: function () {
			this.projectsPluginInit( this );
			this.magnificInit( this );
			this.projectsTermsInit();
		},

		projectsPluginInit: function() {
			if ( $( '.cherry-projects-wrapper' )[0] ) {
				$( '.cherry-projects-wrapper' ).cherryProjectsPlugin( {} );
			}
		},

		magnificIconInit: function() {

			if ( window.elementorFrontend ) {
				return false;
			}

			if ( $( '.zoom-link' )[0] ){
				$( '.zoom-link' ).magnificPopup({type: 'image'});
			}
		},

		magnificInit: function() {
			$( '.cherry-projects-wrapper' ).magnificPopup({
				delegate: '.featured-image a',
				type: 'image',
				gallery: {
					enabled: true
				},
				mainClass: 'mfp-with-zoom',
				zoom: {
					enabled: true,
					duration: 300,
					easing: 'ease-in-out',
					opener: function(openerElement) {
						return openerElement.is('img') ? openerElement : openerElement.find('img');
					}
				}
			});
		},

		projectsTermsInit: function( selector ) {
			var self                  = cherryProjectsFrontScripts,
				$projectsTermWrapper  = selector || $( '.cherry-projects-terms-wrapper' ),
				$projectsTermInstance = $( '.projects-terms-container', $projectsTermWrapper ),
				$loader               = $( '.cherry-projects-ajax-loader' , $projectsTermWrapper );

			$loader.css( { 'display': 'block' } ).fadeTo( 500, 1 );

			$projectsTermInstance.each( function( index ) {
				var $instance        = $( this ),
					instanceSettings = $instance.data( 'settings' ),
					columnNumber     = self.getResponsiveColumn( instanceSettings );

					$instance.imagesLoaded( function() {
						$loader.fadeTo( 500, 0, function() { $( this ).css( { 'display': 'none' } ); } );
						self.showAnimation( $( '.projects-terms-item', $instance ), 0, 100 );
					} );

					switch ( instanceSettings['list-layout'] ) {
						case 'grid-layout':
							self.gridLayoutRender( $instance, columnNumber, instanceSettings['item-margin'] );
						break;
						case 'masonry-layout':
							self.masonryLayoutRender( $instance, columnNumber, instanceSettings['item-margin'] );
						break;
						case 'cascading-grid-layout':
							self.cascadingGridLayoutRender( $instance, instanceSettings['item-margin'] );
						break;
					}

					jQuery( window ).on( 'resize.projects_layout_resize', function() {
						var columnNumber = self.getResponsiveColumn( instanceSettings );

						switch ( instanceSettings['list-layout'] ) {
							case 'grid-layout':
								self.gridLayoutRender( $instance, columnNumber, instanceSettings['item-margin'] );
							break;
							case 'cascading-grid-layout':
								self.cascadingGridLayoutRender( $instance, instanceSettings['item-margin'] );
							break;
						}
					} );
			} );
		},

		gridLayoutRender: function( instance, columnNumber, margin ) {
			var $itemlist = $( '.projects-terms-item', instance );

			$itemlist.each( function( index ) {
				var $this     = $( this ),
					itemWidth = ( 100 / columnNumber ).toFixed(5);

				$this.css( {
					'-webkit-flex-basis': itemWidth + '%',
					'flex-basis': itemWidth + '%',
					'width': itemWidth + '%',
					'margin-bottom': margin + 'px'
				} );

				$('.inner-wrapper', $this ).css( {
					'margin': ( +margin / 2 ).toFixed(2) + 'px'
				} );

			});
		},

		masonryLayoutRender: function( instance, columnNumber, margin ) {
			var $itemlist = $( '.projects-terms-item', instance );

			salvattore.init();

			$( '.inner-wrapper', $itemlist ).css( {
				'margin': +margin
			} );
		},

		cascadingGridLayoutRender: function( instance, marginItem ) {
			var $itemlist = $( '.projects-terms-item', instance ),
				self = this;

			$itemlist.each( function( index ) {
					var $this    = $( this ),
						newWidth = ( 100 / getCascadingIndex( index, self ) ).toFixed( 2 ),
						margin   = Math.ceil( +marginItem / 2 );

					$this.css( {
						'width': +newWidth + '%',
						'max-width': +newWidth + '%'
					} );

					$('.inner-wrapper', $this ).css( {
						'margin': margin + 'px'
					} );
				}
			);

			function getCascadingIndex ( index, self ) {
				var index = index || 0,
					map = [],
					counter = 0,
					mapIndex = 0;

					switch ( self.getResponsiveLayout() ) {
						case 'xl':
							map = cherryProjectsTermObjects.cascadingListMap || [ 1, 2, 2, 3, 3, 3, 4, 4, 4, 4 ];
							break
						case 'lg':
							map = [ 1, 2, 2, 3, 3, 3 ];
							break
						case 'md':
							map = [ 1, 2, 2 ];
							break
						case 'sm':
							map = [ 1, 2, 2 ];
							break
						case 'xs':
							map = [ 1 ];
							break
					}

					for ( var i = 0; i < index; i++ ) {
						counter++;

						if ( counter === map.length ) {
							counter = 0;
						}

						mapIndex = counter;
					};

					return map[ mapIndex ];
			}
		},

		showAnimation: function( itemlist, startIndex, delta ) {
			var counter = 1;

			itemlist.each( function() {
				var $this = $( this ),
				timeOutInterval;

				timeOutInterval = setTimeout( function() {
					$this.removeClass( 'animate-cycle-show' );
				}, delta * parseInt( counter ) );

				counter++;
			} );

		},

		getResponsiveColumn: function( settings ) {
			var columnPerView              = +settings['column-number'] || 4,
				widthLayout                = this.getResponsiveLayout(),
				columnNumberLaptop         = +settings['column-number-laptop'] || 3,
				columnNumberAlbumTablet    = +settings['column-number-album-tablet'] || 3,
				columnNumberPortraitTablet = +settings['column-number-portrait-tablet'] || 2,
				columnNumberMobile         = +settings['column-number-mobile'] || 1;

			switch ( widthLayout ) {
				case 'xl':
					columnPerView = +columnPerView;
					break
				case 'lg':
					columnPerView = columnNumberLaptop;
					break
				case 'md':
					columnPerView = columnNumberAlbumTablet;
					break
				case 'sm':
					columnPerView = columnNumberPortraitTablet;
					break
				case 'xs':
					columnPerView = columnNumberMobile;
					break
			}

			return columnPerView;
		},

		getResponsiveLayout: function() {
			var windowWidth   = $( window ).width(),
				widthLayout   = 'xs';

			if ( windowWidth >= 600 ) {
				widthLayout = 'sm';
			}

			if ( windowWidth >= 900 ) {
				widthLayout = 'md';
			}

			if ( windowWidth >= 1200 ) {
				widthLayout = 'lg';
			}

			if ( windowWidth >= 1600 ) {
				widthLayout = 'xl';
			}

			return widthLayout;
		},
	}

	cherryProjectsFrontScripts.init();

	$( window ).on( 'elementor/frontend/init', cherryProjectsFrontScripts.elementorInit );

}( jQuery, window.elementorFrontend ) );

