jQuery(document).ready(function() {
	var
		postFormat
	,	ajaxRequest = null
	,	ajaxRequestSuccess = true
	,	ajaxRequestFunction = null
	,	settingsContainer = jQuery('#cherry-portfolio-post-format-options')
	;

	settingsContainer.append('<span class="ajax-loader"></span>');

	jQuery('#formatdiv #post-formats-select input').on('click', function(){
		var formatClass
		postFormat = jQuery(this).val();
		if(postFormat == '0'){ postFormat = 'standart'; }

		if( jQuery('.'+postFormat+'-post-format-settings', settingsContainer).length != 0 ){
			jQuery('.inside .settings-item', settingsContainer).hide();
			jQuery('.'+postFormat+'-post-format-settings', settingsContainer).fadeIn();
		}else{
			ajaxRequestFunction();
		}

	})

	ajaxRequestFunction = function(){
		var
			data = {
				action: 'get_new_format_metabox',
				post_format: postFormat,
				post_id : jQuery('#post_ID').val()
			};

			if( ajaxRequest != null && ajaxRequestSuccess){
				ajaxRequest.abort();
			}

			ajaxRequest = jQuery.ajax({
				type: 'POST',
				url: portfolio_post_format_ajax.url,
				data: data,
				cache: false,
				beforeSend: function(){
					ajaxRequestSuccess = false;
					jQuery('.ajax-loader', settingsContainer).fadeIn();
				},
				success: function(response){
					ajaxRequestSuccess = true;
					jQuery('.ajax-loader', settingsContainer).hide();
					jQuery('.inside .settings-item', settingsContainer).hide();
					jQuery('.inside', settingsContainer).prepend( response );
					CHERRY_API.interface_builder.init( jQuery('.inside .settings-item', settingsContainer).eq(0) );
					//jQuery.cherryInterfaceBuilder.CallInterfaceBuilder( jQuery('.inside .settings-item', settingsContainer).eq(0) );
				},
				dataType: 'html'
			});
	}
});