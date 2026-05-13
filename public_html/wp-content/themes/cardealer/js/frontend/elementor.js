/*================================================
[  Table of contents  ]
================================================
======================================
[ End table content ]
======================================*/
( function( $ ) {
	"use strict";

	// Make sure you run this code under Elementor.
	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_counter.default', function(){
			$( document.body ).trigger( 'cdhl_counter_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_video.default', function(){
			$( document.body ).trigger( 'cdhl_video_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_clients.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_image-slider.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_testimonials.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicles-conditions-tabs.default', function(){
			$( document.body ).trigger( 'cdhl_vehicles_conditions_tabs_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_feature-box-slider.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_multi-tab.default', function(){
			$( document.body ).trigger( 'cdhl_multi_tab_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicles-list.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_hotspot.default', function(){
			$( document.body ).trigger( 'cdhl_hotspot_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicles-listing.default', function(){
			$( document.body ).trigger( 'cdhl_custom_filters_event' );
			$( document.body ).trigger( 'cardealer_load_masonry_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_add-vehicle-listing.default', function(){
			$( 'select.cdfs-select2' ).each(function( index, element ) {
				var el = this,
					$el = $( this );
				$el.select2({
					tags: cardealer_el_js.cdfs_allow_add_attribute,
					dropdownCssClass: 'cdfs-select2-' + $el.data('name'),
				});
			});

			if ( $( '.cdfs_editor' ).length > 0 ) {
				$( '.cdfs_editor' ).each( function() {
					wp.editor.remove( $(this).attr( 'id' ) );
					wp.editor.initialize( $(this).attr( 'id' ), wp.editor.getDefaultSettings() );
				});
			}
			$( document.body ).trigger( 'cardealer_locationpicker_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicle-listing-filters.default', function(){
			$( document.body ).trigger( 'cdhl_custom_filters_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_share.default', function(){
			$( document.body ).trigger( 'cdhl_video_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_our-team.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_video-slider.default', function(){
			$( document.body ).trigger( 'cdhl_video_slider_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicles-search.default', function(){
			$( document.body ).trigger( 'cdhl_vehicles_search' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicles-by-type.default', function(){
			$( document.body ).trigger( 'cdhl_vehicles_conditions_tabs_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_custom-filters.default', function(){
			$( document.body ).trigger( 'cdhl_custom_filters_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicle-gallery.default', function(){
			$( document.body ).trigger( 'cdhl_vehicle_gallery_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicle-gallery-wide.default', function(){
			$( document.body ).trigger( 'cdhl_vehicle_gallery_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicle-related-vehicles.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_hero-vehicle-info.default', function(){
			$( document.body ).trigger( 'cdhl_hero_vehicle_info_event' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_vehicle-view360-inline.default', function(){
			// console.log( 'cardealer_initiate_cloudimage_360_view' );
			$( document.body ).trigger( 'cardealer_initiate_cloudimage_360_view' );
		});
		elementorFrontend.hooks.addAction( 'frontend/element_ready/cdhl_blog-posts-1.default', function(){
			$( document.body ).trigger( 'cardealer_owl_event' );
		});
	});

}( jQuery ) );
