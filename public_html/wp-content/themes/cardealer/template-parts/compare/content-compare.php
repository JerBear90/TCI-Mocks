<?php
/**
 * Template part.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package CarDealer
 */

global $car_dealer_options;

$compare_showcase = $args['compare_showcase'];
$compare_ids      = $args['compare_ids'];
$compare_count    = $args['compare_count'];
$column_counts    = $args['column_counts'];
$compare_data     = $args['compare_data'];

$vehicle_mileage_unit = ( isset( $car_dealer_options['vehicle_mileage_unit'] ) && 'none' !== $car_dealer_options['vehicle_mileage_unit'] ) ? $car_dealer_options['vehicle_mileage_unit'] : '';
$compare_fields       = cdhl_compare_column_fields();
$attr_field           = cardealer_get_compare_attr_field();
$attr_terms           = cdhl_get_terms(
	array(
		'taxonomy' => $attr_field,
	)
);
$attr_obj             = get_taxonomy( $attr_field );
$attr_label           = ( $attr_obj ) ? $attr_obj->labels->singular_name : '';


$is_iframe       = cardealer_is_iframe();
$wrapper_classes = array(
	'cd-vehicle-compare-wrapper',
);

if ( $is_iframe ) {
	$wrapper_classes[] = 'cd-vehicle-compare-wrapper-iframe';
}

$table_classes[] = 'cd-vehicle-compare-table';
if ( $compare_showcase ) {
	$table_classes[] = 'cd-vehicle-compare-table-showcase';
}
if ( $is_iframe ) {
	/*
	?>
	<div class="cd-vehicle-compare-loader" style="display: none;"><?php echo esc_html__( 'Loading...', 'cardealer' ); ?></div>
	<?php
	*/
}
?>
<div class="<?php cardealer_class_generator( $wrapper_classes ); ?>">
	<div class="cd-vehicle-compare">
		<table class="<?php cardealer_class_generator( $table_classes ); ?>" data-vehicle_count="<?php echo esc_attr( $compare_count ); ?>">
			<thead>
				<?php
				$compare_field_sr = 1;
				$row_classes = array(
					'vehicle-row',
					"vehicle-row-{$compare_field_sr}"
				);

				$row_classes[] = ( 0 === $compare_field_sr % 2 ) ? 'vehicle-row-even' : 'vehicle-row-odd';
				?>
				<tr class="<?php cardealer_class_generator( $row_classes ); ?>">
					<th class="table-heading table-label">&nbsp;</th>
					<?php
					$select_vehicle_sr = 1;
					$column_counts     = ( $compare_showcase && $column_counts > 2 ) ? 2 : $column_counts;
					for ( $column = 1; $column <= $column_counts; $column++ ) {
						$table_heading_title = esc_html__( 'Select Vehicle', 'cardealer' );
						if ( isset( $compare_data[ $column-1 ] ) && ! empty( $compare_data[ $column-1 ] ) ) {
							$vehicle_data = $compare_data[ $column - 1 ];
							$vehicle_id   = $vehicle_data['vehicle_id'];
							$post_title   = $vehicle_data['post_title'];
							$post_type    = $vehicle_data['post_type'];
							$vehicle_post = $vehicle_data['post'];
						}
						$column_classes = array(
							'table-heading',
							'vehicle-column',
							"vehicle-column-{$column}",
							"vehicle-column--vehicle-{$vehicle_id}",
							'vehicle-column-' . $post_type,
						);
						if ( 'select_vehicle' === $post_type ) {
							$column_classes[] = "vehicle-column-{$post_type}-{$select_vehicle_sr}";
							$select_vehicle_sr++;
						}
						?>
						<th class="<?php cardealer_class_generator( $column_classes ); ?>">
							<?php
							if ( 'cars' === $post_type ) {
								$vehicle_link = get_permalink( $vehicle_id );
								?>
								<a href="<?php echo esc_attr( $vehicle_link ); ?>" class="compare-title-link">
								<?php
							}
							?>
							<?php echo esc_html( $post_title ); ?>
							<?php
							if ( 'cars' === $post_type ) {
								?>
								</a>
								<?php
							}
							?>
						</th>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
				$compare_field_sr = 2;
				foreach ( $compare_fields as $key => $val ) {
					if ( 'remove' === $key ) {
						continue;
					}
					$row_classes = array(
						'table-row',
						'vehicle-row',
						"vehicle-row-{$compare_field_sr}"
					);

					$row_classes[] = ( 0 === $compare_field_sr % 2 ) ? 'vehicle-row-even' : 'vehicle-row-odd';
					?>
					<tr class="<?php cardealer_class_generator( $row_classes ); ?>">
						<th class="table-label"><?php echo esc_html( $val ); ?></th>
						<?php
						$select_vehicle_sr = 1;
						$column_counts     = ( $compare_showcase && $column_counts > 2 ) ? 2 : $column_counts;

						for ( $column = 1; $column <= $column_counts; $column++ ) {
							if ( isset( $compare_data[ $column-1 ] ) && ! empty( $compare_data[ $column-1 ] ) ) {
								$vehicle_data = $compare_data[ $column - 1 ];
								$vehicle_id   = $vehicle_data['vehicle_id'];
								$post_title   = $vehicle_data['post_title'];
								$post_type    = $vehicle_data['post_type'];
								$vehicle_post = $vehicle_data['post'];

								$column_classes = array(
									'vehicle-column',
									"vehicle-column-{$column}",
									"vehicle-column--vehicle-{$vehicle_id}",
									'vehicle-column-' . $post_type,
								);
								if ( 'select_vehicle' === $post_type ) {
									$column_classes[] = "vehicle-column-{$post_type}-{$select_vehicle_sr}";
									$select_vehicle_sr++;
								}
								?>
								<td class="<?php cardealer_class_generator( $column_classes ); ?>">
									<?php
									/*
									if ( 'remove' === $key && 'select_vehicle' !== $post_type ) {
										?>
										<a href="javascript:void(0)" data-car_id="<?php echo esc_attr( $vehicle_id ); ?>" data-column_class=".vehicle-column-<?php echo esc_attr( $vehicle_id ); ?>" class="compare-remove-column"><span class="remove">x</span></a>
										<?php
									}
									*/

									if ( ! $vehicle_post || ( $vehicle_post && ! in_array( $post_type, array( 'cars', 'select_vehicle' ), true ) ) ) {
										// continue;
									}

									if ( 'remove' === $key ) {
									}elseif ( 'car_image' === $key ) {
										if ( 'select_vehicle' !== $post_type ) {
											?>
											<div class="vehicle-compare-main-content">
												<div class="vehicle-compare-main-content-image">
												<?php
												if ( 'cars' === $post_type ) {
													$vehicle_link = get_permalink( $vehicle_id );
													?>
													<a href="<?php echo esc_url( $vehicle_link ); ?>" class="compare-image-link">
														<?php
														if ( function_exists( 'cardealer_get_cars_image' ) ) {
															echo wp_kses( cardealer_get_cars_image( 'car_catalog_image', $vehicle_id ), cardealer_allowed_html( array( 'img' ) ) );
														}
														?>
													</a>
													<?php
												} else {
													echo wp_kses( cardealer_get_cars_image( 'car_catalog_image', $vehicle_id ), cardealer_allowed_html( array( 'img' ) ) );
												}
												?>
												</div>
												<?php
												if ( ! $compare_showcase ) {
													$column_class_vehicle_id = ".vehicle-column--vehicle-{$vehicle_id}";
													?>
													<div class="vehicle-compare-main-content-remove-link">
														<a href="javascript:void(0)" data-car_id="<?php echo esc_attr( $vehicle_id ); ?>" data-column_class="<?php echo esc_attr( $column_class_vehicle_id ); ?>" class="compare-remove-column"><i class="far fa-times-circle"></i></a>
													</div>
													<?php
												}
												?>
											</div>
											<?php
										} else {
											if ( $attr_obj ) {
												?>
												<div class="cd-compare-select-wrapper">
													<div class="cd-compare-select-field cd-compare-select-field-attr">
														<select class="cd-compare-select cd-compare-select-attr" data-field_attr="<?php echo esc_attr( $attr_field ); ?>" data-placeholder="<?php echo sprintf( esc_html__( 'Select %s', 'cardealer' ), $attr_label ); ?>">
															<option value=""></option>
															<?php
															if ( 'car_mileage' === $attr_obj->name ) {
																$mileage_array = cardealer_get_mileage_array();
																foreach ( $mileage_array as $mileage ) {
																	$option_label = ( $vehicle_mileage_unit ) ? "{$mileage} {$vehicle_mileage_unit}" : $mileage;
																	?>
																	<option value="<?php echo esc_attr( $mileage ); ?>">&leq; <?php echo esc_html( $option_label ); ?></option>
																	<?php
																}
															} else {
																foreach ( $attr_terms as $key_tax => $term ) {
																	?>
																	<option value="<?php echo esc_attr( $term ); ?>"><?php echo esc_html( $key_tax ); ?></option>
																	<?php
																}
															}
															?>
														</select>
														<div class="cd-compare-select-field-container cd-compare-select-field-container-attr"></div>
													</div>
													<div class="cd-compare-select-field cd-compare-select-field-vehicle">
														<select class="cd-compare-select cd-compare-select-vehicle" data-placeholder="<?php echo esc_attr__( 'Select Vehicle', 'cardealer' ); ?>">
															<option></option>
														</select>
														<div class="cd-compare-select-field-container cd-compare-select-field-container-vehicle"></div>
													</div>
												</div>
												<?php
											}
										}
									} elseif ( 'price' === $key ) {
										$price_html = cardealer_car_price_html( '', $vehicle_id, true, false );
										if ( empty( $price_html ) || 'select_vehicle' === $post_type ) {
											?>
											<div class="price car-price"><span class="new-price">&mdash;</span></div>
											<?php
										} else {
											cardealer_car_price_html( '', $vehicle_id, true, true );
										}
									} elseif ( 'features_options' === $key ) {
										if ( 'cars' === $post_type ) {
											$car_features_options = wp_get_post_terms( $vehicle_id, 'car_features_options' );
											$json                 = wp_json_encode( $car_features_options ); // Conver Obj to Array.
											$car_features_options = json_decode( $json, true ); // Conver Obj to Array.
											$name_array           = array_map(
												function ( $options ) {
													return $options['name'];
												},
												(array) $car_features_options
											); // get all name term array.
											$options              = implode( ',', $name_array );
											$options_data         = ( empty( $options ) ) ? '&nbsp;' : $options;
											$html                 = $options_data;
											echo esc_html( $html );
										} else {
											echo '&mdash;';
										}
									} else {
										$vehicle_terms = wp_get_post_terms( $vehicle_id, $key );
										if ( empty( $vehicle_terms ) ) {
											echo '&mdash;';
										} else {
											if ( 'car_mileage' === $key ) {
												echo esc_html( cardealer_get_cars_formated_mileage( $vehicle_terms[0]->name ) );
											} else {
												echo esc_html( $vehicle_terms[0]->name );
											}
										}
									}
									?>
								</td>
								<?php
							} else {
								/*
								$uid = uniqid();
								?>
								<td class="empty-vehicle" id="<?php echo esc_attr( "{$key}_{$uid}" ); ?>">
									<?php
									if ( 'car_image' === $key ) {
										?>
										<div class="col-sm-12">
											<span><?php echo sprintf( esc_html__( 'Select %s', 'cardealer' ), $attr_label ); ?></span>
											<div class="selected-box">
												<select data-uid="<?php echo esc_attr( $uid ); ?>" id="sort_<?php echo esc_attr( $field . '_' . $uid ); ?>" data-id="<?php echo esc_attr( $field ); ?>" class="selectpicker custom-compare-filters col-6 cd-select-box">
													<option value=""><?php esc_html_e( '--Select--', 'cardealer' ); ?></option>
													<?php
													foreach ( $attr_terms as $key_tax => $term ) {
														if ( 'car_mileage' === $attr_obj->name ) {
															$mileage_array = cardealer_get_mileage_array();
															if ( 1 === (int) $j ) {
																foreach ( $mileage_array as $mileage ) {
																	if ( $vehicle_mileage_unit ) {
																		?>
																		<option value="<?php echo esc_attr( $mileage ); ?>">&leq; <?php echo esc_html( $mileage . ' ' . $vehicle_mileage_unit ); ?></option>
																		<?php
																	} else {
																		?>
																		<option value="<?php echo esc_attr( $mileage ); ?>">&leq; <?php echo esc_html( $mileage ); ?></option>
																		<?php
																	}
																}
															}
															$j++;
														} else {
															?>
															<option value="<?php echo esc_attr( $term ); ?>"><?php echo esc_html( $key_tax ); ?></option>
															<?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="col-sm-12">
											<span><?php echo esc_html__( 'Select Vehicle', 'cardealer' ); ?></span>
											<div class="selected-box">
												<select  id="cars<?php echo esc_attr( '_' . $uid ); ?>" class="selectpicker select-compare-car">
													<option value=""><?php esc_html_e( '--Select--', 'cardealer' ); ?></option>
												</select>
											</div>
										</div>
										<?php
									}
									?>
								</td>
								<?php
								*/
							}
						}
						?>
					</tr>
					<?php
					$compare_field_sr++;
				}
				?>
			</tbody>
		</table>
	</div>
</div>
