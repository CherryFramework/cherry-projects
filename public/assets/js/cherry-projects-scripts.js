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
			$('.cherry-projects-wrapper').cherryProjectsPlugin({});
		},
	}
	CherryJsCore.cherryProjectsFrontScripts.init();
}(jQuery));

