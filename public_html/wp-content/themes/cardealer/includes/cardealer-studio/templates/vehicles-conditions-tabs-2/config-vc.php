<?php
/**
 *Vehicles Conditions Tabs
 *
 * @package Cardealer
 */

return array(
	'name'              => esc_html__( 'Vehicles Conditions Tabs 2', 'cardealer' ),
	'template_category' => esc_html__( 'Vehicles Conditions Tabs', 'cardealer' ),
	'disabled'          => true, // Disable it to not show in the default tab.
	'content'           => '<<<CONTENT
<p>[vc_row full_width="stretch_row" el_class="bg-gradient-grey" css=".vc_custom_1697031924228{padding-top: 80px !important;padding-bottom: 80px !important;}"][vc_column][cd_vehicles_search_type vehicle_makes="buick,chevrolet" cars_body_styles="coupe,hatchback,sedan,suv" type_search_tab_lables="custom" custom_lbl_type_1="Browse Make" custom_lbl_type_2="Browse Body Style" hide_type_tab="no" search_style="style_2"][/vc_column][/vc_row]</p>
CONTENT',
);
