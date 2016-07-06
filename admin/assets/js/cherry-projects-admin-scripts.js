(function($){
	"use strict";

	CherryJsCore.utilites.namespace('project_admin_theme_script');
	CherryJsCore.project_admin_theme_script = {
		ajaxRequest: null,
		ajaxRequestSuccess: true,
		init: function () {
			var self = this;

			if( CherryJsCore.status.is_ready ){
				self.readyRender( self );
			}else{
				CherryJsCore.variable.$document.on( 'ready', self.readyRender( self ) );
			}
		},
		readyRender: function ( self ) {

			var self = self,
				$projectsOptionsForm = $('#cherry-projects-options-form'),
				$saveButton = $('#cherry-projects-save-options', $projectsOptionsForm ),
				$defineAsDefaultButton = $('#cherry-projects-define-as-default', $projectsOptionsForm ),
				$restoreButton = $('#cherry-projects-restore-options', $projectsOptionsForm );

				$saveButton.on( 'click', {
					self: self,
					optionsForm: $projectsOptionsForm,
					ajaxRequestType: 'save'
				}, self.ajaxRequestFunction );

				$defineAsDefaultButton.on( 'click', {
					self: self,
					optionsForm: $projectsOptionsForm,
					ajaxRequestType: 'define_as_default'
				}, self.ajaxRequestFunction );

				$restoreButton.on( 'click', {
					self: self,
					optionsForm: $projectsOptionsForm,
					ajaxRequestType: 'restore'
				}, self.ajaxRequestFunction );

		},
		ajaxRequestFunction: function( event ) {
			var self = event.data.self,
				$projectsOptionsForm = event.data.optionsForm,
				$cherrySpinner = $('.cherry-spinner-wordpress', $projectsOptionsForm),
				ajaxRequestType = event.data.ajaxRequestType,
				serializeArray = $projectsOptionsForm.serializeObject(),
				data = {
					nonce: CherryJsCore.variable.security,
					action: 'cherry_projects_ajax_request',
					post_array: serializeArray,
					type: ajaxRequestType
				};

			if ( ! self.ajaxRequestSuccess ) {
				self.ajaxRequest.abort();
				self.noticeCreate( 'error-notice', cherryProjectsPluginSettings.please_wait_processing );
			}

			self.ajaxRequest = jQuery.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: data,
				cache: false,
				beforeSend: function(){
					self.ajaxRequestSuccess = false;
					$cherrySpinner.fadeIn();
				},
				success: function( response ) {
					self.ajaxRequestSuccess = true;
					$cherrySpinner.fadeOut();
					self.noticeCreate( response.type, response.message );
					if ( 'restore' === ajaxRequestType ) {
						window.location.href = cherryProjectsPluginSettings.redirect_url;
					}
				},
				dataType: 'json'
			} );

			return false;
		},
		noticeCreate: function( type, message ) {
			var notice = $( '<div class="notice-box ' + type + '"><span class="dashicons"></span><div class="inner">' + message + '</div></div>' ),
				rightDelta = 0,
				timeoutId;

			$( 'body' ).prepend( notice );
			reposition();
			rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
			notice.css( {'right' : rightDelta } );

			timeoutId = setTimeout( function () { notice.css( { 'right' : 10 } ).addClass( 'show-state' ) }, 100 );
			timeoutId = setTimeout( function () {
				rightDelta = -1 * ( notice.outerWidth( true ) + 10 );
				notice.css( { right: rightDelta } ).removeClass( 'show-state' );
			}, 4000 );
			timeoutId = setTimeout( function () {
				notice.remove(); clearTimeout( timeoutId );
			}, 4500 );

				function reposition(){
					var topDelta = 100;

					$( '.notice-box' ).each( function( index ) {
						$( this ).css( { top: topDelta } );
						topDelta += $( this ).outerHeight( true );
					} );
				}
		}
	}
	CherryJsCore.project_admin_theme_script.init();
}(jQuery));

