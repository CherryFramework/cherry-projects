(function($){
	"use strict";

	CherryJsCore.utilites.namespace('cherryProjectsFrontScripts');
	CherryJsCore.cherryProjectsFrontScripts = {
		init: function () {
			var self = this;

			if ( CherryJsCore.status.is_ready ) {
				self.readyRender( self );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.readyRender( self ) );
			}

		},

		readyRender: function ( self ) {
			self.projectsPluginInit( self );
			self.magnificInit( self );
			self.projectsTermsInit( self );
		},

		projectsPluginInit: function( self ) {
			if ( $( '.cherry-projects-wrapper' )[0] ) {
				$( '.cherry-projects-wrapper' ).cherryProjectsPlugin( {} );
			}
		},

		magnificIconInit: function() {
			if ( $( '.zoom-link' )[0] ){
				$( '.zoom-link' ).magnificPopup({type: 'image'});
			}
		},

		magnificInit: function( self ) {
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
						console.log(openerElement);
						return openerElement.is('img') ? openerElement : openerElement.find('img');
					}
				}
			});
		},

		projectsTermsInit: function( self ) {
			var $projectsTermInstance = $('.projects-terms-container' );

			projectsTermInstance.each( function( index ) {
				var $instance        = $( this ),
					instanceSettings = $instance.data( 'settings' );

			});
		}
	}
	CherryJsCore.cherryProjectsFrontScripts.init();
}(jQuery));

