<?php
/**
 * Client 02
 *
 * @package Cardealer
 */

return array(
	'name'              => esc_html__( 'Client 03', 'cardealer' ),
	'template_category' => esc_html__( 'Client', 'cardealer' ),
	'disabled'          => true, // Disable it to not show in the default tab.
	'config'            => array(
	'images' => array(
			'brand1' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-01.png',
			'brand2' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-02.png',
			'brand3' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-03.png',
			'brand4' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-04.png',
			'brand5' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-05.png',
			'brand6' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-06.png',
			'brand7' => 'https://sampledata.potenzaglobalsolutions.com/cardealer-modern-vc/wp-content/uploads/2023/12/clients-07.png',
		),
	),
	'content'           => '<<<CONTENT
<p>[vc_row cd_enable_responsive_settings="true" css=".vc_custom_1697703553572{margin-top: 100px !important; margin-bottom: 100px !important;}" element_css_md=".vc_custom_1697703553573{margin-top: 90px !important; margin-bottom: 90px !important;}" element_css_sm=".vc_custom_1697703553573{margin-top: 80px !important; margin-bottom: 80px !important;}"][vc_column][vc_text_separator title="Our Partners &amp; Suppoters" css=".vc_custom_1696847622627{margin-bottom: 80px !important;}" el_class="text-uppercase"][cd_ourclients list_style="with_slider" data_md_items="5" data_sm_items="3" data_xs_items="2" data_xx_items="1" arrow="false" dots="false" autoplay="true" data_loop="true" data_space="20" slider_type="with_slider" silder_type="with_silder" client_slider_opt="Autoplay,Loop" slider_images="{{brand1}},{{brand2}},{{brand3}},{{brand4}},{{brand5}},{{brand6}},{{brand7}}"][/vc_column][/vc_row]</p>
CONTENT',
);
