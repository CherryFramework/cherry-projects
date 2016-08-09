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
			var self = this,
				$projectsTermWrapper = $( '.cherry-projects-terms-wrapper' ),
				$projectsTermInstance = $( '.projects-terms-container', $projectsTermWrapper ),
				$loader = $( '.cherry-projects-ajax-loader' , $projectsTermWrapper );

			$loader.css( { 'display': 'block' } ).fadeTo( 500, 1 );

			$projectsTermInstance.each( function( index ) {
				var $instance        = $( this ),
					instanceSettings = $instance.data( 'settings' );

					$instance.imagesLoaded( function() {
						$loader.fadeTo( 500, 0, function() { $( this ).css( { 'display': 'none' } ); } );
						self.showAnimation( $( '.projects-terms-item', $instance ), 0, 100 );
					} );

					switch ( instanceSettings['list-layout'] ) {
						case 'grid-layout':
							self.gridLayoutRender( $instance, instanceSettings['column-number'], instanceSettings['item-margin'] );
						break;
						case 'masonry-layout':
							self.masonryLayoutRender( $instance, instanceSettings['column-number'], instanceSettings['item-margin'] );
						break;
					}

			});

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

		}

	}
	CherryJsCore.cherryProjectsFrontScripts.init();
}(jQuery));

