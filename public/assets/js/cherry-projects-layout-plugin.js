// cherryPortfolioLayoutPlugin plugin


(function($){
	var methods = {
		init : function( options ) {

			var settings = {
				call: function(){}
			}

			return this.each(function(){
				if ( options ){
					$.extend(settings, options);
				}

				var
					_this = $(this),
					ajaxGetNewRequest = null,
					ajaxGetMoreRequest = null,
					ajaxGetNewRequestSuccess = true,
					portfolioContainer = $('.portfolio-container', _this),
					portfolioList = $('.portfolio-list', portfolioContainer),
					ajaxLoaderContainer = null,
					column = portfolioContainer.data('column'),
					postPerPage = portfolioContainer.data('post-per-page'),
					itemMargin = parseInt( portfolioContainer.data('item-margin') ),
					loadingMode = portfolioContainer.data('loading-mode'),
					fixedHeight = portfolioContainer.data('fixed-height'),
					listLayout = portfolioContainer.data('list-layout'),
					template = portfolioContainer.data('template'),
					postsFormat = portfolioContainer.data('posts-format'),
					isotopeOptions = {
						itemSelector : '.portfolio-item',
						resizable: false,
						masonry: { columnWidth: Math.floor( $('.portfolio-list', portfolioContainer).width() / column ) }
					},
					currentSlug = '',
					currentPaginationPage = 1,
					allPageLenght = 0,
					allPageLenght_temp = 0,
					ajaxMoreClicked = false,
					orderSetting = {
						order: 'DESC',
						orderby: 'date'
					}
				;

				_constructor();
				function _constructor(){
					if( $('.cherry-portfolio-ajax-loader').length == 0 ){
						portfolioContainer.append('<div class="cherry-portfolio-ajax-loader"><div class="cherry-spinner cherry-spinner-double-bounce"><div class="cherry-double-bounce1"></div><div class="cherry-double-bounce2"></div></div></div>');
						ajaxLoaderContainer = $('.cherry-portfolio-ajax-loader');
					}else{
						ajaxLoaderContainer = $('.cherry-portfolio-ajax-loader');
					}

					ajaxLoaderContainer.css({"display":"block"}).fadeTo(500, 1);

					if( $('.portfolio-filter', _this)[0] ){
						orderSetting = {
							order: $('.portfolio-filter', _this).data('order-default').toUpperCase(),
							orderby: $('.portfolio-filter', _this).data('orderby-default')
						}
					}

					ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );

					allPageLenght = Math.ceil( parseInt($('.portfolio-list', portfolioContainer).data('all-posts-count'))/parseInt( postPerPage ) );
					allPageLenght_temp = allPageLenght;

					addEventsFunction();
				}

				function addEventsFunction(){
					$('.portfolio-filter > .filter a', _this).on('click', function(e){
						if( !$(this).parent().hasClass('active') ){
							$('.portfolio-filter > .filter li', _this).removeClass('active');
							$(this).parent().addClass('active');

							if(currentSlug !== $(this).data('slug')){
								currentPaginationPage = 1;
							}
							currentSlug = $(this).data('slug');

							ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );
						}
					});

					$('.portfolio-filter > .order-filter', _this).on('click', 'li', function(){
						var $this = $(this);
						$this.toggleClass('dropdown-state');
					})

					$('.portfolio-filter > .order-filter', _this).on('click', '[data-order="order"]', function(){
						var
							$this = $(this)
						,	desc_label = $this.data('desc-label')
						,	asc_label = $this.data('asc-label')
						;

						if( $this.hasClass('dropdown-state') ){
							$('.current', $this).html( asc_label );
							orderSetting.order = 'ASC';
						}else{
							$('.current', $this).html( desc_label );
							orderSetting.order = 'DESC';
						}

						if( 'more-button' == loadingMode ){
							currentPaginationPage = 1;
						}
						ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );
					})


					$('.portfolio-filter > .order-filter', _this).on('click', '.orderby-list > li', function(){
						var
							$this = $(this)
						,	$parent = $(this).parents('[data-orderby="orderby"]')
						,	$orderList = $parent.siblings('[data-order="order"]')
						,	orderby = $this.data('orderby')
						;

						if( $parent.hasClass('dropdown-state') ){
							$parent.removeClass('dropdown-state');
						}
						$('.current', $parent).html( $this.html() );

						$('li', $parent).removeClass('active');
						$this.addClass('active');

						orderSetting.orderby = orderby;

						if( 'more-button' == loadingMode ){
							currentPaginationPage = 1;
						}
						ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );
					})

					switch( loadingMode ){
						case 'ajax-pagination':
							$('.portfolio-pagination > ul > li a', _this).on('click', function(e){
								ajaxPaginationLinkClickEventFunction( $(this) );
							})
							$('.portfolio-pagination .page-nav .next-page', _this).on('click', function(e){
								ajaxNavigationClickEvent( $(this), 'next' );
							})
							$('.portfolio-pagination .page-nav .prev-page', _this).on('click', function(e){
								ajaxNavigationClickEvent( $(this), 'prev' );
							})
						break
						case 'more-button':
							$('.portfolio-ajax-button .load-more-button a', _this).on('click', function(e){
								ajaxMoreButtonClickEventFunction();
							})
						break
					}
					// update columnWidth on window resize
					jQuery(window).on('resize.portfolio_layout_resize', function(){
						mainResizer();
					});
				}

				// ajax filter
				function ajaxGetNewContent( slug, page, order ){
					var data = {
						action: 'get_new_items',
						value_slug: slug,
						value_pagination_page: page,
						post_per_page: postPerPage,
						loading_mode: portfolioContainer.data('loading-mode'),
						list_layout: portfolioContainer.data('list-layout'),
						order_settings: order,
						template: template,
						posts_format: postsFormat
					};

					hidePortfolioList();

					if( ajaxGetNewRequest != null && ajaxRequestSuccess){
						ajaxGetNewRequest.abort();
					}

					ajaxGetNewRequest = $.ajax({
							type: 'POST',
							url: portfolio_type_ajax.url,
							data: data,
							cache: false,
							beforeSend: function(){
								ajaxRequestSuccess = false;
								ajaxLoaderContainer.css({"display":"block"}).fadeTo(500, 1);
							},
							success: function( response ){
								ajaxRequestSuccess = true;

								$('.portfolio-pagination > ul > li a', _this).off('click');
								$('.portfolio-pagination .page-nav .next-page', _this).off('click');
								$('.portfolio-pagination .page-nav .prev-page', _this).off('click');
								$('.portfolio-ajax-button .load-more-button a', _this).off('click')
								$('.portfolio-pagination, .portfolio-ajax-button', portfolioContainer ).remove();

								beforeItemLength = 0;

								portfolioList = $('.portfolio-container .portfolio-list', _this);

								isotopeOptions = {
									itemSelector : '.portfolio-item',
									resizable: false,
									//layoutMode: ( 'masonry-layout' == listLayout ) ? 'masonry' : 'fitRows' ,
									layoutMode: 'masonry' ,
									masonry: {
										columnWidth: Math.floor( portfolioList.width() / widthLayoutChanger() )
									}
								}

								var
									elementsList = $('.portfolio-item', response )
								,	pagePagination = $('.portfolio-pagination', response )
								,	pageMoreButton = $('.portfolio-ajax-button', response )
								,	allPostsCount = $('.portfolio-list', response ).data('all-posts-count')
								;

								allPageLenght = Math.ceil( parseInt( allPostsCount ) / parseInt( postPerPage ) );

								switch( listLayout ){
									case 'masonry-layout':
									case 'grid-layout':

										$('.inner-wrap', elementsList).css({ "margin": Math.floor( itemMargin * 0.5 ) });

										$(elementsList).css({ "width": Math.floor( portfolioList.width() / widthLayoutChanger() ) });

										portfolioList.html('').isotope( isotopeOptions ).isotope( 'insert', elementsList );
										portfolioContainer.append( pagePagination );
										portfolioContainer.append( pageMoreButton );

										portfolioList.imagesLoaded( function() {
											portfolioList.isotope( isotopeOptions )
											showPortfolioList( beforeItemLength );

											jQuery(window).trigger('resize.portfolio_layout_resize');

											ajaxLoaderContainer.fadeTo(500, 0, function(){
												$(this).css({"display":"none"});
											});
										})
									break;
									case 'justified-layout':
										portfolioList.html('').append(elementsList);
										portfolioContainer.append( pagePagination );
										portfolioContainer.append( pageMoreButton );

										$(elementsList).each(function( index ){
											var
												$this = $(this)
											,	image_src = $this.data('image-src')
											,	image_width = $this.data('image-width')
											,	image_height = $this.data('image-height')
											,	image_ratio = $this.data('image-ratio')
											,	flex_value = Math.round( image_ratio*100 )
											,	new_width = Math.round( fixedHeight * image_ratio )
											;

											if( $('.justified-image', $this)[0] ){
												new_height = 'auto';
												$('.justified-image', $this).css({
													'width': '100%'
												,	'height': fixedHeight
												,	'background-image': 'url(' + image_src + ')'
												})
											}else{
												new_height = fixedHeight;
												$('.inner-wrap', $this).css({
													'background-image': 'url(' + image_src + ')'
												})
											}

											$this.css({
												'width': new_width + 'px'
											,	'height': new_height
											,	'-webkit-flex': flex_value + ' 1 ' + new_width + 'px'
											,	'-ms-flex': flex_value + ' 1 ' + new_width + 'px'
											,	'flex': flex_value + ' 1 ' + new_width + 'px'
											,	margin: Math.ceil(itemMargin*0.5) + 'px'
											});
										})

										portfolioList.imagesLoaded( function() {
											showPortfolioList( beforeItemLength );
											ajaxLoaderContainer.fadeTo(500, 0, function(){
												$(this).css({"display":"none"});
											});
										})
									break;
									case 'list-layout':
										portfolioList.html('').append(elementsList);
										portfolioContainer.append( pagePagination );
										portfolioContainer.append( pageMoreButton );
										portfolioList.imagesLoaded( function() {
											showPortfolioList( beforeItemLength );
											ajaxLoaderContainer.fadeTo(500, 0, function(){
												$(this).css({"display":"none"});
											});
										} )
									break;
								}

								switch( loadingMode ){
									case 'ajax-pagination':
										$('.portfolio-pagination > ul > li a', _this).on('click', function(e){
											ajaxPaginationLinkClickEventFunction( $(this) );
										})
										$('.portfolio-pagination .page-nav .next-page', _this).on('click', function(e){
											ajaxNavigationClickEvent( $(this), 'next' );
										});
										$('.portfolio-pagination .page-nav .prev-page', _this).on('click', function(e){
											ajaxNavigationClickEvent( $(this), 'prev' );
										})
									break
									case 'more-button':
										$('.portfolio-ajax-button .load-more-button a', _this).on('click', function(e){
											ajaxMoreButtonClickEventFunction();
										})
										//$('.portfolio-ajax-button .load-more-button', _this).removeClass('disabled');
										$('.portfolio-ajax-button .load-more-button', _this).slideDown();
									break
								}

								// ajax_success - trigger
								_this.trigger( 'ajax_success' );

								CHERRY_API.cherry_portfolio.magnific_popap_init();
							},
							dataType: 'html'
					});
				}

				// ajax get more
				function ajaxGetMore( page, slug, order ){
					var data = {
						action: 'get_more_items',
						value_pagination_page: page,
						value_slug: slug,
						post_per_page: postPerPage,
						list_layout: portfolioContainer.data('list-layout'),
						order_settings: order,
						template: template,
						posts_format: postsFormat
					};
					if( ajaxGetMoreRequest != null && ajaxRequestSuccess){
						ajaxGetMoreRequest.abort();
					}
					ajaxGetMoreRequest = $.ajax({
							type: 'POST',
							url: portfolio_type_ajax.url,
							data: data,
							cache: false,
							beforeSend: function(){
								ajaxRequestSuccess = false;
								ajaxLoaderContainer.css({"display":"block"}).fadeTo(500, 1);
							},
							success: function( response ){
								ajaxRequestSuccess = true;
								ajaxLoaderContainer.fadeTo(500, 0, function(){
									$(this).css({"display":"none"});
								});

								portfolioList = $('.portfolio-list', portfolioContainer);

								beforeItemLength = $('.portfolio-list .portfolio-item', portfolioContainer).length;

								var
									elementsList = $('.portfolio-item', response )
								,	allPostsCount = $(response).data('all-posts-count');
								;

								//var allPostsCount = $('.portfolio-list', response ).data('all-posts-count');
								allPageLenght = allPageLenght_temp;

								switch(listLayout){
									case 'masonry-layout':
									case 'grid-layout':
										var $elems = $(response);

										elementsList.css({ "width": Math.floor( $('.portfolio-list', portfolioContainer).width() / widthLayoutChanger() ) });
										$('.inner-wrap', elementsList).css({ "margin": Math.floor( itemMargin * 0.5 ) });
										portfolioList.append( elementsList ).isotope( 'appended', elementsList );
										portfolioList.imagesLoaded( function() {
											portfolioList.isotope( isotopeOptions )
											showPortfolioList( beforeItemLength );
											jQuery(window).trigger('resize.portfolio_layout_resize');
										});
									break;
									case 'justified-layout':
										portfolioList.append( elementsList );

										$(elementsList).each(function(index){
											var
												$this = $(this)
											,	image_src = $this.data('image-src')
											,	image_width = $this.data('image-width')
											,	image_height = $this.data('image-height')
											,	image_ratio = $this.data('image-ratio')
											,	flex_value = Math.round( image_ratio*100 )
											,	new_width = Math.round( fixedHeight * image_ratio )
											;

											if( $('.justified-image', $this)[0] ){
												new_height = 'auto';
												$('.justified-image', $this).css({
													'width': '100%'
												,	'height': fixedHeight
												,	'background-image': 'url(' + image_src + ')'
												})
											}else{
												new_height = fixedHeight;
												$('.inner-wrap', $this).css({
													'background-image': 'url(' + image_src + ')'
												})
											}

											$this.css({
												'width': new_width + 'px'
											,	'height': new_height
											,	'-webkit-flex': flex_value + ' 1 ' + new_width + 'px'
											,	'-ms-flex': flex_value + ' 1 ' + new_width + 'px'
											,	'flex': flex_value + ' 1 ' + new_width + 'px'
											,	margin: Math.ceil(itemMargin*0.5) + 'px'
											});

										})

										portfolioList.imagesLoaded( function() {
											showPortfolioList( beforeItemLength );
											ajaxLoaderContainer.fadeTo(500, 0, function(){
												$(this).css({"display":"none"});
											});
										} )
									break;
									case 'list-layout':
										portfolioList.append( elementsList );
										portfolioList.imagesLoaded( function() {
											showPortfolioList( beforeItemLength );
											ajaxLoaderContainer.fadeTo(500, 0, function(){
												$(this).css({"display":"none"});
											});
										} )
									break;
								}

								// ajax_success - trigger
								_this.trigger( 'ajax_success' );

								CHERRY_API.cherry_portfolio.magnific_popap_init();
							},
							dataType: 'html'
					});
				}

				function ajaxPaginationLinkClickEventFunction( clicked ){
					if( !clicked.parent().hasClass('active') ){
						$('.portfolio-pagination > .page-link > li', _this).removeClass('active');
						clicked.parent().addClass('active');
						currentPaginationPage = clicked.parent().index() + 1;
						ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );
					}
				}

				function ajaxNavigationClickEvent( clicked, direction ){
					switch(direction){
						case 'next':
							currentPaginationPage++;
							ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );
							break
						case 'prev':
							currentPaginationPage--;
							ajaxGetNewContent( currentSlug, currentPaginationPage, orderSetting );
							break
					}
				}

				function ajaxMoreButtonClickEventFunction(){
					if( currentPaginationPage < allPageLenght ){
						currentPaginationPage++;
						ajaxMoreClicked = true;

						if( currentPaginationPage == allPageLenght){
							$('.portfolio-ajax-button .load-more-button', _this).slideUp();
						}
						ajaxGetMore( currentPaginationPage, currentSlug, orderSetting );
					}
				}

				function showPortfolioList( index ){
					var counter = 1;
					$('.portfolio-item', portfolioContainer).each(function(){
						if( $(this).index() >= index){
							show_item( $(this), 100*parseInt(counter) );
							counter++;
						}
					})
					function show_item( itemList, delay ){
						var timeOutInterval = setTimeout(function(){
							itemList.removeClass('animate-cycle-show');
						}, delay );
					}
				}
				function hidePortfolioList(){
					$('.portfolio-item', portfolioContainer).each( function(){
						hide_item( $(this), 50*parseInt($(this).index()+1) );
					} )
					function hide_item(itemList, delay){
						var timeOutInterval = setTimeout(function(){
							itemList.addClass('animate-cycle-hide');
						}, delay );
					}
				}
				function mainResizer(){

					switch(listLayout){
						case 'masonry-layout':
						case 'grid-layout':

							var
								newWidth = Math.floor( $('.portfolio-list', portfolioContainer).width() / widthLayoutChanger() )
							;

							$('.portfolio-list .portfolio-item', portfolioContainer).css({ "width": newWidth });
							$('.portfolio-list', portfolioContainer).isotope({
								masonry: { columnWidth: newWidth }
							});
						break;
						case 'list-layout':
						break;
					}
				}

				function widthLayoutChanger(){
					var
						windowWidth = $(window).width()
					,	columnPerView
					,	widthLayout = 'large'
					;

					if ( windowWidth >= 1200 ) { widthLayout = 'large'; }
					if ( windowWidth <= 1199 && windowWidth >= 768 ) { widthLayout = 'medium'; }
					if ( windowWidth <= 767 && windowWidth >= 481) { widthLayout = 'small'; }
					if ( windowWidth <= 480 ) { widthLayout = 'extra-small'; }
					switch ( widthLayout ) {
						case 'large':
							columnPerView = column;
							break
						case 'medium':
							columnPerView = Math.ceil( column / 2 );
							break
						case 'small':
							columnPerView = Math.ceil( column / 4 );
							break
						case 'extra-small':
							columnPerView = 1;
							break
					}
					return columnPerView;
				}

			});
		},
		destroy    : function( ) { },
		update     : function( content ) { }
	};

	$.fn.cherryPortfolioLayoutPlugin = function( method ){
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method with name ' +  method + ' is not exist for jQuery.cherryPortfolioLayoutPlugin' );
		}
	}//end plugin
})(jQuery)