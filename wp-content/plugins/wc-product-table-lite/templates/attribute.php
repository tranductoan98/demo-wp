<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// false attribute
if( 
	empty( $attribute_name ) ||
	(
		$attribute_name == '_custom' &&
		empty( $custom_attribute_name )
	)
){
	return;
}

// custom attribute
if( $attribute_name !== '_custom' ){
	$custom_attribute_name = false;
}

if( $custom_attribute_name ){

	$result_found = false;
	
	if( // product variation
		$product->get_type() == 'variation' &&
		$term = get_post_meta( $product->get_id(), 'attribute_' . $custom_attribute_name, true )
	){

		$result_found = true;

		?>
		<div class="wcpt-attribute wcpt-custom-attribute <?php echo $html_class; ?>">
			<div class="wcpt-attribute-term ">
				<?php echo esc_html( $term ); ?>
			</div>
		</div>
		<?php

	}	else { // other product types
		$attributes = $product->get_attributes();		

		if( $val = $product->get_attribute( trim($custom_attribute_name) ) ){

			if( empty ( $separator ) ){
				$separator = '';
			}else{
			 $separator = wcpt_parse_2( $separator );
		 }

			$custom_terms = array_map( 'trim', explode( '|', $val ) );
			$result_found = true;

			?>
			<div class="wcpt-attribute wcpt-custom-attribute <?php echo $html_class; ?>">
				<?php foreach( $custom_terms as $index=> $custom_term ): ?>
					<div class="wcpt-attribute-term"><?php echo esc_html( $custom_term ); ?></div>
					<?php	if( $index < count( $custom_terms ) - 1 ): ?>
						<div class="wcpt-attribute-term-separator wcpt-term-separator"><?php echo $separator ?></div>
					<?php	endif; ?>
				<?php endforeach; ?>
			</div>
			<?php
		}

	}

	if( ! $result_found ){
		if( empty( $empty_relabel ) ){
			$empty_relabel = '';
		}
	
		echo wcpt_parse_2($empty_relabel);			
	}

	return;
}

// product variation
if(	in_array( $product->get_type(), array( 'subscription_variation', 'variation' ) ) ){
	$field_name = 'attribute_pa_' . $attribute_name;

	// stored on custom field as variation
	if( get_post_meta( $product->get_id(), $field_name, true ) ){
		include( 'custom_field.php' );
		return;

	// possibly stored on parent		
	}else{
		$product = wc_get_product( $product->get_parent_id() );

	}

}

$taxonomy = 'pa_' . $attribute_name;
$attributes = $product->get_attributes();

if ( isset( $attributes[ $attribute_name] ) ) {
	$attribute_object = $attributes[ $attribute_name];
} elseif ( isset( $attributes[ 'pa_' . $attribute_name ] ) ) {
	$attribute_object = $attributes[ 'pa_' . $attribute_name ];
}

if( empty( $attribute_object ) ){
	$terms = false;

} else if( $attribute_object && $attribute_object->is_taxonomy() ){
	$terms = wc_get_product_terms( $product->get_id(), $attribute_object->get_name(), array( 'fields' => 'all', 'orderby' => 'menu_order' ) );

}else{ // text attribute
	$terms = $attribute_object->get_options();

}

// excludes array
$excludes_arr = array();
if( ! empty( $exclude_terms ) ){
	$excludes_arr = preg_split( '/\r\n|\r|\n/', $exclude_terms );
}

if( $terms && count( $terms ) ){
// associated terms exist

	if( empty ( $separator ) ){
 		$separator = '';
 	}else{
		$separator = wcpt_parse_2( $separator );
	}

	$output = '';

	if( empty( $relabels ) ){
		$relabels = array();
	}

	// sort terms prioritizing current fitler
	global $wcpt_table_data;
	$table_id = $wcpt_table_data['id'];
	$filter_key = $table_id . '_attr_pa_' . $attribute_name;
	if( ! empty( $_GET[ $filter_key ] ) && ! empty( $terms ) ){
		$_terms = array();
		foreach( $terms as $term ){
			$_terms[$term->term_id] = $term;
		}
		$terms = array_replace( array_intersect_key( array_flip( $_GET[ $filter_key ] ), $_terms ), $_terms );
	}

	$terms = array_values($terms);

	// relabel each term
	foreach( $terms as $index => $term ){

		// exclude
		if( in_array( $term->name, $excludes_arr ) ){
			continue;
		}

		// filtering
		if( 
			! empty( $_GET[ $filter_key ] ) &&
			is_array( $_GET[ $filter_key ] ) &&
			in_array( $term->term_taxonomy_id, $_GET[ $filter_key ] )
		){
			$filtering = 'true';
		}else{
			$filtering = 'false';
		}

		// is link
		$archive_url = get_term_link($term->term_id, $taxonomy);
		if( is_wp_error( $archive_url ) ){
			$archive_url = '';
		}

		if(
			! empty( $click_action ) &&
			$click_action == 'archive_redirect' &&
			$archive_url
		){
			$is_link = true;
		}else{
			$is_link = false;
		}

		// data attrs
		$common_data_attrs = 'data-wcpt-slug="'. $term->slug .'" data-wcpt-filtering="'. $filtering .'" data-wcpt-archive-url="'. $archive_url .'"';
		if( $is_link ){
			$common_data_attrs .= ' href="'. $archive_url .'" ';
		}		

		// look for a matching rule
		$match = false;

    foreach( $relabels as $rule ){

      if(
				wp_specialchars_decode( $term->name ) == $rule['term'] ||
				(
					function_exists('icl_object_id') &&
					! empty( $rule['ttid'] ) &&
					$term->term_taxonomy_id == icl_object_id( $rule['ttid'], 'pa_'. $attribute_name , false )
				)
			){
				$match = true;

				// style
				wcpt_parse_style_2( $rule, '!important' );
				$term_html_class = 'wcpt-' . $rule['id'];

				// append
				$label = str_replace( '[term]', $term->name, wcpt_parse_2( $rule['label'] ) );

				// wrap in a / div tag
				if( $is_link ){
					$output .= '<a class="wcpt-attribute-term ' . $term_html_class . '" '. $common_data_attrs .'>' . $label . '</a>';
				}else{
					$output .= '<div class="wcpt-attribute-term ' . $term_html_class . '" '. $common_data_attrs .'>' . $label . '</div>';
				}

				break;
      }
		}
		
		if( ! $match ){
			if( $is_link ){
				$output .= '<a class="wcpt-attribute-term" '. $common_data_attrs .'>' . $term->name . '</a>';
			}else{
				$output .= '<div class="wcpt-attribute-term" '. $common_data_attrs .'>' . $term->name . '</div>';
			}
		}

		if( $index < count( $terms ) - 1 ){
			$output .= '<div class="wcpt-attribute-term-separator wcpt-term-separator">'. $separator .'</div>';
		}

  }

}else{
// no associated terms

	if( empty( $empty_relabel ) ){
		$empty_relabel = '';
	}

	$output = wcpt_parse_2($empty_relabel);

}

if( empty( $click_action ) ){
	$click_action = false;
}

if( 
	$click_action == 'trigger_filter' &&
	! wcpt_check_if_nav_has_filter( null, 'attribute_filter', $attribute_name ) 
){
	$click_action = false;
}

if( $click_action ){
	$html_class .= ' wcpt-'. $click_action .' ';
}

if( ! empty( $separate_lines ) ){
	$html_class .= ' wcpt-terms-in-separate-lines ';
}

if( ! empty( $output ) ){
	?>
		<div
			class="wcpt-attribute <?php echo $html_class; ?>"
			data-wcpt-taxonomy="<?php echo $taxonomy; ?>"
		>
			<?php echo $output; ?>
		</div>
	<?php
}
