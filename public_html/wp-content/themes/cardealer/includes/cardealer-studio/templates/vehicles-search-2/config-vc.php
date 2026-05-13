<?php
/**
 *Vehicles Search
 *
 * @package Cardealer
 */

return array(
	'name'              => esc_html__( 'Vehicles Search 2', 'cardealer' ),
	'template_category' => esc_html__( 'Vehicles Search', 'cardealer' ),
	'disabled'          => true, // Disable it to not show in the default tab.
	'content'           => '<<<CONTENT
<p>[vc_row full_width="stretch_row" cd_enable_responsive_settings="true" css=".vc_custom_1701343134311{padding-top: 80px !important;padding-bottom: 80px !important;background: #db2d2e url(https://cardealer.potenzaglobalsolutions.com/wp-content/uploads/2023/11/section-bg-pattern-2.png?id=11795) !important;background-position: center !important;background-repeat: no-repeat !important;background-size: cover !important;}" element_css_md=".vc_custom_1701343134311{padding-top: 80px !important;padding-bottom: 80px !important;}" element_css_sm=".vc_custom_1701343134312{padding-top: 80px !important;padding-bottom: 80px !important;}"][vc_column][cd_vehicles_search condition_tab_lables="" vehicle_filters="" filter_background="light" hide_location_input="" filter_style="box" search_style="style-2" section_title="I Want to Buy"][/vc_column][/vc_row]</p>
CONTENT',
);
