<?php

/*
* Check if ad is already in compare
*/
if( !function_exists('directorypress_is_in_compare') ){
function directorypress_is_in_compare( $id ){
	if( !empty( $_COOKIE['directorypress_compare'] ) && $_COOKIE['directorypress_compare'] != '-1' ){
		$list = json_decode( stripslashes( $_COOKIE['directorypress_compare'] ), true );
		if( !empty( $list ) && in_array( $id, $list ) ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}
}


/*
* handle compare
*/
if( !function_exists('directorypress_compare') ){
function directorypress_compare(){
	$compare = isset( $_POST['compare'] ) ? $_POST['compare'] : '';
	$id = isset( $_POST['id'] ) ? $_POST['id'] : '';
	$list = array();
	if( !empty( $_COOKIE['directorypress_compare'] ) && $_COOKIE['directorypress_compare'] != '-1' ){
		$list = json_decode( stripslashes( $_COOKIE['directorypress_compare'] ), true );
	}

	if( $compare == 'remove' ){
		$key = array_search( $id, $list );
		if ( $key !== false ) {
		    unset( $list[$key] );
		}
	}
	else if( $compare == 'add' && !in_array( $id, $list ) ){
		$list[] = $id;
	}

	setcookie( 'directorypress_compare', json_encode( $list ), current_time( 'timestamp' ) + 86400, '/' );

	//var_dump($list);
	if( !empty( $list ) ){
		
		$args = array(
			'post__in' => $list,
			'post_type' => DIRECTORYPRESS_POST_TYPE,
			'posts_per_page' => 3,
		);

		$listings = new WP_Query($args);

		$compare_fields = array();
		$compare_fields_organized = array();
		$adverts_data = array();

		if( $listings->have_posts() ){			
			while( $listings->have_posts() ){
				$listings->the_post();
				$listing = new directorypress_listing;
				$listing->directorypress_init_lpost_listing(get_post());
				if(isset($listing->logo_image) && !empty($listing->logo_image)){
					$image_src_array = wp_get_attachment_image_src($listing->logo_image, 'full');
					$image_src = $image_src_array[0];
				}elseif(isset($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url']) && !empty($DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url'])){
					$image_src_array = $DIRECTORYPRESS_ADIMN_SETTINGS['directorypress_nologo_url']['url'];
					$image_src = $image_src_array;
				}else{
					$image_src = DIRECTORYPRESS_RESOURCES_URL.'images/no-thumbnail.jpg';
				}
				$param = array(200, 200);
				ob_start();
				
				echo '<a href="'.get_permalink().'"><img alt="'.$listing->title().'" src="'. bfi_thumb($image_src, $param).'" width="200" height="200" /></a>';
				?>
				<h5>
					<a href="<?php get_permalink() ?>" class="text-overflow" title="<?php echo esc_attr( $listing->title() ) ?>" target="_blank">
						<?php echo $listing->title(); ?>
					</a>
				</h5>
				<?php
				$content = ob_get_contents();
				ob_end_clean();
				global $directorypress_object;
				$advert_fields = $listing->display_content_fields(false);
				//if( empty( $compare_fields[$advert_fields->last_category_id] ) ){
					//$advert_fields->set_cat_fields();
					//$compare_fields[$advert_fields->last_category_id] = $advert_fields->cat_fields;	
				//}
				//else{
				//	$advert_fields->set_cat_fields( $compare_fields[$advert_fields->last_category_id] );
			//	} 
				
				
				//$advert_fields->set_fields_array();
				$adverts_data[get_the_ID()] = array(
					'fields' 	=> $advert_fields,
					'content'	=> $content,
					//'price'		=> directorypress_get_advert_price()
				);
			}
			/* let's organize category fields first */
			/* foreach( $compare_fields as $cat_id => $fields ){
				foreach( $fields as $field ){
					if( $field->cf_type != 5 ){
						$compare_fields_organized[$field->cf_slug] = $field->cf_label;
					}
					else{
						$labels = explode( '|', $field->cf_label );
						foreach( $labels as $label ){
							$compare_fields_organized[sanitize_title($label)] = $label;
						}
					}
				}
			} */

			?>
			<div class="responsive-table">
				<table>
					<tr>
						<th>&nbsp;</th>
						<?php
						foreach( $adverts_data as $id => $data ){
							?>
							<td class="cad_<?php echo esc_attr( $id ); ?>">
								<?php echo $data['content'] ?>
								<a href="javascript:void(0);" class="compare-remove" data-id="<?php echo esc_attr( $id ) ?>">
									<i class="fas fa-trash-alt"></i>
								</a>
							</td>
							<?php
						}
						?>
					</tr>
					<?php foreach( $directorypress_object->fields->fields_array as $field ): ?>
						<tr>
							<th><?php echo esc_html( $field->name ) ?></th>
							<?php //foreach( $adverts_data as $id => $data ){ ?>
								<td class="cad_<?php //echo esc_attr( $id ); ?>">
									<?php
									//if( !empty( $data['fields'][$slug] ) ){
										echo $advert_fields;
									//}
									//else{
										//echo '/';
									//}
									?>
								</td>
							<?php //} ?>
						</tr>
					<?php endforeach; ?>
					<tr>
						<th><?php //esc_html_e( 'Price', 'directorypress' ) ?></th>
						<?php
						//foreach( $adverts_data as $id => $data ){
							//echo '<td class="cad_'.esc_attr( $id ).'"><div class="bottom-advert-meta">'.$data['price'].'</div></td>';
						//}
						?>
					</tr>
				</table>
			</div>
			<?php
		}
		else{
			?>
			<h5 class="text-center"><?php esc_html_e( 'There are no listing to compare', 'directorypress' ) ?></h5>
			<?php			
		}		
		wp_reset_postdata();
	}
	else{
		?>
		<h5 class="text-center"><?php esc_html_e( 'There are no listing to compare', 'directorypress' ) ?></h5>
		<?php
	}

	die();
}
add_action( 'wp_ajax_directorypress_compare', 'directorypress_compare' );
add_action( 'wp_ajax_nopriv_directorypress_compare', 'directorypress_compare' );
}
