(function($){
	"use strict";

	CherryJsCore.utilites.namespace('cherryProjectsFrontSingleScripts');
	CherryJsCore.cherryProjectsFrontSingleScripts = {
		init: function () {
			var self = this;

			if( CherryJsCore.status.is_ready ){
				self.readyRender( self );
			}else{
				CherryJsCore.variable.$document.on( 'ready', self.readyRender( self ) );
			}

		},
		readyRender: function( self ) {

			self.skillsListInit( self );
			self.imagesListingInit( self );
			self.sliderInit( self );
		},
		skillsListInit: function( self ) {
			$( '.cherry-projects-single-skills-list li ' ).each( function() {
				var $this = $( this ),
					skillValue = $( '.skill-bar', $this).data( 'skill-value' );
					$( '.skill-bar span', $this).css( {
						'width': skillValue + '%'
					} );
			} );
		},
		imagesListingInit: function( self ) {
			$('.cherry-projects-additional-image-list').magnificPopup({
				delegate: 'a',
				type: 'image',
				gallery: {
					enabled: true
				}
			});

			$('.featured-image').magnificPopup({
				delegate: 'a',
				type: 'image',
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

			$('.cherry-projects-additional-image-list').magnificPopup({
				delegate: 'a',
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

			$( '.cherry-projects-additional-image-list' ).each( function() {
				var $this         = $( this ),
					$thisList     = $( '.additional-image-list', $this ),
					$listItems    = $( '.image-item', $thisList ),
					listingLayout = $thisList.data( 'listing-layout' ),
					columnNumber  = $thisList.data( 'column-number' ),
					imageMargin  = $thisList.data( 'image-margin' );

					switch ( listingLayout ) {
						case 'grid-layout':
							var itemWidth = ( 100 / +columnNumber ).toFixed(3);
							$listItems.css( {
								'-webkit-flex-basis': itemWidth + '%',
								'flex-basis': itemWidth + '%',
							} );

							$('.inner-wrapper', $listItems ).css( {
								'margin': Math.floor( imageMargin / 2 ) + 'px',
							} );

							$thisList.css( {
								'margin': - Math.floor( imageMargin / 2 ) + 'px',
							} );

						break;
						case 'masonry-layout':
							$thisList.css( {
								'-webkit-column-count': +columnNumber,
								'column-count': +columnNumber,
								'-webkit-column-gap': +imageMargin,
								'column-gap': +imageMargin,
							} );

							$('.inner-wrapper', $listItems ).css( {
								'margin-bottom': imageMargin + 'px',
							} );

						break;
					}
			} );
		},
		sliderInit: function( self ) {
			$( '.cherry-projects-slider__instance' ).each( function() {
				var slider = $(this),
					settings = slider.data('settings'),
					sliderId = settings['id'];

				if ( $( '.projects-slider__item', '#' + sliderId ).length > 0 ) {
					$( '#' + sliderId ).sliderPro( {
						width: settings['width'],
						height: settings['height'],
						orientation: 'horizontal',
						imageScaleMode: settings['scale-mode'],
						forceSize: settings['force-size'],
						aspectRatio: -1,
						visibleSize: settings['visible-size'],
						slideDistance: +settings['distance'],
						slideAnimationDuration: +settings['duration'],
						fade: false,
						arrows: settings['navigation'],
						fadeArrows: true,
						buttons: false,
						autoplay: settings['autoplay'],
						fullScreen: true,
						shuffle: false,
						loop: settings['loop'],
						waitForLayers: false,
						thumbnailArrows: false,
						thumbnailsPosition: settings['thumbnails-position'],
						thumbnailWidth: settings['thumbnails-width'],
						thumbnailHeight: settings['thumbnails-height'],
						init: function() {
							$( this ).resize();
						},
						breakpoints: {
							992: {
								height: +settings['height'] * 0.75,
							},
							768: {
								height: +settings['height'] * 0.5
							}
						}
					} );
				}
			});//each end
		}
	}
	CherryJsCore.cherryProjectsFrontSingleScripts.init();
}(jQuery));

