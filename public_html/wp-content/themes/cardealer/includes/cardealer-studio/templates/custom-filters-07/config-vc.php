<?php
/**
 * Custom Filters 05
 *
 * @package Cardealer
 */

return array(
	'name'              => esc_html__( 'Custom Filters 07', 'cardealer' ),
	'template_category' => esc_html__( 'Custom Filters', 'cardealer' ),
	'disabled'          => true, // Disable it to not show in the default tab.
	'content'           => '<<<CONTENT
<p>[vc_row][vc_column][cd_section_title heading_tag="h3" title_align="text-center" style="style_1" section_title="Vertical Layout For Filter" section_sub_title="Vertical layout for filter with fix attribute." title_font_size_lg="" title_font_size_md="" title_font_size_sm="" title_font_size_xs="" title_line_height_lg="" title_line_height_md="" title_line_height_sm="" title_line_height_xs=""][/cd_section_title][/vc_column][/vc_row][vc_row][vc_column width="1/4"][cd_vehicle_listing_filters title="Vehicle Filters"][cd_space desktop="80" tablet="70" portrait="60" mobile="50" mobile_portrait="40"][/vc_column][vc_column width="3/4"][cd_vehicle_listing hide_sold_vehicles="" posts_per_page="12"][cd_space desktop="80" tablet="70" portrait="60" mobile="50" mobile_portrait="40"][/vc_column][/vc_row]</p>
CONTENT',
);
