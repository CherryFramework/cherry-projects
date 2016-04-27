/**
 * Cherry_Portfolio
 */
(function($){
	"use strict";

	CHERRY_API.utilites.namespace('cherry_portfolio');
	CHERRY_API.cherry_portfolio = {
		init: function () {
			var self = this;
			if( CHERRY_API.status.document_ready ){
				self.render();
			}else{
				CHERRY_API.variable.$document.on('ready', self.render() );
			}
		},
		render: function () {
			var self = this;
			jQuery('.portfolio-wrap').cherryPortfolioLayoutPlugin();

			jQuery('.swiper-container').each(function(){
				var
					swiper
				,	slides_per_view = parseFloat( jQuery(this).data('slides-per-view') )
				,	slides_per_column = parseFloat( jQuery(this).data('slides-per-column') )
				,	space_between_slides = parseFloat( jQuery(this).data('space-between-slides') )
				,	swiper_loop = jQuery(this).data('swiper-loop')
				,	duration_speed = parseFloat( jQuery(this).data('duration-speed') )
				,	free_mode = jQuery(this).data('free-mode')
				,	grab_cursor = jQuery(this).data('grab-cursor')
				,	mouse_wheel = jQuery(this).data('mouse-wheel')
				,	swiper_effect = jQuery(this).data('swiper-effect')
				,	uniqId = jQuery(this).data('uniq-id')
				;

				switch ( swiper_effect ) {
					case 'swiper-effect-slide':
						swiper_effect = 'slide';
						break
					case 'swiper-effect-fade':
						swiper_effect = 'fade';
						slides_per_view = 1;
						slides_per_column = 1;
						break
					case 'swiper-effect-cube':
						swiper_effect = 'cube';
						break
					case 'swiper-effect-coverflow':
						swiper_effect = 'coverflow';
						break
				}

				swiper = new Swiper( $('#'+uniqId), {
						speed: duration_speed,
						slidesPerView: slides_per_view,
						spaceBetween: space_between_slides,
						grabCursor: grab_cursor,
						loop: swiper_loop,
						freeMode: free_mode,
						nextButton: '#' + uniqId + '-next',
						prevButton: '#' + uniqId + '-prev',
						pagination: '#' + uniqId + '-pagination',
						paginationClickable: true,
						mousewheelControl: mouse_wheel,
						slidesPerColumn: slides_per_column,
						effect: swiper_effect,
					}
				);
			})

			self.single_gallery_init();
			self.magnific_popap_init();
		},// end render
		magnific_popap_init: function () {
			if( $('.magnific-popup-link')[0] ){
				$('.magnific-popup-link').magnificPopup({
					type: 'image'
				,	removalDelay: 500
				,	callbacks: {
					beforeOpen: function() {
						this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
						this.st.mainClass = 'mfp-zoom-in';
					}
				}
				,	closeOnContentClick: true
				,	midClick: true
				});
			}
			if( $('.magnific-popup-zoom')[0] ){
				$('.magnific-popup-zoom').magnificPopup({
					type: 'image'
				});
			}
			if( $('.thumbnailset')[0] ){
				$('.thumbnailset').each(function() { // the containers for all your galleries
					$(this).magnificPopup({
						delegate: 'a'// the selector for gallery item
					,	type: 'image'
					,	removalDelay: 500
					,	callbacks: {
						beforeOpen: function() {
							this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
							this.st.mainClass = this.st.el.attr('data-effect');
						}
					}
					,	closeOnContentClick: true
					,	midClick: true
					,	gallery: {
							enabled: true
						}
					});
				});
			}
			if( $('.gallery-list')[0] ){
				$('.gallery-list').each(function() { // the containers for all your galleries
					$(this).magnificPopup({
						delegate: 'a'// the selector for gallery item
					,	type: 'image'
					,	removalDelay: 500
					,	callbacks: {
						beforeOpen: function() {
							this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
							this.st.mainClass = this.st.el.attr('data-effect');
						}
					}
					,	closeOnContentClick: true
					,	midClick: true
					,	gallery: {
							enabled: true
						}
					});
				});
			}
		},// end magnific_popap_init
		single_gallery_init: function () {
			var
				$masonry_list = $('.masonry-list')
			,	$grid_list = $('.grid-list')
			,	$grid_items = $('.grid-item', $grid_list)
			,	$justified_list = $('.justified-list')
			,	$justified_items = $('.justified-item', $justified_list)
			,	masonry_columns = parseInt( $masonry_list.data('columns') )
			,	masonry_gutter = parseInt( $masonry_list.data('gutter') )
			;

			if( $masonry_list[0] ){
				$masonry_list.css({
					'column-count': masonry_columns,
					'-webkit-column-count': masonry_columns,
					'-moz-column-count': masonry_columns,
					'column-gap': masonry_gutter,
					'-webkit-column-gap': masonry_gutter,
					'-moz-column-gap': masonry_gutter,
				});
			}

			if( $justified_list[0] ){
				$justified_items.each(function(){
					var
						$this = $(this)
					,	image_src = $this.data('image-src')
					,	image_width = $this.data('image-width')
					,	image_height = $this.data('image-height')
					,	image_ratio = $this.data('image-ratio')
					,	flex_value = Math.round( image_ratio * 100 )
					,	new_width = Math.round( $this.height() * image_ratio )
					;

					$this.css({
						'width': new_width + 'px'
					,	'-webkit-flex': flex_value + ' 1 ' + new_width + 'px'
					,	'-ms-flex': flex_value + ' 1 ' + new_width + 'px'
					,	'flex': flex_value + ' 1 ' + new_width + 'px'
					});
					$('.justified-image', $this).css({
						'background-image': 'url(' + image_src + ')'
					})
				})
			}
		}
	}
	CHERRY_API.cherry_portfolio.init();
}(jQuery));
