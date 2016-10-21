(function($){
	"use strict";

	CherryJsCore.utilites.namespace('cherryProjectsFrontScripts');
	CherryJsCore.cherryProjectsFrontScripts = {
		init: function () {
			CherryJsCore.variable.$document.on( 'ready', this.readyRender.bind( this ) );
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

		projectsTermsInit: function() {
			var self                  = this,
				$projectsTermWrapper  = $( '.cherry-projects-terms-wrapper' ),
				$projectsTermInstance = $( '.projects-terms-container', $projectsTermWrapper ),
				$loader               = $( '.cherry-projects-ajax-loader' , $projectsTermWrapper );

			$loader.css( { 'display': 'block' } ).fadeTo( 500, 1 );

			$projectsTermInstance.each( function( index ) {
				var $instance        = $( this ),
					instanceSettings = $instance.data( 'settings' ),
					columnNumber     = self.getResponsiveColumn( +instanceSettings['column-number'] );

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
						var columnNumber = self.getResponsiveColumn( +instanceSettings['column-number'] );

						switch ( instanceSettings['list-layout'] ) {
							case 'grid-layout':
								self.gridLayoutRender( $instance, columnNumber, instanceSettings['item-margin'] );
							break;
							case 'masonry-layout':
								self.masonryLayoutRender( $instance, columnNumber, instanceSettings['item-margin'] );
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

			$( '.projects-terms-list', instance ).css( {
				'-webkit-column-count': columnNumber,
				'column-count': columnNumber,
				'-webkit-column-gap': +margin,
				'column-gap': +margin,
			} );

			$( '.inner-wrapper', $itemlist ).css( {
				'margin-bottom': +margin
			} );
		},

		cascadingGridLayoutRender: function( instance, marginItem ) {
			var $itemlist = $( '.projects-terms-item', instance );

			$itemlist.each( function( index ) {
					var $this    = $( this ),
						newWidth = ( 100 / getCascadingIndex( index ) ).toFixed( 2 ),
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

			function getCascadingIndex ( index ) {
				var index = index || 0,
					map = cherryProjectsTermObjects.cascadingListMap || [ 1, 2, 2, 3, 3, 3, 4, 4, 4, 4 ],
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

		getResponsiveColumn: function( columns ) {
			var columnPerView = +columns,
				widthLayout   = this.getResponsiveLayout();

			switch ( widthLayout ) {
				case 'xl':
					columnPerView = +columns;
					break
				case 'lg':
					columnPerView = Math.ceil( columnPerView / 2 );
					break
				case 'md':
					columnPerView = Math.ceil( columnPerView / 3 );
					break
				case 'sm':
					columnPerView = Math.ceil( columnPerView / 4 );
					break
				case 'xs':
					columnPerView = 1;
					break
			}
			return columnPerView;
		},

		getResponsiveLayout: function() {
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
		},


	}
	CherryJsCore.cherryProjectsFrontScripts.init();
}(jQuery));

