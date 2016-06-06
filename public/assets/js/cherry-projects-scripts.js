(function($){
	"use strict";

	CherryJsCore.utilites.namespace('cherryProjectsFrontScripts');
	CherryJsCore.cherryProjectsFrontScripts = {
		init: function () {
			var self = this;

			if( CherryJsCore.status.is_ready ){
				self.readyRender( self );
			}else{
				CherryJsCore.variable.$document.on( 'ready', self.readyRender( self ) );
			}

		},
		readyRender: function ( self ) {

			if( $('.cherry-projects-wrapper')[0] ){
				$('.cherry-projects-wrapper').cherryProjectsPlugin({});
			}

			$('.cherry-projects-wrapper').magnificPopup({
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
	}
	CherryJsCore.cherryProjectsFrontScripts.init();
}(jQuery));

